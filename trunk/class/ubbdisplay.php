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
/*img 图片*/
    'img' => array('$this','img'),
    'imgzh' => array('$this','img'),
    'thumb' => array('$this','thumb'),
);
  
/*text 纯文本*/
  public function text($data) {
    return code::html($data['value'],'<br/>');
  }
/*link 链接*/
  public function link($data) {
    global $PAGE;
    if(trim($data['title'])=='') $data['title']=$data['url'];
    if($data['type']='urlout') $data['url']='http://'.$data['url'];
    $url=$_SERVER['PHP_SELF'].'/link.url.'.$PAGE->bid.'?url='.urlencode($data['url']);
    return '<a href="'.code::html($url).'">'.code::html($data['title']).'</a>';
  }
/*img 图片*/
  public function img($data) {
    return '<img src="'.code::html($data['src']).'"'.($data['alt']!='' ? ' alt="'.code::html($data['alt']).'"' : '').'/>';
  }
/*thumb */
  public function thumb($data) {
    $src=code::html($data['src']);
    return '<a href="'.$src.'"><img src="http://s.image.wap.soso.com/img/'.floor($data['w']).'_'.floor($data['h']).'_0_0_'.$src.'" alt="点击查看大图"/></a>';
  }

}