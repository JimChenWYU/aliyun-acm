<?php

namespace Aliyun\Acm;

final class HostBuilder
{
    /**
     * 获取服务器地址
     *
     * @param string $endpoint
     * @param string $port
     * @return string
     */
    public static function serverHost(string $endpoint, string $port)
    {
        return self::build($endpoint, $port, 'diamond-server/diamond');
    }

    /**
     * 获取配置
     *
     * @param string $endpoint
     * @param string $port
     * @return string
     */
    public static function getConfigHost(string $endpoint, string $port)
    {
        return self::build($endpoint, $port, 'diamond-server/config.co');
    }

    /**
     * 删除配置
     *
     * @param string $endpoint
     * @param string $port
     * @return string
     */
    public static function deleteAllDatumsHost(string $endpoint, string $port)
    {
        return self::build($endpoint, $port, 'diamond-server/datum.do?method=deleteAllDatums');
    }

    /**
     * 发布配置
     *
     * @param string $endpoint
     * @param string $port
     * @return string
     */
    public static function syncUpdateAllHost(string $endpoint, string $port)
    {
        return self::build($endpoint, $port, 'diamond-server/basestone.do?method=syncUpdateAll');
    }
    
    /**
     * 构建请求地址
     *
     * @param string $endpoint
     * @param string $port
     * @param string $path
     * @return string
     */
    private static function build(string $endpoint, string $port, string $path)
    {
        return sprintf('http://%s:%s/%s', $endpoint, $port, $path);
    }
}
