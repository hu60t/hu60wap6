<?php

/**
 * 验证码操作类
 */
class secCode
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sendToPhone($phone)
    {
        $arr = $this->user->getSafety('secCode.phone');

        if (time() - $arr['time'] < SECCODE_SMS_INTERVAL) {
            throw new secCodeException('验证码发送过快，请' . ( SECCODE_SMS_INTERVAL - (time() - $arr['time']) ) . '秒后再试');
        }

        $sms = new secCodeSms();
        $code = $sms->sendCode($phone);

        if (false !== $code) {
            $arr = ['phone' => $phone, 'code' => $code, 'time' => time(), 'errCount' => 0];
            $this->user->setSafety('secCode.phone', $arr);

            return true;
        } else {
            return false;
        }
    }

    public function checkFromPhone($phone, $code)
    {
        $arr = $this->user->getSafety('secCode.phone');

        if (empty($arr)) {
            throw new secCodeException('请重新获取验证码');
        }

		if ($arr['phone'] != $phone) {
		    throw new secCodeException('验证码与手机号不匹配');
		}

        if (time() - $arr['time'] > SECCODE_SMS_TIME) {
            throw new secCodeException('验证码已过期，请重新获取');
        }

        if ($arr['code'] == $code) {
            $this->user->setSafety('secCode.phone', null);

            return true;

        } else {
            $arr['errCount']++;

            if ($arr['errCount'] >= SECCODE_SMS_MAX_ERR) {
                $this->user->setSafety('secCode.phone', null);
                throw new secCodeException('验证码已失效，请重新获取');
            }

            $this->user->setSafety('secCode.phone', $arr);
            return false;
        }
    }
}
