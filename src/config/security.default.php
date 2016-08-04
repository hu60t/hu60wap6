<?php
/**
 * 安全性设置
 */


/**
 * 加密用户密码用的Key
 *
 * 注意：此Key仅可在网站没有任何用户时更改。否则一但更改，所有用户的密码都将失效！
 */
define('USER_PASS_KEY', 'USER_PASS_KEY');

/**
 * 是否启用短信验证码
 */
define('SECCODE_SMS_ENABLE', false);

/**
 * 短信验证码URL
 *
 * 将被替换的内容：
 *    {@phone}    手机号码
 *    {@code}    欲发送的验证码
 */
define('SECCODE_SMS_URL', 'http://hu60.cn/sms?key=hu60&phone={@phone}&code={@code}');

/**
 * 短信验证码发送成功标志
 *
 * 将其设为发送成功时URL会返回的内容
 */
define('SECCODE_SMS_SUCCESS_FLAG', 'success');

/**
 * 发送验证码的间隔（秒）
 */
define('SECCODE_SMS_INTERVAL', 30);

/**
 * 验证码有效期（秒）
 */
define('SECCODE_SMS_TIME', 300);

/**
 * 验证码允许输错次数
 */
define('SECCODE_SMS_MAX_ERR', 5);

/**
 * 短信验证码提供者信息（设为null则不显示）
 */
define('SECCODE_SMS_PROVIDER_INFO', '<p>短信验证码由虎绿林提供</p>');
