<?php
class ubbDisplay extends XUBBP {
/*注册显示回调函数*/
protected $display=array(
/*text 纯文本*/
    'text' => 'text',
/*newline 换行*/
    'newline' => 'newline',
/*code 代码高亮*/
/*link 链接*/
    'url' => 'link',
    'urlzh' => 'link',
    'urlout' => 'link',
/*img 图片*/
    'img' => 'img',
    'imgzh' => 'img',
    'thumb' => 'thumb',
);
  
/*text 纯文本*/
  public function text($data) {
    return code::html($data['value'],'<br/>');
  }
  
/*代码高亮*/
  public function code() {
      
  }

/*link 链接*/
  public function link($data) {
    global $PAGE;
	if (is_array($data['title'])) {
	    $data['title'] = $this->display($data['title']);
	} else {
	    if(trim($data['title'])=='') $data['title']=$data['url'];
		$data['title'] = code::html($data['title']);
	}
    if($data['type']='urlout') $data['url']='http://'.$data['url'];
    $url=$_SERVER['PHP_SELF'].'/link.url.'.$PAGE->bid.'?url64='.code::b64e($data['url']);
    return '<a href="'.code::html($url).'">'.$data['title'].'</a>';
  }
/*img 图片*/
  public function img($data) {
    $url=$_SERVER['PHP_SELF'].'/link.img.'.$PAGE->bid.'?url64='.code::b64e($url);
    return '<img src="'.code::html($url).'"'.($data['alt']!='' ? ' alt="'.code::html($data['alt']).'"' : '').'/>';
  }
/*thumb 缩略图*/
  public function thumb($data) {
    $src=code::html($data['src']);
    return '<a href="'.$src.'"><img src="http://s.image.wap.soso.com/img/'.floor($data['w']).'_'.floor($data['h']).'_0_0_'.$src.'" alt="点击查看大图"/></a>';
  }
  
/*newline 换行*/
  public function newline($data) {
      return '<br/>';
  }

}