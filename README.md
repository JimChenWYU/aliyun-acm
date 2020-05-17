<h1 align="center"> PHP SDK for aliyun ACM. </h1>

<p align="center"> 阿里云ACM PHP SDK.</p>


## Installing

```shell
$ composer require aliyun/acm -vvv
```

## Usage

```php
use Aliyun\Acm\Client;

$client = new Client('acm.aliyun.com');
$client->setAccessKey('your access key');
$client->setSecretKey('your secret key');
$client->setNameSpace('your namespace');
$client->setNameSpace('your namespace');
$client->setAppName('your app name');

/** 
 * 在进行下面操作前必须调用获取服务器IP列表 refreshAcmServerIpList()
 * @see https://help.aliyun.com/document_detail/64129.html?spm=a2c4g.11186623.6.574.bef5674fio1Bnv 
 */
$client->refreshAcmServerIpList();

/**
 * 获取配置
 * @see https://help.aliyun.com/document_detail/64131.html?spm=a2c4g.11186623.6.576.5305674fxujgIk
 * @var string $config 
 */
$config = $client->getConfig('your dataId', 'your group');
/**
 * 发布配置
 * @see https://help.aliyun.com/document_detail/69307.html?spm=a2c4g.11186623.6.578.2cf37a1c8vxBgD
 * @var bool $isPublish 
 */
$isPublish = $client->publish('your dataId', 'your group', '{"a":1}');
/** 
 * 删除配置
 * @see https://help.aliyun.com/document_detail/69308.html?spm=a2c4g.11186623.6.579.23e052b5igAgFi
 * @var bool $isRemove 
 */
$isRemove = $client->remove('your dataId', 'your group');
/** 
 * 检查配置是否更新，如果检测到没有更新会立即返回，否则会进行长轮询等待30秒
 * @see https://help.aliyun.com/document_detail/64132.html?spm=a2c4g.11186623.6.577.5b9111b6a3vgi8
 * @var bool $isModify 
 */
$isModify = $client->checkIfModify('your dataId', 'your group', '{"a":1}');
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/JimChenWYU/aliyun-acm/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/JimChenWYU/aliyun-acm/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT