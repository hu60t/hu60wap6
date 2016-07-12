<?php

/**
 * 可扩展UBB代码解析器
 *
 * @version 0.2
 * @author 老虎会游泳 <hu60.cn@gmail.com>
 * @copyright 该类是 hu60t 框架的一部分，使用 LGPLv3 授权
 *
 *
 * 【特点】
 *
 * 该类是一个可扩展的UBB代码解析引擎，
 * 它通过正则表达式和回调函数提供了UBB解析、显示功能。
 * 通过继承该类实现相应的回调函数，它不仅能将任意格式的UBB转换成HTML代码，
 * 还可以将其转换成任何你需要的格式，比如XML、txt甚至word文档，
 * 并且可以通过回调函数自行对解析好的内容进行关键字屏蔽等后期处理。
 *
 * 该类的另一个特点是速度较快。
 * 众所周知，使用正则表达式做的UBB解析器在有大量UBB需要处理时效率较低，
 * 而该类克服了这一缺点，因为它的解析是分两步进行的：
 * 第一步，用正则和回调函数分析UBB并产生UBB资源数组；
 * 第二步，用回调函数遍历分析UBB资源数组，产生HTML代码。
 * 该类加速的秘诀是：通过 serialize() 函数处理数组，
 * 可以把第一步的结果以字符串的形式保留下来，
 * 当需要显示时，用 unserialize() 还原数组并直接交给第二步处理，
 * 免除了重复的正则匹配工作。
 *
 *
 * 【FAQ】
 *
 * - 我是不是要同时保存生成的数组和原文，
 * 因为UBB数组是没法交给用户去编辑修改的？
 *     - 答：不需要。你只需要保存UBB数组即可，因为通过扩展，
 * 该类可以把UBB资源数组转换成任何形式，包括UBB代码本身，
 * 写两个显示方案，一个把UBB数组转成HTML代码，另一个转成UBB代码，问题就解决了。
 *
 * - 为什么不直接保存最终产生的HTML代码呢，这样不是更快？
 *     - 答：相信很多WAP论坛的开发者都遇到过怎么给经过UBB替换后的内容分页的问题。
 * 传统的方法没有什么好的解决方案，而该类通过保存解析的中间结果，在需要显示时由
 * 解析器自动抽取所要显示的部分，较好地解决了该问题。
 *
 *
 * 【使用方法】
 *
 * 该类本身不提供任何UBB规则，所以要使用该类，
 * 必须继承它。一套UBB方案至少要继承两个类，
 * 一个用来定义你的UBB解析方案，另一个用来把解析好的内容转换成html。
 * 最好不要把解析和显示方案混合在一个子类里，这样看起来很混乱。
 *
 * 关于回调函数：
 * UBB的解析和显示都需要使用回调函数，回调函数是子类的非静态方法。
 * 举个简单的例子：
 * <code>
 * //UBB解析器
 * class myUbbParser extends XUBBP {
 *     private $parse=array(
 *         //url标签 链接
 *         '!^(.*)\[url=(.*?)\](.*?)\[/url\](.*)$!is'
 *             => array(array(1,4), 'url', array(2,3)),
 *         '!^(.*)\[url\](.*?)\[/url\](.*)$!is'
 *             => array(array(1,3), 'url', array(2)),
 *         //img标签 图片
 *         '!^(.*)\[img=(.*?)\](.*?)\[/img\](.*)$!is'
 *             => array(array(1,4), 'img', array(2,3)),
 *         '!^(.*)\[img\](.*?)\[/img\](.*)$!is'
 *             => array(array(1,3), 'img', array(2)),
 *     );
 *
 *     private function url($url,$title='') {
 *         return array(array(
 *             'type' => 'url',
 *             'url' => $url,
 *             'title' => $title,
 *             'len' => $this->len($url)+$this->len($title)
 *         ));
 *     }
 *
 *     private function img($src,$alt='') {
 *         return array(array(
 *             'type' => 'url',
 *             'src' => $src,
 *             'alt' => $alt,
 *             'len' => $this->len($url)+$this->len($title)
 *         ));
 *     }
 * }
 * </code>
 * 这便是一个能够解析[url]和[img]标签的UBB解析器了。
 * 我们看到，首先它继承了XUBBP类，接着它在$parse属性中定义了
 * UBB的解析规则及要使用的回调方法（$this->ubb和$this->img），
 * 最后它实现了这些回调方法。
 *
 *
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
class XUBBP
{

    /**
     * 显示UBB数组时是否忽略未知的类型
     *
     * 如果该属性为TRUE，遇到未知类型时会直接跳过。
     * 如果它为FALSE，则会抛出一个异常。
     * 不要直接操作该属性，使用 self::skipUnknown() 设置该属性的值。
     */
    protected $skipUnknown = FALSE;

    /**
     * UBB解析器的分析规则列表
     *
     * 它是一个关联数组，格式是：
     * <code>array(
     *  '正则查找' => array(array(开头括号序号,结尾括号序号), '回调方法名', array(传递给回调方法的参数)),,
     *  ……
     * );</code>
     *
     * 数组键值的意义和具体写法见 self::addParse()
     */
    protected $parse = array();

    /**
     * UBB显示器的回调函数列表
     *
     * 它是一个关联数组，格式是：
     * <code>array(
     *  '类型1' => '回调函数1',
     *  '类型2' => '回调函数2',
     *  ……
     * );</code>
     *
     * 数组键值的意义和具体写法见 self::addDisplay()
     */
    protected $display = array();


    /**
     * 结束标记栈（用于自动关闭未结束的标记）
     *
     * 格式：
     * <code>array(
     *     array('type1', 'methodName'),
     *     …
     * );</code>
     *
     * 如果你正在解析一个分成两半匹配的UBB（比如[p][/p]标签对），则请按如下方法操作：
     * 解析开始标记的函数需要使用 $this->regEndTag(); 注册结束标记
     * 解析结束标记的函数需要使用 $this->rmEndTag(); 移除结束标记，防止结束标记重复。
     *
     * 解析完成后，若该栈中仍有未正常关闭的标记，解析器将通过回调函数获得结束标记。
     * 回调函数不接受任何参数，返回与开始标记对应的结束标记。
     * @see self::regEndTag(),self::rmEndTag()
     */
    protected $endTags = array();

    /**
     * 可选或自定义参数
     */
    protected $opt = array();

    /**
     * 递归解析器临时变量
     */
    protected $tmp_parse_result = null;
    protected $tmp_parse_param = null;


    /*len  计算utf-8字符串长度*/
    protected function len($str)
    {
        return mb_strlen($str, 'utf-8');
    }

    /*init 初始化解析器*/
    protected function init()
    {
        $this->endTags = array();
        $this->tmp_parse_result = null;
        $this->tmp_parse_param = null;
    }

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
    public function parse($text, $serialize = false)
    {
        $this->init();
        $arr = $this->parser($text);
        $this->tmp_parse_result = null;
        $this->tmp_parse_param = null;
        $this->rmEndTag(NULL, $arr);
        if ($serialize) return serialize($arr);
        else return $arr;
    }

    /**
     * 递归解析文本
     */
    protected function parser($text)
    {
        if ($text == '') return array();
        foreach ($this->parse as $k => $v) {
            $arr = array();
            $this->tmp_parse_result = &$arr;
            $this->tmp_parse_param = $v;
            $ok = preg_replace_callback($k, array($this, 'parseExec'), $text);
            if ($ok === NULL) throw new xubbpException("正则表达式 '$k' 错误，解析失败！"/*."\n引起错误的文本：$text"*/, 500);
            if ($arr === NULL) throw new xubbpException("正则表达式  '$k' 的回调函数 '$v[1]' 返回值错误，应该返回二维数组！", 501);
            if ($ok == '') return $arr;
        }
        return $this->parseText($text);
    }

    /**
     * 使用回调函数执行递归解析
     */
    protected function parseExec($argv)
    {
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
    protected function parseText($text)
    {
        return array(array(
            'type' => 'text',
            'value' => $text,
            'len' => $this->len($text)
        ));
    }


    /**
     * 将UBB资源数组转换成可显示的文本
     *
     * 该方法通过已定义的回调函数将UBB数组转换成可显示的HTML或其他格式。
     *
     * @return string 转换后的HTML代码
     */
    public function display($ubbArray, $serialize = false, $maxLen = null, $page = null)
    {
        $this->init();

        if ($maxLen != null && $page != null) {
            $ubbArray = $this->displayPage($ubbArray, $maxLen, $page);
        }
        if ($serialize)
            $ubbArray = unserialize($ubbArray);

        $html = '';

        foreach ($ubbArray as $id => $v) {
            $type = $v['type'];
            if (!isset($this->display[$type])) {
                if ($this->skipUnknown) continue;
                else throw new XUBBPException('未知类型：' . $type . ' 类型未被定义，无法解析。请正确定义该类型的显示方案，或者使用 $xubbp->skipUnknown(TRUE) 跳过未知类型。', 404);
            }
            $v['id'] = $id;
            $func = $this->display[$type];
            $html .= $this->$func($v);
        }
        $this->rmEndTag(NULL, $html);
        return $html;
    }

    /**
     * 设置解析规则
     */
    public function setParse($value)
    {
        if (is_array($value)) {
            $this->parse = $value;
            return true;
        } else
            return false;
    }

    /**
     * 读取解析规则
     */
    public function getParse()
    {
        return $this->parse;
    }

    /**
     * 设置显示规则
     */
    public function setDisplay($value)
    {
        if (is_array($value)) {
            $this->display = $value;
            return true;
        } else
            return false;
    }

    /**
     * 读取显示规则
     */
    public function getDisplay()
    {
        return $this->Display;
    }

    /**
     * 读取自定义参数
     *
     * 以点分隔的参数会被保存在多维数组内，比如  a.b.c.d 就是
     * $arr['a']['b']['c']['d']。可以一次获取一个数组，
     * 比如 $ubb->getOpt('a.b.c') 将得到包含['d']其同一级下其他
     * 成员的数组。
     */
    public function getOpt($index = null)
    {
        $set = $this->opt;
        $index = explode('.', $index);
        foreach ($index as $key) {
            $set = $set[$key];
        }
        return $set;
    }

    /**
     * 设置自定义参数
     *
     * 以点分隔的参数会被保存在多维数组内，比如  a.b.c.d 就是
     * $arr['a']['b']['c']['d']。可以一次设置一个数组，
     * 比如 $ubb->setOpt('a.b.c', array('d'=>1, 'e'=>2))
     */
    public function setOpt($index, $data)
    {
        $set =& $this->opt;
        if ($index !== null) {
            $index = explode('.', $index);
            foreach ($index as $key) {
                $set =& $set[$key];
            }
        }
        if ($set === $data) return NULL;
        $set = $data;
        return TRUE;
    }

    /**
     * 注册结束标记回调
     */
    protected function regEndTag($type, $func, $data = NULL)
    {
        array_push($this->endTags, array($type, $func, $data));
    }

    /**
     * 移除结束标记回调
     *
     * @return 找到并成功移除返回 TRUE，未找到返回 FALSE。
     * 如果函数返回 FALSE，表明没有对应的开始标记，
     * 函数调用者应该返回 array() ，不要继续产生结束标记，
     * 否则会有不配对的结束标记出现。
     */
    protected function rmEndTag($type, &$data)
    {
        while ($this->endTags) {
            $tag = array_pop($this->endTags);
            if ($tag[0] != $type) {
                array_push($this->endTags, $tag);
                $func = $tag[1];
                if (is_array($data))
                    $data = array_merge($data, $this->$func($tag[2]));
                else
                    $data .= $this->$func($tag[2]);
            } else {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 为UBB数组分页
     *
     * @todo 对text和code等长文本内容进行拆分
     */
    public function displayPage($ubbArray, $maxLen, $page = null)
    {
        if ($maxLen < 1) $maxLen = 1;
        if (empty($ubbArray))
            return array();

        $arr = array();
        $len = 0;
        $p = &$arr[];
        $p = array();
        foreach ($ubbArray as $v) {
            if ($len >= $maxLen || ($len > 0 && $len + $v['len'] - maxLen >= $maxLen - $len)) {
                $p = &$arr[];
                $p = array();
                $len = 0;
            }
            if ($len == 0 || $len + $v['len'] <= $maxLen) {
                $p[] = $v;
                $len += $v['len'];
            } else {
                throw new XUBBPException('UBB数组分页异常：遗漏元素', 501);
            }
        }
        if ($page != null) {
            if ($page < 1)
                $page = 1;
            if ($page > count($arr))
                $page = count($arr);
        }
        return $page == null ? $arr : $arr[$page - 1];
    }

    /**
     * 注意匹配的顺序，独占内容或不允许嵌套的块（如[code]应该放在前面）
     */
}  
