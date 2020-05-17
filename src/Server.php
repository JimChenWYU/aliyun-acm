<?php

namespace Aliyun\Acm;

final class Server
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $port;
    /**
     * @var bool
     */
    private $isIpv4;

    /**
     * Server constructor.
     * @param string $url
     * @param string $port
     * @param bool   $isIpv4
     */
    public function __construct(string $url, string $port, bool $isIpv4)
    {
        $this->url = $url;
        $this->port = $port;
        $this->isIpv4 = $isIpv4;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getIsIpv4()
    {
        return $this->isIpv4;
    }

    /**
     * @param mixed $isIpv4
     */
    public function setIsIpv4($isIpv4)
    {
        $this->isIpv4 = $isIpv4;
    }
}
