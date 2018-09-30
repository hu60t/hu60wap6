#!/bin/sh

for file in `ls | grep -iP '^(ok|[\x{4e00}-\x{9fa5}]{1,3})\.(png|gif|jpg|jpeg)$'`
do
	baseName=${file%%.*}
	hexName=`echo -n $baseName | xxd -ps`
	newName=$hexName.gif

	if [ -e $newName ]; then
		printf "%-15s -X     %s     (文件已存在)\n" $file $newName
	else
		printf "%-15s ->     %s\n" $file $newName
		mv $file $newName
	fi
done
