#!/bin/sh
#自动备份网站和数据库
    
#网站根目录
webRoot="/home/wwwroot";
#数据库备份文件存放路径（应在网站根目录内）
sqlPath="${webRoot}/mysql.backup/all.sql";
#备份文件存放目录（应在网站根目录外）
backupDir="/home/backup";
    
#以下不需要修改
echo "Backup Mysql Database";
mysqldump -A > $sqlPath;
echo "Mysql Backup Completed";
echo "Tar webRoot";
tar jcf "$backupDir/$(date +%Y-%m-%d).tar.bz2" $webRoot;
echo "Backup Completed";