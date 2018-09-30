<?php

/**
 * 验证码接口（Security Code Interface）
 *
 */
interface secCodeInterface
{
    /**
     * 发送验证码
     *
     * 随机产生验证码，将验证码发送给 $receiver 并返回验证码的值。
     */
    public function sendCode($receiver);
}
