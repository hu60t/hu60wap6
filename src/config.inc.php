<?php
/**
 * 全局配置文件
 *
 * @package hu60t
 * @version 0.1.0
 * @author 老虎会游泳 <hu60.cn@gmail.com>
 * @copyright 配置文件
 *
 * 本文件提供以下种服务：
 * 1. 定义框架的常用路径（*_DIR 系列常量）。
 * 2. 如果 $_SERVER['REQUEST_TIME_FLOAT'] 不存在则在第一时间定义它，供程序运行计时器使用。
 * 3. 设置PHP的错误报告、最大运行时间、允许占用的最大内存等。
 * 4. 配置自动加载类。具体加载规则见以下函数的注释：
 * @see autoload_file()
 * 5. 关闭魔术引号并去除$_GET、$_POST、$_COOKIE等被加的斜扛
 * 6. 载入其他配置文件
 *
 */

/*常用路径*/

/**
 * 框架根目录
 *
 * 全局配置文件(config.inc.php)所在目录的绝对路径，也是hu60t框架的根目录。
 * 框架中所有可由用户浏览器直接访问到的资源都应存放在此。
 *
 * 路径的结尾不包含斜杠。
 * 默认值是config.inc.php文件的绝对路径。
 *
 * 可以用该常量定位需要加载的文件，
 * 但是如果它所在的目录已经被其他常量定义，
 * 则不要使用该常量。
 * 比如，以下情况应该避免：
 * <code>require_once ROOT_DIR.'/func/test.php'</code>
 * 因为func目录已被常量FUNC_DIR定义，所以应改为：
 * <code>require_once FUNC_DIR.'/test.php';</code>
 * 之所以这样要求，是因为有时为了安全，
 * 开发者可能会把func等目录移动到其他位置（比如Web不能访问到的位置），
 * 如果坚持使用FUNC_DIR而不是ROOT_DIR.'/func'，
 * 那他们只要修改下FUNC_DIR的值就搞定了。
 */
define('ROOT_DIR', defined('__DIR__') ? __DIR__ : dirname(__FILE__));

/**
 * 类文件存放目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 ROOT_DIR.'/class'
 *
 * 项目使用的类应该按自动加载类的命名规则存放在该目录，
 * 命名规则见：
 * @see autoload_file()
 * 符合自动加载命名规则的类文件在该类第一次被使用时会自动载入，
 * 所以开发者无需手动载入任何类文件。
 */
define('CLASS_DIR', ROOT_DIR . '/class');

/**
 * 函数库存放目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 ROOT_DIR.'/func'
 *
 * 该目录存放项目使用的函数或函数库文件。
 * 命名规则没有限制，但是建议文件名使用小写，
 * 并且使用 test.php 这样的直接命名，
 * 不要用 test.func.php 之类的复杂命名，
 * 因为在加载函数库时我们使用
 * <code>require_once FUNC_DIR.'/test.php';</code>
 * FUNC_DIR已经指明加载的是函数了，不必再重复地用*.func.php指明。
 */
define('FUNC_DIR', ROOT_DIR . '/func');

/**
 * 配置文件目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 ROOT_DIR.'/config'
 *
 * 该常量是配置文件的存放目录。
 * 配置文件的命名规则建议参考FUNC_DIR的注释：
 * @see FUNC_DIR
 */
define('CONFIG_DIR', ROOT_DIR . '/config');

/**
 * 页面和默认模板目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 ROOT_DIR.'/page'
 *
 * 该目录保存hu60t的页面和默认模板。
 * 页面的概念见：
 * @see PAGE::load()
 * 每个页面只有一套默认模板，但可以有很多套可选模板，
 * 可选模板则放在 TPL_DIR常量定义的目录里。
 *
 * hu60t之所以让默认模板和页面放在一起，而可选模板放在另一边，
 * 是因为默认模板在某种意义上来说不是给网站用户看的，而是给其他开发者看的。
 * 虽然一个页面可以只有可选模板而没有默认模板，
 * 但开发者在写一个页面的时最好实现它的默认模板。
 * 默认模板可以很简单，很难看，但要能用，而且最好有良好的注释，
 * 因为它是告诉第三方模板开发者怎么写模板的最好方式。
 */
define('PAGE_DIR', ROOT_DIR . '/page');

/**
 * 可选模板目录
 */
define('TPL_DIR', ROOT_DIR . '/tpl');


/**
 * 临时文件存放目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 ROOT_DIR.'/temp'
 *
 * 该目录存放临时文件，比如Smarty模板的编译结果等。
 * 开发者可以在里面存放任何临时文件，
 * 但是应该自己负责回收工作。
 * hu60t没有临时文件自动清理机制。
 *
 * 为了防止命名冲突，最好建立一个子目录存放你的临时文件，
 * 不要把文件直接放在 TEMP_DIR 根目录里。
 */
define('TEMP_DIR', ROOT_DIR . '/temp');

/**
 * 过程存放目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 ROOT_DIR.'/sub'
 *
 * 该目录存放hu60t或开发者编写的“过程”。
 * 所谓“过程”就是一段PHP代码片段，
 * 它不定义函数和类，仅仅用已定义的东西完成特定的任务。
 * 当过程被载入时，它将在当前变量作用域内运行，
 * 这个特性可能带来方便，也可能带来麻烦，看开发者怎么用了。
 *
 * 过程可以用在那种在很多地方都要用，但写成函数或类又不值得的事
 * 函数require_once后还要调用一次，麻烦。类倒是能自动载入，但有时只有五六行代码也没必要写成类。
 *
 * 还有就是，过程可以做配置文件的补充。
 * 通常的配置文件(放在CONFIG_DIR目录的)是允许网站管理者修改的，
 * 所以它们往往只是简单的定义常量，没有代码逻辑。
 * 而过程则可以包含代码逻辑，得到更灵活的配置，
 * 而这些配置，通常只有开发者才能修改。
 *
 * 给开发者的建议是少用过程，把过程都改成静态类吧，
 * 这样不仅功能扩展方便，还能自动加载，调用非常方便。
 */

define('SUB_DIR', ROOT_DIR . '/sub');

/**
 * SMARTY插件目录
 *
 * 路径的结尾不包含斜杠。
 * 默认值为 CLASS_DIR.'/smarty'
 *
 * 该常量是Smarty模板引擎要求的，它定义的Smarty插件的存放目录。
 * 详情见Smarty手册。
 */
define('SMARTY_DIR', CLASS_DIR . '/smarty/');

/*
* 设置程序开始运行的时间
* 该步骤对PHP5.2有用，因为它没有 $_SERVER['REQUEST_TIME_FLOAT'] 变量
*/
//PHP5.4+不再需要
/*if (!isset($_SERVER['REQUEST_TIME_FLOAT']))
    $_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
if (!isset($_SERVER['REQUEST_TIME']))
    $_SERVER['REQUEST_TIME'] = time();*/


/*php运行时配置*/

//错误提示等级，E_ALL全部开启、0关闭
@ error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

//在页面上显示错误，true开启、false关闭
@ ini_set('display_errors', true);

//设置程序最大内存占用
@ ini_set('memory_limit', '64M');

//设置脚本超时时间是60秒
@ set_time_limit(60);

//设置用户断开连接后脚本不自动停止。
@ ignore_user_abort(true);


/*载入自动加载类的函数*/
require_once FUNC_DIR . '/autoload.php';

/*注册自动加载类的函数*/
spl_autoload_register('autoload_file');

/*处理GET、POST、COOKIE等被加上的反斜杠*/
//PHP5.4+不再需要
//require_once SUB_DIR . '/strip_quotes_gpc.php';


/*载入其他配置文件*/
require_once CONFIG_DIR . '/system.php';
require_once CONFIG_DIR . '/db.php';
require_once CONFIG_DIR . '/security.php';
