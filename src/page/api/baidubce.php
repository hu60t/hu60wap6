<?php
/**
 * Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on
 * an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 */
require_once ROOT_DIR . '/nonfree/class/BaiduBce.phar';

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\Util\DateUtils;

class SignatureBuilder {
    public function simple() {

        if (!($this->hasQuery('httpMethod')
            && $this->hasQuery('path')
            && $this->hasQuery('queries')
            && $this->hasQuery('headers'))) {
            return array('statusCode' => 403);
        }

        $httpMethod = $this->getQuery('httpMethod');
        if (strcmp($httpMethod, 'DELETE') === 0) {
            return array('statusCode' => 403);
        }

        $path = $this->getQuery('path');
        $queries = json_decode($this->getQuery('queries'), TRUE);
        $headers = json_decode($this->getQuery('headers'), TRUE);

        //禁止分片
        if (isset($queries['uploadId']) || isset($queries['uploads'])) {
            return array('statusCode' => 403);
        }

        //禁止过大请求和空文件上传
        if (!isset($headers['Content-Length']) || $headers['Content-Length'] < 1 ||
            $headers['Content-Length'] > BAIDUBCE_BOS_MAX_FILESIZE) {
            return array('statusCode' => 403);
        }

        //禁止非法Host
        if ($headers['Host'] !== BAIDUBCE_BOS_HOST) {
            return array('statusCode' => 403);
        }

        $patten = '#^(/v\d+)?/'.preg_quote(BAIDUBCE_BOS_BUCKET, '#').
            '/file/(hash|uuid)/[a-z0-9_-]{1,10}/[a-z0-9_.-]+\.[a-z0-9_-]{1,10}$#s';

        if (!preg_match($patten, $path)) {
            return array('statusCode' => 403);
        }

        $credentials = array(
            'ak' => BAIDUBCE_AK,
            'sk' => BAIDUBCE_SK
        );
        $signer = new BceV1Signer();
        $authorization = $signer->sign($credentials, $httpMethod, $path, $headers, $queries);
        $timestamp = new \DateTime();
        $timestamp->setTimezone(DateUtils::$UTC_TIMEZONE);
        $xbceDate = DateUtils::formatAlternateIso8601Date($timestamp);
        return array(
            'statusCode' => 200,
            'signature' => $authorization,
            'xbceDate' => $xbceDate
        );
    }
    public function sts() {
        return array(
            'AccessKeyId' => '',
            'SecretAccessKey' => '',
            'SessionToken' => '',
            'Expiration' => ''
        );
    }
    public function policy() {
        $policy = base64_encode($this->getQuery('policy'));
        $signature = hash_hmac('sha256', $policy, BAIDUBCE_SK);
        return array(
            'accessKey' => BAIDUBCE_AK,
            'policy' => $policy,
            'signature' => $signature
        );
    }
    private function getQuery($key) {
        return $_GET[$key];
    }
    private function hasQuery($key) {
        return isset($_GET[$key]);
    }
    public function getResponse() {
        if ($this->hasQuery('sts')) {
            return $this->sts();
        }
        else if ($this->hasQuery('policy')) {
            return $this->policy();
        }
        else {
            return $this->simple();
        }
    }
}
function Main() {
    $builder = new SignatureBuilder();
    $result = $builder->getResponse();
    header('Content-Type: text/javascript; charset=utf-8');
    //header('Access-Control-Allow-Origin: *');
    if (isset($_GET['callback'])) {
        echo sprintf("%s(%s);", $_GET['callback'], json_encode($result));
    }
    else {
        echo json_encode($result);
    }
}
Main();

/* vim: set ts=4 sw=4 sts=4 tw=120: */