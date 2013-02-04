<?php
class ubbDisplay extends XUBBP {
/*注册显示回调函数*/
protected $display=array(
/*text 纯文本*/
    'text' => array('$this','text'),
/*link 链接*/
    'url' => array('$this','link'),
    'urlzh' => array('$this','link'),
    'urlout' => array('$this','link'),
);
  
/*text 纯文本*/
  public function text($data) {
    return code::html($data['value']);
  }
/*link 链接*/
  public function link($data) {
    global $PAGE;
    if(trim($data['title'])=='') $data['title']=$data['url'];
    if($data['type']='urlout') $data['url']='http://'.$data['url'];
    $url=$_SERVER['PHP_SELF'].'/link.url.'.$PAGE->bid.'?url='.urlencode($data['url']);
    return '<a href="'.code::html($url).'">'.code::html($data['title']).'</a>';
  }

}