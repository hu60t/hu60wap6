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
