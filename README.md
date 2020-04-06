hu60wap6
========

hu60wap6 是一个社区系统

由PHP编写

界面简洁，十分简洁，特别简洁，就像非智能手机时代的移动端网页

嗯，这个风格确实的从非智能手机时代继承过来的

hu60wap6 的主要功能有：论坛、聊天室、内信、@Ta、文件图片上传、发言中插入音频视频

其他功能有：编程常用的编码解码器、HTTP请求查看器（可看浏览器UA）

它使用GPLv3协议发布

源代码获取说明
-----------------
本项目包含两个子模块，所以建议采用git来克隆项目及其所有子模块，命令如下：
```
git clone --recursive https://github.com/hu60t/hu60wap6.git
```

如果你已经克隆了项目，但不包含子模块，可以通过以下命令获取：
```
git submodule init
git submodule update
```

如果你是下载的源代码压缩包，就需要手动获取子模块并放到对应位置，请参考这个文件中的`url`和`path`：
[.gitmodules](.gitmodules)

安装说明
-----------------

1. 把 `src` 文件夹里的全部文件放在网站根目录。

2. 如果你使用Linux，可以用如下命令取代下面两步：
```bash
# 先cd到网站根目录
php script/init.php
```
如果你使用Windows，上面的命令可能不能用，请按照下面两步的说明手动复制文件。

2. 进入网站根目录，把`config.inc.default.php`复制一份，改名为`config.inc.php`。然后再把 `config` 文件夹里的所有 `xxx.default.php` 都复制一份，改名成 xxx.php，去掉 `.default`。如果你准备用git进行版本控制，建议采用复制而不是删除原文件或者直接给原文件重命名。

   也就是说，把`config.inc.default.php`复制为`config.inc.php`，再把 `config/xxx.default.php` 复制为 `config/xxx.php`。

3. 进入 `config/tpl` 文件夹，把 `site_info.default.conf` 复制一份，改名为 `site_info.conf`。

   也就是说，把 `config/tpl/site_info.default.conf` 复制为 `config/tpl/site_info.conf`。

3. 修改 `config/db.php` ，填写好 mysql 信息。

4. 导入 `db/mysql.sql` 到数据库。

5. 访问。

5. 在Windows中，你可能会遇到这样的错误：
   ```
   Syntax error in config file 'conf:site.info' on line 1 '../../../../config/tpl/site_info.conf'
   ```
   这是因为你所使用的Windows版解压缩软件或者git工具不支持符号连接，所以就把链接的源位置做为文本内容保存在了目标位置。
   要解决该问题，你需要把多个文件从源位置复制到目标位置，分别是：
   * `src/config/tpl/site_info.conf` -> `src/tpl/classic/html/site/info.conf`
   * `src/config/tpl/site_info.conf` -> `src/tpl/jhin/html/site/info.conf`
   
   此外还需要提醒你的是：不要将你的这些更改提交到git版本库，因为我们希望保留符号连接，而不是多个相同文件的复制品。
   如果你想要避免这些麻烦，建议在WSL（Windows Subsystem of Linux，适用于Linux的Windows子系统）中运行`git clone`来获得源代码，WSL中的git可以正确创建符号连接。此外，你也可以在WSL中运行web服务器。
   新版本的`Git for Windows`如果启用相关选项，也可以创建符号连接，但似乎只适用于符号连接的源文件存在的情况下。在clone本项目时，符号连接的源文件并不存在，所以`Git for Windows`似乎也会创建内容为源位置的文本文件做为替代。

6. uid 为 1 的用户会成为系统的管理员用户，可以访问后台（虽然后台只有添加版块这一个功能，修改版块的功能是崩溃的。）

7. 要让附件上传功能生效，不仅需要正确设置七牛云AK/SK，修改html中的域名，还需要正确安装本项目的`nonfree`子模块，参考“源代码获取说明”一节。


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

### 新手难度
1. 下载源代码，按照上面的说明进行安装。网站根目录是源代码目录中的`src`文件夹，所以下面说的文件夹路径均省略`src`。
2. 访问网站并切换到`经典主题`。因为`经典主题`比较容易修改。
2. 进入 `tpl/classic/html/index`文件夹（`经典主题`的文件夹），里面的`index.tpl`就是首页的内容。按照你的想法改变其html代码，但是在不了解的情况下，不要动任何位于花括号（`{}`）里面的内容，这里面的内容是smarty模板标记。
3. 进入 `tpl/classic/css` 文件夹，里面的 `default.css` 就是默认css，随你修改。`night.css`是对应的夜间模式css。
4. `tpl/classic/img` 文件夹里面是图片，比如 `hulvlin2.gif` 是默认logo。里面的`face`文件夹放的是表情包。表情包的文件名命名规则详见 [这里](https://github.com/hu60t/hu60wap6/blob/master/src/tpl/classic/img/face/README.md)。
5. `tpl/classic/html/bbs` 里面是论坛各种页面的模板。基本上打开文件你就知道是哪个页面了。随意修改。
6. `tpl/classic/html/user` 是用户中心，`tpl/classic/html/addin` 里面有聊天室的模板，等等。
7. 如果你想要修改网页的头部和尾部，那么在`tpl/classic/html/comm`里面，分别是`head.tpl`和`foot.tpl`。

### 专家难度
1. 下载源代码，按照上面的说明进行安装。网站根目录是源代码目录中的`src`文件夹，所以下面说的文件夹路径均省略`src`。
2. 进入 `tpl` 文件夹，把 `classic` 或者 `jhin` 文件夹复制一份。复制哪个取决于你想拿哪个做为基础主题进行改造。 `classic` 较为简单，但是使用的是较老的模板技术。`jhin` 更复杂，并且使用了模块化（`block`）取代 `classic` 的 `include`。
3. 接下来，打开 `sub/reg_page_bid.php`，在最后一行添加 `page::regTpl('你的主题文件夹名称');`
4. 访问 `http://你的域名/q.php/link.tpl.你的主题文件夹名称.html` 切换到你的主题。
5. 开始修改你的主题吧。`tpl/主题名称`下的目录结构还是比较简单的。`html`文件夹里面是页面的`smarty`模板，放在和`url`中第一部分同名的目录中。比如`http://你的域名/q.php/bbs.topic.xxx.html`对应的PHP文件是`page/bbs/topic.php`，它里面加载了`tpl:topic`这个模板，对应的就是`tpl/主题名称/html/bbs/topic.tpl`。
6. smarty的模板分隔符是默认的`{}`。
7. 引用模板（`{include file="这里"}`）填写的模板名称格式是这样的：`tpl:模板文件名`或`tpl:目录名.模板文件名`，如`tpl:topic`或者`tpl:bbs.topic`，带`目录名.`的主要是访问与当前文件不在同一目录的模板。同样的，主题下的配置文件（`*.conf`）也可以用类似的名称引用：`conf:配置文件名`或者`conf:目录名.配置文件名`。
8. `tpl/主题名/html/comm`里面的是公共模板，可以放被各个页面引用的模板比如header、footer等。
9. 模板中有一些可用的全局变量，比如：
   * `$CID` class id，也就是url的第一部分，比如`http://你的域名/q.php/bbs.topic.xxx.html`中的`bbs`
   * `$PID` page id，也就是url中的第二部分，比如`http://你的域名/q.php/bbs.topic.xxx.html`中的`topic`
   * `$BID` breed id，也就是url中的最后一部分，相当于扩展名，比如`http://你的域名/q.php/bbs.topic.xxx.html`中的`html`
   * `$USER` 当前用户对象，`class/user.php`下`User`类的实例。注意当前用户可能未登录（`$USER->islogin == false`）从而某些属性拿不到。
   * `$PAGE` 当前页面对象，`class/page.php`下`Page`类的实例。`$PAGE`对象可以用来获取当前页面URL中的各种其他细节，也可以用来获取一些静态资源的绝对路径（比如`{$PAGE->getTplUrl("img/hulvlin2.gif")}`，获取`tpl/主题名称/img/hulvlin2.gif`这个文件的绝对URL）。
10. 模板中可以直接调用PHP函数，比如`{date('Y-m-d')}`或者静态类方法`{str::ago(time()-30)}`。
11. 如果要输出由用户编写的内容，记得调用`|code`修饰器来进行`htmlspecialchars()`操作，比如：`{$topic.content|code}`。

