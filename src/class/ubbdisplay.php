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
/*code 代码高亮*/
    'code' => 'code',
/*copyright 版权声明*/
    'copyright' => 'copyright',
/*battlenet 战网*/
    'battlenet' => 'battlenet',
);
  
/*text 纯文本*/
  public function text($data) {
    return code::html($data['value'],'<br/>');
  }
  
/*代码高亮*/
  public function code($data) {
      global $PAGE;
      if ($PAGE->bid == 'wml') {
          return code::html($data['data'], '<br/>');
      }
      
      $geshi = new geshi($data['data'], $data['lang']);
      $geshi->set_header_type(GESHI_HEADER_DIV);
      $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS); 
      return $geshi->parse_code();
  }
  
/*time 时间*/
  public function time($data) {
      return code::html(date($data['tag']));
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
  
/*copyright 版权声明*/
  public function copyright($data) {
      $x=strtolower($data['tag']);

      if(substr($x,0,3)=='cc-') {
        $en='by';
        $cn='署名';
        if(strpos($x,'-nc')) {
            $en.='-nc';
            $cn.='-非商业性使用';
        }
        if(strpos($x,'-nd')) {
            $en.='-nd';
            $cn.='-禁止演绎';
        }elseif(strpos($x,'-sa')) {
            $en.='-sa';
            $cn.='-相同方式共享';
        }
        return '<a rel="license" href="http://creativecommons.org/licenses/'.$en.'/3.0/cn/"><img alt="知识共享许可协议|Creative Commons" style="border-width:0" src="http://i.creativecommons.org/l/'.$en.'/3.0/cn/88x31.png" /></a><br/>本作品采用<a rel="license" href="http://creativecommons.org/licenses/'.$en.'/3.0/cn/">知识共享'.$cn.'3.0许可协议</a>进行许可。';
      }

    if($x=='gfdl') {
        return '本作品采用<a rel="license" href="http://baike.baidu.com/view/20722.htm">GNU自由文档许可证</a>进行许可。';
    }
    if($x=='公有领域' or $x=='公共领域') {
        return '本作品属于<a rel="license" href="http://baike.baidu.com/view/556002.htm">公有领域</a>。';
    }
    return '本作品采用'.code::html($name).'进行许可。';
  }
  
/*battlenet 战网*/
  public function battlenet($data) {
      if ($data['server'] != '') {
          return '<a href="http://www.battlenet.com.cn/wow/zh/character/'.urlencode($data['server']).'/'.urlencode($data['name']).'">'.code::html("{$data['name']}@{$data['server']}").'</a>';
      } else {
          return '<a href="http://www.battlenet.com.cn/wow/zh/search?q='.urlencode($data['name']).'&amp;f=wowcharacter">'.code::html($data['name']).'</a>';
      }
  }
  
/*newline 换行*/
  public function newline($data) {
      return '<br/>';
  }

/*layout 布局开始*/
  public function layoutStart($data) {
      
  }
}
















