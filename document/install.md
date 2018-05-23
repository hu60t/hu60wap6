# 安装说明

1. 更名下面文件
    ```
    src/config/db.default.php => src/config/db.php
    src/config/security.default.php => src/config/security.php
    src/config/site.default.php => src/config/site.php
    src/config/system.default.php => src/config/system.php
    ```
2. 修改db.php，根据文件里面信息配置数据库。
3. 导入`src/db/mysql.sql`到数据库里面。
4. 注册一个账户，第一个注册的账户将成为管理员。
