<?php
class ubbDisplay extends XUBBP {
/*注册显示回调函数*/
protected $display=array(
/*text 纯文本*/
    'text' => 'text',
/*newline 换行*/
    'newline' => 'newline',
    'tab' => 'tab',
    'empty' => 'emptyTag',
/*link 链接*/
    'url' => 'link',
    'urlzh' => 'link',
    'urlout' => 'link',
    'urlname' => 'urlname',
/*img 图片*/
    'img' => 'img',
    'imgzh' => 'img',
    'thumb' => 'thumb',
/*code 代码高亮*/
    'code' => 'code',
/*time 时间标记*/
    'time' => 'time',
/*copyright 版权声明*/
    'copyright' => 'copyright',
/*battlenet 战网*/
    'battlenet' => 'battlenet',
/*layout 布局*/
    'layout' => 'layout',
/*style 风格*/
    'style' => 'style',
/*urltxt 网址文本*/
    'urltxt' => 'urltxt',
/*mailtxt 邮箱文本*/
    'mailtxt' => 'mailtxt',
/*at消息*/
    'at' => 'at',
	'atMsg' => 'atMsg',
/*face 表情*/
    'face' => 'face',
);
  
/*text 纯文本*/
  public function text($data) {
	  $text = $data['value'];
	  
	  $text = preg_replace('/[\x{0e00}-\x{0e7f}]{10,}/u', '(为防止版面长草，过长的泰文被过滤)', $text);
	  
      return code::html($text, '<br/>');
  }
  
/*代码高亮*/
  public function code($data) {
      global $PAGE;
      if ($PAGE->bid == 'wml') {
          return code::html($data['data'], '<br/>');
      }

      $codes = preg_replace(['/^[\r\n]+/s', '/[\r\n]+$/s'], '', $data['data']);

      $geshi = new geshi($codes, $data['lang']);
      $geshi->set_header_type(GESHI_HEADER_DIV);
      $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS); 
      return $geshi->parse_code();
  }
  
/*time 时间*/
  public function time($data) {
      if ($data['tag'] == '') {
          $data['tag'] = 'Y-m-d H:i:s';
      }
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
    if($data['type']=='urlout') $data['url']='http://'.$data['url'];
    $url=$_SERVER['PHP_SELF'].'/link.url.'.$PAGE->bid.'?url64='.code::b64e($data['url']);
    return '<a href="'.code::html($url).'">'.$data['title'].'</a>';
  }
  public function urlname($data) {
    return '<a name="'.code::html($data['url']).'">'.code::html($data['title']).'</a>';
  }
/*img 图片*/
  public function img($data) {
      global $PAGE;

if (preg_match('#^data:image/#is', $data['src'])) {
    $url = $data['src'];
} else {
    $url=$_SERVER['PHP_SELF'].'/link.img.'.$PAGE->bid.'?url64='.code::b64e($data['src']);
}

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
          if ($data['display']==null) {
              $data['display']="{$data['name']}@{$data['server']}";
          }

          return '<a href="http://www.battlenet.com.cn/wow/zh/character/'.urlencode($data['server']).'/'.urlencode($data['name']).'/simple">'.code::html($data['display']).'</a>';
      } else {
          if ($data['display']==null) {
              $data['display']="{$data['name']}";
          }

          return '<a href="http://www.battlenet.com.cn/wow/zh/search?q='.urlencode($data['name']).'&amp;f=wowcharacter">'.code::html($data['display']).'</a>';
      }
  }
  
/*newline 换行*/
  public function newline($data) {
      if (in_array($data['tag'], ['hr', '＜＜＜'])) {
        return '<br/>--------<br/>';
      } else {
        return '<br/>';
      }
  }

  /*tab 4em空格*/
  public function tab($data) {
    return '　　';
  }

  /*empty UBB转义*/
  public function emptyTag($data) {
    return '';
  }

/*layout 布局*/
  public function layout($data) {
      if ($data['tag'][0] != '/') {
          $dataEnd = $data;
          $dataEnd['tag'] = '/'.$data['tag'];
          $this->regEndTag('/'.$data['tag'], 'layout', $dataEnd);
          switch ($data['tag']) {
          case 'b':
              return '<span style="font-weight:bold">';
          case 'i':
              return '<span style="font-style:italic">';
          case 'u':
              return '<span style="text-decoration:underline">';
          case 'center':
          case 'left':
          case 'right':
              return '<span style="text-align:'.$data['tag'].'">';
          default:
              return '<span>';
          }
      } else {
          $html = '';
          if ($this->rmEndTag($data['tag'], $html)) {
              return $html.'</span>';
          } else {
              return '';
          }
      }
  }
  
/*style 风格*/
  public function style($data) {
      if ($data['tag'][0] != '/') {
          $dataEnd = $data;
          $dataEnd['tag'] = '/'.$data['tag'];
          $this->regEndTag('/'.$data['tag'], 'style', $dataEnd);
          switch ($data['tag']) {
          case 'color':
              return '<span style="color:'.code::html($data['opt'],false,true).'">';
          case 'div':
              return '<div style="'.code::html($data['opt'],false,true).'">';
          case 'span':
              return '<span style="'.code::html($data['opt'],false,true).'">';
          }
      } else {
          $html = '';
          if ($this->rmEndTag($data['tag'], $html)) {
              switch ($data['tag']) {
              case '/color':
                  $html .= '</span>';
                  break;
              case '/div':
                  $html .= '</div>';
                  break;
              case '/span':
                  $html .= '</span>';
                  break;
              }
              return $html;
          } else {
              return '';
          }
      }
  }
  
/*urltxt 网址文本*/
  public function urltxt($data) {
    global $PAGE;

    $url=$_SERVER['PHP_SELF'].'/link.url.'.$PAGE->bid.'?url64='.code::b64e($data['url']);
      return '<a href="'.code::html($url).'">'.code::html($data['url']).'</a>';
  }

/*mailtxt 邮箱文本*/
  public function mailtxt($data) {
      return '<a href="mailto:'.code::html($data['mail']).'">'.code::html($data['mail']).'</a>';
  }
  
/*at用户名*/
  public function at($data) {
      global $PAGE;
	  
	  $jsFunc = $this->getOpt('at.jsFunc');
	  $uinfo = new UserInfo();
	  $uinfo->uid($data['uid']);
	  $name = $uinfo->name===null ? $data['tag'] : $uinfo->name;
	  
	  if ($jsFunc) {
		  return '<a href="#" onclick="'.$jsFunc.'(\''.$name.'\');return false">@</a><a href="user.info.'.code::html($data['uid']).'.'.$PAGE->bid.'">'.code::html($name).'</a>';
	  } else {
		  return '<a href="user.info.'.code::html($data['uid']).'.'.$PAGE->bid.'">@'.code::html($name).'</a>';
	  }
  }
  
/*at通知信息*/
  public function atMsg($data) {
	  global $PAGE;
	  
	  $url = code::html($data['url']);
	  $pos = code::html($data['pos']);
	  $msg = code::html($data['msg']);
	  $uinfo = new UserInfo();
	  $uinfo->uid($data['uid']);

return <<<HTML
<a href="user.info.{$uinfo->uid}.{$PAGE->bid}">{$uinfo->name}</a> 在 <a href="{$url}">{$pos}</a> at你：
<blockquote>
{$msg}
</blockquote>
HTML;
  }
  
/*face 表情*/
  public function face($data) {
      return $data['face'].'(表情图未就绪)';
  }
}
















