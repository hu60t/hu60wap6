<?php
/**
 * 安全性设置
 */

///////////////////// 用户密码加密 /////////////////////

/**
 * 加密用户密码用的Key
 *
 * 注意：此Key仅可在网站没有任何用户时更改。否则一但更改，所有用户的密码都将失效！
 */
define('USER_PASS_KEY', 'USER_PASS_KEY');


///////////////////// 内容审核 /////////////////////

/**
 * 是否启用头像审核
 */
define('AVATAR_NEED_REVIEW', true);


///////////////////// 用户激活短信验证 /////////////////////

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
 * 短信验证码请求的方式
 *
 * 可选值：GET、POST等HTTP方法，区分大小写。
 */
define('SECCODE_SMS_METHOD', 'GET');

/**
 * 短信验证码POST数据，若使用GET方式发送，则无需关心
 *
 * 可使用的变量：
 *    {@phone}    手机号码
 *    {@code}    欲发送的验证码
 */
define('SECCODE_SMS_POST_DATA', 'key=hu60&phone={@phone}&code={@code}');

/**
 * 短信验证码POST数据的MIME类型，若使用GET方式发送，则无需关心
 */
define('SECCODE_SMS_POST_MIME', 'application/x-www-form-urlencoded');

/**
 * 短信验证码发送成功标志
 *
 * 将其设为发送成功时URL会返回的内容（或内容中的一部分）
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


///////////////////// 第三方微信推送服务 /////////////////////

/**
 * WxPusher微信推送服务的APP_TOKEN
 */
define('WXPUSHER_APP_TOKEN', 'AT_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');


///////////////////// 内容安全（机审）配置 /////////////////////

/**
 * 内容安全（机审）服务商
 * 
 * 可选值:
 * ContentSecurity::SERVICE_NONE       关闭机审
 * ContentSecurity::SERVICE_ALIYUN     阿里云内容安全
 */
define('CONTENT_SECURITY_SERVICE', ContentSecurity::SERVICE_NONE);

/**
 * 内容安全的 AK 和 SK
 */
define('CONTENT_SECURITY_AK', '');
define('CONTENT_SECURITY_SK', '');

/**
 * 阿里云内容安全接入地区
 * https://help.aliyun.com/document_detail/107743.htm
 */
define('ALIYUN_GREEN_REGION', 'cn-shenzhen');

/**
 * 阿里云内容安全业务场景
 */
// 用户名（昵称）
define('ALIYUN_GREEN_BIZ_TYPE_NAME', 'name');
// 个性签名
define('ALIYUN_GREEN_BIZ_TYPE_SIGNATURE', 'signature');
// 主题帖
define('ALIYUN_GREEN_BIZ_TYPE_TOPIC', 'topic');
// 回帖
define('ALIYUN_GREEN_BIZ_TYPE_REPLY', 'reply');
// 聊天室
define('ALIYUN_GREEN_BIZ_TYPE_CHAT', 'chat');

/**
 * 机审超时时间
 */
define('CONTENT_SECURITY_TIMEOUT', 3);


///////////////////// 上传附件到云存储 /////////////////////

/**
 * 云存储服务商
 * 
 * 可选值:
 * CloudStorage::SERVICE_BAIDU      百度BOS云存储
 * CloudStorage::SERVICE_QINIU      七牛云对象存储
 * CloudStorage::SERVICE_ALIYUN     阿里云OSS
 */
define('CLOUD_STORAGE_SERVICE', CloudStorage::SERVICE_ALIYUN);

/**
 * 云存储的 AK 和 SK
 */
define('CLOUD_STORAGE_AK', '');
define('CLOUD_STORAGE_SK', '');

/**
 * 云存储上传文件最大限制（单位：字节，用于服务器端签名）
 */
define('CLOUD_STORAGE_MAX_FILESIZE', 10485760); // 10MB

/**
 * 云存储上传文件的Bucket
 */
define('CLOUD_STORAGE_BUCKET', 'hu60');

/**
 * 云存储服务器上传文件的Endpoint（七牛云不用填）
 */
define('CLOUD_STORAGE_ENDPOINT', 'http://oss-cn-beijing.aliyuncs.com');

/**
 * 云存储客户端（浏览器）直传文件的Endpoint（七牛云不用填）
 */
define('CLOUD_STORAGE_CLIENT_ENDPOINT', 'http://hu60.oss-cn-beijing.aliyuncs.com');

/**
 * 云存储下载文件的HOST
 */
define('CLOUD_STORAGE_DOWNLOAD_HOST', 'file.hu60.cn');

/**
 * 下载资源时使用HTTPS
 */
define('CLOUD_STORAGE_USE_HTTPS', false);

/**
 * 保存被和谐文件的备份文件夹
 * 
 * 版主和谐图片、视频时，原始文件会被移到这里。
 * 请务必修改路径，加上复杂随机内容，防止用户猜到。
 * 此外还要对路径保密，否则用户自己修改路径后还是可以访问到。
 */
define('CLOUD_STORAGE_BLOCK_DIR', 'file/block/backup');

/**
 * 用于和谐图片、视频的模板文件
 * 
 * 版主和谐图片、视频时，将使用这些资源进行替换。
 */
$CLOUD_STORAGE_BLOCK_TEMPLATE = [
    'image' => 'file/block/template/block.jpg',
    'video' => 'file/block/template/block.mp4',
];


/////////////////// 防止CC攻击 ///////////////////

/**
 * 是否启用防CC模块
 */
$ENABLE_CC_BLOCKING = false;

/**
 * 使用memcache
 */
$CC_USE_MEMCACHE = false;

/**
 * CC行为记录文件
 *
 * 请指定到tmpfs文件系统内，否则性能会很差。
 * 如系统为Windows，请使用memcache，这样就不需要指定该文件。
 */
$CC_DATA = '/dev/shm/hu60-cc.dat';

/**
 * CC行为日志
 * 
 * 设为null禁用
 */
$CC_BLOCK_LOG = '/tmp/hu60-cc.log';

/**
 * 正常访问日志
 *
 * 设为null禁用
 */
$CC_ACCESS_LOG = null;

/**
 * CC判定范围
 */
$CC_LIMIT = [
    10,  // n秒内
    50, // 最多访问n次
];

/**
 * 特定IP判定范围
 */
$CC_IP_LIMIT = [
    // 设置特定IP n秒最多能访问的次数
    '127.0.0.1' => 100,
];

/**
 * 真实IP
 * 
 * 开头加#禁用，删除#启用
 */

// 无代理时使用
$CC_REAL_IP = $_SERVER['REMOTE_ADDR'];

// 采用CloudFlare或百度云加速代理时使用
#$CC_REAL_IP = $_SERVER['HTTP_CF_CONNECTING_IP'];


