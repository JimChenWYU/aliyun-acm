<?php

namespace Aliyun\Acm;

final class Validator
{
    const VALID_STR = array('_','-', '.', ':');

    public static function isIpv4($ipAddress){
        return is_numeric(ip2long($ipAddress));
    }

    public static function checkDataId($dataId){
        if(!is_string($dataId)){
            throw new InvalidArgumentException('Invalid dataId input.');
        }
    }

    public static function checkGroup($group){
        if(!is_string($group) || empty($group)){
            return 'DEFAULT_GROUP';
        }

        return $group;
    }

    public static function checkSecretKey(string $secretKey)
    {
        if (empty($secretKey)) {
            throw new InvalidArgumentException("Invalid secret key info.");
        }
    }

    public static function checkAccessKey(string $accessKey)
    {
        if (empty($accessKey)) {
            throw new InvalidArgumentException("Invalid access key info.");
        }
    }
}
