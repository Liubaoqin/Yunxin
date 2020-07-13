#  网易云信 SDK
####因为没有所有api的封装包，所以在[cccdl/yunxin_sdk](https://github.com/cccdl/yunxin_sdk) 进一步封装，加上一些功能

## 安装
> 运行环境要求PHP7.2+
> 依赖 guzzlehttp/guzzle "~6.0" 没有则会自动安装
```shell
$ composer require abao/man-yunxin-pack
```
### 接口对应文件

| 文件                       | 方法                 |  说明    |
| :-----------------------  | --------------         |  :----    |
| Friend.php        | `add()`       | 加好友，两人保持好友关系 |
| User.php        | `create()`       | 创建网易云通信ID |
| Msg.php        | `sendMsg()`       | 发送普通消息 |

### 快速使用
在您开始之前，您需要注册网易云信并获取您的[凭证](https://dev.yunxin.163.com)。

## 网易云信文档中心 [文档](https://dev.yunxin.163.com/)


```php
<?php

use Abao\Api\User;

 $user = new User('appKey', 'appSecrt');
 $ret = $user->create('92551152231s212221eee1',[
            'name' => ' 我是创奇',
            'sign' => 'dadafsdfasdfsdf12312312323123sdsaaaaaa32233333333333333333333333333333333333333332dsfdsf',
            'email' => '1160643896@qq.com',
            'birth' => '19940530',
            'gender' => '1',
            'mobile' => '18576750530',
 ]);
 var_dump($ret);
