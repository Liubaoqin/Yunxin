<?php

namespace Abao\Traits;

use Abao\Exception\YunXinException;
use GuzzleHttp\Client;

trait Request
{
    protected static $timeout = 3;

    public function post($url, $param)
    {
        $time = time();

        $client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => self::$timeout,
            'headers' => [
                'AppKey' => $this->AppKey,
                'Nonce' => $this->Nonce,
                'CurTime' => $time,
                'CheckSum' => sha1($this->AppSecret . $this->Nonce . $time),
            ]
        ]);

        $response = $client->post($url, ['form_params' => $param]);

        if ($response->getStatusCode() != 200) {
            throw new YunXinException('请求失败: ' . $response->getStatusCode());
        }

        $arr = json_decode($response->getBody(), true);

        if (!isset($arr['code']) || $arr['code'] != 200) {
            throw new YunXinException('请求结果异常' . $response->getBody());
        }

        return $arr;
    }
}
