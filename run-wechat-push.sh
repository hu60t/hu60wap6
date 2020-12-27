#!/bin/bash
cd "$(realpath "$0")"
while true; do php src/service/wechat-push.php; sleep 5; done
