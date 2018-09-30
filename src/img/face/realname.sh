#!/bin/sh

for file in `ls | grep -P '^[a-z0-9]+\.gif$'`
do
	baseName=${file%%.*}
	realName=`echo 0 $baseName | xxd -r`
	printf "%-25s ->   %s\n" $file $realName
done