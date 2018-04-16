hu60wap6
========

hu60wap6 是一个社区系统

由PHP编写

界面简洁，十分简洁，特别简洁，就像非智能手机时代的移动端网页

嗯，这个风格确实的从非智能手机时代继承过来的

hu60wap6 的主要功能有：论坛、聊天室、内信、@Ta、文件图片上传、发言中插入音频视频

其他功能有：编程常用的编码解码器、HTTP请求查看器（可看浏览器UA）

它使用GPLv3协议发布


安装说明
-----------------

1. 把 `src` 文件夹里的全部文件放在网站根目录。
2. 进入网站根目录，把 `config` 文件夹里的所有 `xxx.default.php` 都复制一份，改名成 xxx.php，去掉 `.default`。如果你准备用git进行版本控制，建议采用复制而不是删除原文件或者直接给原文件重命名。
3. 进入 `src/tpl/classic/html/site` 文件夹，把 `friend_links_data.default.tpl` 复制一份，改名为 `friend_links_data.tpl`。
3. 修改 `config/db.php` ，填写好 mysql 信息。
4. 导入 `db/mysql.sql` 到数据库。
5. 访问。
6. uid 为 1 的用户会成为系统的管理员用户，可以访问后台（虽然后台只有添加版块这一个功能，修改版块的功能是崩溃的。）


支持的PHP版本
-----------------

PHP 5.4 或以上。

PHP 7 或以上。


不支持的PHP版本
-----------------

PHP 5.2，PHP 5.3。

如果你使用的PHP版本小于5.4，你将会遇到如下错误：

```
Parse error: syntax error, unexpected '[' in /web/class/page.php on line 34
```

如何开发新主题
--------------
1. 下载源代码，按照上面的说明进行安装。
2. 进入 `src/tpl` 文件夹，把 `classic` 或者 `jhin` 文件夹复制一份。复制哪个取决于你想拿哪个做为基础主题进行改造。 `classic` 较为简单，但是使用的是较老的模板技术。`jhin` 更复杂，并且使用了模块化（`block`）取代 `classic` 的 `include`。
3. 接下来，打开 `src/sub/reg_page_bid.php`，在最后一行添加 `page::regTpl('你的主题文件夹名称');`
4. 访问 `http://你的域名/q.php/link.tpl.你的主题文件夹名称.html` 切换到你的主题。
5. 开始修改你的主题吧。`src/tpl/主题名称`下的目录结构还是比较简单的。`html`文件夹里面是页面的`smarty`模板，放在和`url`中第一部分同名的目录中。比如`http://你的域名/q.php/bbs.topic.xxx.html`对应的PHP文件是`src/page/bbs/topic.php`，它里面加载了`tpl:topic`这个模板，对应的就是`src/tpl/主题名称/html/bbs/topic.tpl`。
6. smarty的模板分隔符是默认的`{}`。
7. 引用模板（`{include file="这里"}`）填写的模板名称格式是这样的：`tpl:模板文件名`或`tpl:目录名.模板文件名`，如`tpl:topic`或者`tpl:bbs.topic`，带`目录名.`的主要是访问与当前文件不在同一目录的模板。同样的，主题下的配置文件（`*.conf`）也可以用类似的名称引用：`conf:配置文件名`或者`conf:目录名.配置文件名`。
8. `src/tpl/主题名/html/comm`里面的是公共模板，可以放被各个页面引用的模板比如header、footer等。
9. 模板中有一些可用的全局变量，比如：
   * `$CID` class id，也就是url的第一部分，比如`http://你的域名/q.php/bbs.topic.xxx.html`中的`bbs`
   * `$PID` page id，也就是url中的第二部分，比如`http://你的域名/q.php/bbs.topic.xxx.html`中的`topic`
   * `$BID` breed id，也就是url中的最后一部分，相当于扩展名，比如`http://你的域名/q.php/bbs.topic.xxx.html`中的`html`
   * `$USER` 当前用户对象，`class/user.php`下`User`类的实例。注意当前用户可能未登录（`$USER->islogin == false`）从而某些属性拿不到。
   * `$PAGE` 当前页面对象，`class/page.php`下`Page`类的实例。`$PAGE`对象可以用来获取当前页面URL中的各种其他细节，也可以用来获取一些静态资源的绝对路径（比如`{$PAGE->getTplUrl("img/hulvlin2.gif")}`，获取`/src/tpl/主题名称/img/hulvlin2.gif`这个文件的绝对URL）。
10. 模板中可以直接调用PHP函数，比如`{date('Y-m-d')}`或者静态类方法`{str::ago(time()-30)}`。
11. 如果要输出由用户编写的内容，记得调用`|code`修饰器来进行`htmlspecialchars()`操作，比如：`{$topic.content|code}`。
