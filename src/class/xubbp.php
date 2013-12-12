<?php
/**
* 可扩展UBB代码解析器
* 
* @version 0.2
* @author 老虎会游泳 <hu60.cn@gmail.com>
* @copyright 该类是 hu60t 框架的一部分，使用 LGPLv3 授权
* 
* 
* 该类是一个简单但却强大的UBB代码解析引擎，
* 它通过正则表达式和回调函数提供了可扩展的UBB解析、显示功能。
* 通过扩展，它不仅能将任意格式的UBB转换成HTML代码，
* 还可以将其转换成任何你需要的格式，比如XML、txt甚至word文档，
* 并且可以通过回调函数自行对解析好的内容进行关键字屏蔽等后期处理。
* 
* 该类的另一个优点就是速度快。
* 众所周知，使用正则表达式做的UBB解析器在有大量UBB需要处理时效率相当低下，
* 不过该类克服了这一缺点，因为它的解析是分两步进行的：
* 第一步，用正则和回调函数分析UBB并产生UBB资源数组；
* 第二步，用回调函数遍历分析UBB资源数组，产生HTML代码。
* 该类加速的秘诀是：通过 serialize() 函数处理数组，
* 你可以把第一步的结果以字符串的形式保留下来，
* 当需要显示时，用 unserialize() 还原数组并直接交给第二步处理，
* 免除了重复的正则匹配工作，速度想不快都难！
* 看到这里你可能会问：我是不是要同时保存生成的数组和原文，
* 因为UBB数组是没法交给用户去编辑修改的？或者，
* 为什么不直接保存最终产生的HTML代码呢，这样不是更快？
* 第一个问题的答案是不需要。你只需要保存UBB数组即可，因为通过扩展，
* 该类可以把UBB资源数组转换成任何形式，包括UBB代码本身，
* 写两个显示方案，一个把UBB数组转成HTML代码，另一个转成UBB代码，问题就解决了。
* 第二个问题的答案相信很多WAP论坛的开发者都遇到过：
* 怎么给经过UBB替换后的内容分页？传统的方法没有什么好的解决方案，
* 而该类通过保存解析的中间结果，在需要显示时由回调函数自动抽取所需的字数，
* 完美地解决了该问题。
* 
*
* 该类的使用方法：
* 该类本身不提供任何UBB规则，所以要使用该类，
* 必须继承它。一套UBB方案至少要继承两个类，
* 一个用来定义你的UBB解析方案，另一个用来把解析好的内容转换成html。
* 最好不要把解析和显示方案混合在一个子类里，这样看起来很混乱。
* 
* 关于回调函数：
* UBB的解析和显示都需要使用回调函数，
* 它既可以是普通PHP函数，也可以是类的方法（静态或非静态方法都可）。
* 如果你通过继承使用该类，推荐把回调函数定义在子类里，这样是最方便的。
* 举个简单的例子：
* <code>
* //UBB解析器
* class myUbbParser extends XUBBP {
*     private $parse=array('…'=>'…','…'=>'…');
* 
*     private function url($url,$title=null) {
*         …
*     }
* 
*     private function img($src,$alt=null) {
*         …
*     }
* }
* </code>
* 
* <code>
* //UBB显示器
* class myUbbDisplay extends XUBBP {
*     private $display=array('…'=>array(……),'…'=>array(……));
* 
*     private function url($data) {
*         …
*     }
* 
*     private function img($data) {
*         …
*     }
* }
* </code>
*/
class XUBBP {
/**
* 显示UBB数组时是否忽略未知的类型
* 
* 如果该属性为TRUE，遇到未知类型时会直接跳过（但仍会解析它的child，如果有的话）。
* 如果它为FALSE，则会抛出一个异常。
* 不要直接操作该属性，使用 @see self::skipUnknown() 设置该属性的值。
*/
    protected $skipUnknown=FALSE;
/**
* UBB解析器的分析规则列表
* 
* 它是一个关联数组，格式是：
* <code>array(
*  '正则查找1'=>'正则替换1',
*  '正则查找2'=>'正则替换2',
*  ……
* );</code>
* 
* 数组键值的意义和具体写法见 @see self::addParse()
*/
    protected $parse=array();
/**
* UBB显示器的回调函数列表
* 
* 它是一个关联数组，格式是：
* <code>array(
*  '类型1'=>'回调函数1',
*  '类型2'=>'回调函数2',
*  ……
* );</code>
* 
* 数组键值的意义和具体写法见 @see self::addDisplay()
*/
    protected $display=array();

 
/**
* 用于自动关闭未结束标记的回调函数
* 
* 格式：
* <code>array(
*     array('type1', 'methodName'),
*     …
* );</code>
* 
* 解析时，如果发现未正常关闭的标记，将通过回调函数获得结束标记。
* 回调函数只接受一个参数 $xubbp（即该类中的 $this）
* 解析开始标记的回调函数需要使用 $xubbp->regEndTag(…); 注册结束标记的回调函数
* 解析结束标记的回调函数需要使用 $xubbp->rmEndTag(…); 移除结束标记，防止结束标记重复。
* @see self::regEndTag(),self::rmEndTag()
*/
protected $endTags=array();

/**
* 递归解析器临时变量
*/
protected $tmp_parse_result = null;
protected $tmp_parse_param = null;
  
/**
* 对UBB文本进行解析
* 
* 该方法对含有UBB代码的文本进行解析并返回UBB资源数组，
* 或者返回该数组经 serialize() 函数处理后生成的字符串（第二个参数为true时）。
* 你无须关注该数组的具体结构，只要把它传递给 display() 方法即可获得可显示的解析结果。
* 以保存UBB资源数组来代替保存原文，可以避免每次显示时都重新分析UBB代码，能够大大加快UBB的解析速度。
* 

* @param string 要解析的文本
* @param bool 是否返回UBB数组的serialize形式
* @return mixed 第二个参数为TRUE时返回 string UBB数组的serialize形式，否则直接返回 array UBB数组
*/
    public function parse($text,$serialize=false) {
        $arr=$this->parser($text);
	    $this->tmp_parse_result = null;
		$this->tmp_parse_param = null;
        $this->rmEndTag(NULL,$arr);
         if($serialize) return serialize($arr);
         else return $arr;
    }
	
/**
* 递归解析文本
*/
    public function parser($text) {
        if($text=='') return array();
        foreach($this->parse as $k=>$v) {
		$arr=array();
		$this->tmp_parse_result = &$arr;
		$this->tmp_parse_param = $v;
            $ok=preg_replace_callback($k,array($this, 'parseExec'),$text);
            if($ok===NULL) throw new xubbpException("正则表达式 '$k' 错误，解析失败！",500);
            if($arr===NULL) throw new xubbpException("正则表达式  '$k' 的回调函数 '$v[1]' 返回值错误，应该返回二维数组！",501);
            if($ok=='') return $arr;
        }
        return $this->parseText($text);
    }
	
/**
* 使用回调函数执行递归解析
*/
    public function parseExec($argv) {
	    $arr = &$this->tmp_parse_result;
		$v = $this->tmp_parse_param;
		$func = $v[1];
		$param = $v[2];
		foreach ($param as &$tmpV) {
		    if (is_int($tmpV)) $tmpV = $argv[$tmpV];
		}
		$arr = array_merge($this->parser($argv[$v[0][0]]), call_user_func_array(array($this, $func), $param), $this->parser($argv[$v[0][1]]));
		return '';
	}
  
  
/**
* 解析成纯文本
* 
* 子类可以重载该方法以实现敏感词过滤等功能。
*/
    public function parseText($text) {
        return array(array(
            'type'=>'text',
            'value'=>$text
        ));
    }
  

/**
* 将UBB资源数组转换成可显示的文本
* 
* 该方法通过已定义的回调函数将UBB数组转换成可显示的HTML或其他格式。
* 
* @return string 转换后的HTML代码
*/
    public function display($ubbArray, $serialize=false) {
	    if ($serialize) $ubbArray = unserialize($ubbArray);
        $html='';

        foreach($ubbArray as $id=>$v) {
             $type=$v['type'];
             if(!isset($this->display[$type])) {
                 if($this->skipUnknown) continue;
                 else throw new XUBBPException('未知类型：'.$type.' 类型未被定义，无法解析。请正确定义该类型的显示方案，或者使用 $xubbp->skipUnknown(TRUE) 跳过未知类型。',404);
             }
             $v['id']=$id;
             $func=$this->display[$type];
             $html.=$this->$func($v);
        }
        return $html;
    }
  
public function setParse($value) {
    if (is_array($value)) {
        $this->parse = $value;
        return true;
    } else
        return false;
}
public function getParse() {
    return $this->parse;
}

public function setDisplay($value) {
    if (is_array($value)) {
        $this->display = $value;
        return true;
    } else
        return false;
}
public function getDisplay() {
    return $this->Display;
}

public function regEndTag($type,$func) {
array_push($this->endTags,array($type,$func));
}

/**
* 移除开始标记的回调函数注册的结束标记
* 
* @return 找到并成功移除返回 TRUE，未找到返回 FALSE。
* 如果函数返回 FALSE，表明没有对应的开始标记，
* 函数调用者应该返回 array() ，不要继续产生结束标记，
* 否则会有不配对的结束标记出现。
*/
public function rmEndTag($type,&$ubbArray) {
while($this->endTags) {
$tag=array_pop($this->endTags);
if($tag[0]!=$type) {
array_push($this->endTags,$tag);
$func=$tag[1]; $ubbArray=array_merge($ubbArray,$this->$func());
} else {
return TRUE;
}
}
return FALSE;
}
  
/**
* 注意匹配的顺序，独占内容或不允许嵌套的块（如[code]应该放在前面）
*/
}  
  
/**
* XUBBP异常处理类
*/
class XUBBPException extends Exception {
}
