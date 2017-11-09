<?php
/**
 * Created by PhpStorm.
 * User: yee
 * Date: 17-11-9
 * Time: 下午7:36
 */

//由于smarty模板限制，一些查询只能有PHP实现，所以写这个类库，做兼容

class jhinfunc{
    static function forum($id = 0,$depth = 2){
        if($depth < 0)
            return [];
        $bbs = new bbs();
        $res = [];
        foreach($bbs->childForumMeta($id) as $forum){
            $res[] = [
                "id"=>$forum["id"],
                "name"=>$forum["name"],
                "child"=>jhinfunc::forum($forum['id'],--$depth)
            ];
        }
        return $res;
    }
    static function randTopic($user){
        return;
        $bbs = new bbs($user);
        for($i = 0; $i < 100; $i ++){
            $bbs->newTopic(rand(1,7),"随机帖子_".rand(100000000,900000000),"腾讯对于第三方修改版QQ客户端的打击一直很严厉，不知道大家是否还记得当年满城风雨的珊瑚虫事件？如今，对于PC QQ的修改已经基本销声匿迹，或者说没什么必要，但是在手机端，活跃着不少修改版、纯净版、美化版。他们要么通过Xposed模块修改或者移除QQ的原本功能，要么替换原版手机QQ中的图片起到美化作用，能够实现正常QQ里没有的功能或样式，但他们一直都游离在灰色地带，腾讯的打击也是不遗余力，主要是禁止登陆。");
        }
    }
}