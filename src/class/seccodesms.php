<?php

/**
 * 短信验证码
 *
 */
class secCodeSms implements secCodeInterface
{
    //发送验证码到手机
    public function sendCode($phone)
    {
		if (function_exists('random_int')) {
			// 密码学安全随机数发生器
			$code = random_int(100000, 999999);
		} else {
			// 非安全随机数发生器，结果可能被预测到
			$code = mt_rand(100000, 999999);
		}

        $url = str_replace('{@phone}', urlencode($phone), SECCODE_SMS_URL);
        $url = str_replace('{@code}', urlencode($code), $url);
		
		// context 选项
		$opts = [
			'method'  => 'GET',
			'timeout' => 15,
		];
		
		if (SECCODE_SMS_METHOD != 'GET') {
			$data = str_replace('{@phone}', urlencode($phone), SECCODE_SMS_POST_DATA);
			$data = str_replace('{@code}', urlencode($code), $data);
			
			$opts['method'] = SECCODE_SMS_METHOD;
			$opts['header'] = 'Content-Type: '.SECCODE_SMS_POST_MIME;
			$opts['content'] = $data;
		}

		$context = stream_context_create([ 'http' => $opts ]);
        $result = file_get_contents($url, false, $context);

        if (false !== strpos($result, SECCODE_SMS_SUCCESS_FLAG)) {
            return $code;
        } else {
            return false;
        }
    }
}
