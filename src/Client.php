<?php

namespace Aliyun\Acm;

use GuzzleHttp\Exception\BadResponseException;
use JimChen\Utils\Collection;
use JimChen\Utils\Traits\HasHttpRequest;

final class Client
{
    use HasHttpRequest;

    /**
     * @var string
     */
    const DEFAULT_PORT = '8080';

    /**
     * @var string
     */
    protected $accessKey;
    /**
     * @var string
     */
    protected $secretKey;
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var string
     */
    protected $port;
    /**
     * @var string
     */
    protected $nameSpace;
    /**
     * @var string
     */
    protected $appName;
    /**
     * @var Collection
     */
    protected $serverList;

    /**
     * Client constructor.
     * @param string $endpoint
     * @param string $port
     */
    public function __construct(string $endpoint, string $port = self::DEFAULT_PORT)
    {
        $this->endpoint = $endpoint;
        $this->port = $port;
        $this->serverList = Collection::make();
    }

    /**
     * @param mixed $accessKey
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;
    }

    /**
     * @param mixed $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @param mixed $nameSpace
     */
    public function setNameSpace($nameSpace)
    {
        $this->nameSpace = $nameSpace;
    }

    /**
     * @param mixed $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * 服务地址列表
     *
     * @return Server[]
     */
    public function getServerList()
    {
        return $this->serverList->all();
    }

    /**
     * 随机获得服务地址
     *
     * @return Server
     */
    public function getRandomServer()
    {
        if ($this->serverList === null || $this->serverList->isEmpty()) {
            throw new InvalidArgumentException('Not found alive server.');
        }
        return $this->serverList->random(1);
    }

    /**
     * 更新获取 acm 服务地址
     */
    public function refreshAcmServerIpList()
    {
        $this->serverList = Collection::make();
        $serverRawList = $this->getAcmServerIp();
        if (is_string($serverRawList)) {
            $serverArray = array_filter(explode("\n", $serverRawList));
            foreach ($serverArray as $value) {
                $value = trim($value);
                $singleServerList = explode(':', $value);
                if (count($singleServerList) === 1) {
                    $singleServer = new Server($value, self::DEFAULT_PORT, Validator::isIpv4($value));
                } else {
                    $singleServer = new Server($singleServerList[0], $singleServerList[1], Validator::isIpv4($value));
                }
                $this->serverList->put($singleServer->getUrl(), $singleServer);
            }
        }
    }

    /**
     * 获取配置
     *
     * @param $dataId
     * @param $group
     * @return string
     * @throws InvalidResponseException
     */
    public function getConfig($dataId, $group, $decode = false)
    {
        Validator::checkAccessKey($this->accessKey);
        Validator::checkSecretKey($this->secretKey);
        Validator::checkDataId($dataId);
        $group = Validator::checkGroup($group);
        $singleServer = $this->getRandomServer();
        $acmHost = HostBuilder::getConfigHost($singleServer->getUrl(), $singleServer->getPort());
        $query = [
            'dataId' => $dataId,
            'group'  => $group,
            'tenant' => $this->nameSpace,
        ];
        $headers = $this->getCommonHeaders($group);

        try {
            return $this->get($acmHost, $query, $headers);
        } catch (BadResponseException $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        }
    }

    /**
     * 检测配置是否更新
     *
     * @param        $dataId
     * @param        $group
     * @param string $content
     * @return bool
     * @throws InvalidResponseException
     */
    public function checkIfModify($dataId, $group, string $content)
    {
        Validator::checkAccessKey($this->accessKey);
        Validator::checkSecretKey($this->secretKey);
        Validator::checkDataId($dataId);
        $group = Validator::checkGroup($group);

        $singleServer = $this->getRandomServer();

        $acmHost = HostBuilder::getConfigHost($singleServer->getUrl(), $singleServer->getPort());
        $contentMd5 = md5($content);
        $probeModifyRequest = "$dataId%02$group%02$contentMd5%02{$this->nameSpace}%01";
        $body = "Probe-Modify-Request=$probeModifyRequest";
        $headers = $this->getCommonHeaders($group);
        $headers['longPullingTimeout'] = '30000';

        try {
            $rawData = $this->request('POST', $acmHost, [
                'timeout' => 35,
                'headers' => $headers,
                'body'    => $body,
            ]);
            return !empty($rawData);
        } catch (BadResponseException $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        }
    }
    
    /**
     * 更新远端配置
     *
     * @param $dataId
     * @param $group
     * @param $content
     * @return bool
     * @throws InvalidResponseException
     */
    public function publish($dataId, $group, $content)
    {
        Validator::checkAccessKey($this->accessKey);
        Validator::checkSecretKey($this->secretKey);
        Validator::checkDataId($dataId);
        $group = Validator::checkGroup($group);

        $singleServer = $this->getRandomServer();

        $acmHost = HostBuilder::syncUpdateAllHost($singleServer->getUrl(), $singleServer->getPort());
        $formData = [
            'dataId'  => $dataId,
            'group'   => $group,
            'tenant'  => $this->nameSpace,
            'content' => $content,
        ];
        if (is_string($this->appName)) {
            $formData['appName'] = $this->appName;
        }
        $headers = $this->getCommonHeaders($group);
        try {
            $rawData = $this->post($acmHost, $formData, $headers);
            return $rawData['code'] === 200;
        } catch (BadResponseException $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        }
    }

    /**
     * 移除配置
     *
     * @param $dataId
     * @param $group
     * @return bool
     * @throws InvalidResponseException
     */
    public function remove($dataId, $group)
    {
        Validator::checkAccessKey($this->accessKey);
        Validator::checkSecretKey($this->secretKey);
        Validator::checkDataId($dataId);
        $group = Validator::checkGroup($group);

        $singleServer = $this->getRandomServer();

        $acmHost = HostBuilder::deleteAllDatumsHost($singleServer->getUrl(), $singleServer->getPort());
        $formData = [
            'dataId' => $dataId,
            'group'  => $group,
            'tenant' => $this->nameSpace
        ];
        $headers = $this->getCommonHeaders($group);
        try {
            $rawData = $this->post($acmHost, $formData, $headers);
            return $rawData['code'] === 200;
        } catch (BadResponseException $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        }
    }

    /**
     * 获取公共请求头
     *
     * @param $group
     * @return array
     */
    private function getCommonHeaders($group)
    {
        $headers = array();
//        $headers['Diamond-Client-AppName'] = 'ACM-SDK-PHP';
        $headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
//        $headers['exConfigInfo'] =  'true';
        $headers['Spas-AccessKey'] = $this->accessKey;

        $ts = (int)(microtime(true) * 1000);
        $headers['timeStamp'] = $ts;

        $signStr = $this->nameSpace.'+';
        if (is_string($group)) {
            $signStr .= $group . '+';
        }
        $signStr = $signStr.$ts;
        $headers['Spas-Signature'] = base64_encode(hash_hmac('sha1', $signStr, $this->secretKey, true));
        return $headers;
    }

    /**
     * 获取服务器 IP 列表
     *
     * @return array
     * @throws InvalidResponseException
     */
    private function getAcmServerIp()
    {
        if (empty($this->endpoint)) {
            throw new InvalidArgumentException(__CLASS__ .'::endpoint can not be null.');
        }
        try {
            return $this->get(HostBuilder::serverHost($this->endpoint, $this->port));
        } catch (BadResponseException $e) {
            throw new InvalidResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        }
    }

    /**
     * @return array
     */
    protected function getBaseOptions()
    {
        return [
            'timeout' => 5.0,
//            'debug'   => true,
        ];
    }
}
