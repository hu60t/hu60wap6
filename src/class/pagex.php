<?php

class pagex
{
    public $total; //记录总数
    public $pagename; //分页URL参数名称default:p;
    public $pagesize; //每页记录数default:10        
    public $pagelist; //显示数字页数default:5
    public $pageurl; //链接URL
    public $pagetext; //显示分页相关信息
    public $thispage;

    // 初始化函数
    function __construct($pagesize = 10, $pagelist = 5, $pagetext = true, $pagename = 'p')
    {
        // $this->total = intval($total);
        $this->pagesize = $pagesize;
        $this->pagename = $pagename;
        $this->pagelist = $pagelist;
        $this->pagetext = $pagetext;
        $this->thispage = ($this->currpage() - 1) * $this->pagesize;
        // URL处理===================================
        if (!$this->pageurl) {
            $this->pageurl = $_SERVER["REQUEST_URI"];
        }
        $parse_url = parse_url($this->pageurl);
        if (!empty($parse_url["query"])) {
            $url_query = $parse_url["query"];
            $url_query = preg_replace("!(^|&){$this->pagename}={$this->currpage()}!", "", $url_query);
            $this->pageurl = str_replace($parse_url["query"], $url_query, $this->pageurl);
            if ($url_query) $this->pageurl .= "&amp;" . $this->pagename;
            else $this->pageurl .= $this->pagename;
        } else {
            $this->pageurl .= "?" . $this->pagename;
        }
        // URL处理结束=================================
    }


    // 获取当前页码
    function currpage()
    {
        $getpage = (@$_REQUEST[$this->pagename] + 0 <= 0) ? 1 : (@$_REQUEST[$this->pagename] + 0);
        if (!is_int($getpage)) {
            $getpage = 1;
        }
        return $getpage;
    }
    // 获取当前页码
    /**
     * 计算页数================================
     */
    // 最后一页
    function lastpage()
    {

        return intval((floor($this->total / $this->pagesize) == ceil($this->total / $this->pagesize)) ? floor($this->total / $this->pagesize) : ceil($this->total / $this->pagesize));
        // return ceil($this->total/$this->pagesize);
    }

    // 上一页
    function prevpage()
    {
        return $this->currpage() - 1;
    }

    // 下一页
    function nextpage()
    {
        return ($this->currpage() == $this->lastpage() || $this->lastpage() < 1 ? 0 : $this->currpage() + 1);
    }
    /**
     * 计算结束================================
     */

    // 数字分页按扭
    function numberhtml($page)
    {
        if ($this->currpage() == $page) {
            return "<span>[" . $page . "]</span>";
        } else {
            return "<span>" . "<a class=\"pnum\" title=\"" . $page . "\" href=\"" . $this->pageurl . "=" . $page . "\">[" . $page . "]</a>" . "</span>";
        }
    }

    // 计算生成数字分页导航条
    function pagelist()
    {
        if ($this->pagelist > 0) {
            $listhtml = '';
            $listhtml .= '';
            if ($this->pagelist >= $this->lastpage()) {
                for ($i = 1; $i <= $this->lastpage(); $i++) {
                    $listhtml .= $this->numberhtml($i);
                }
            } else {
                $ceilpage = ceil($this->pagelist / 2);
                if ($this->currPage() <= $ceilpage) {
                    for ($i = 1; $i <= $this->pagelist; $i++) {
                        $listhtml .= $this->numberhtml($i);
                    }
                } else {
                    if ($this->currPage() + $ceilpage <= $this->lastpage()) {
                        for ($i = $this->currPage() - $ceilpage + 1; $i <= $this->currPage() + $ceilpage; $i++) {
                            $listhtml .= $this->numberhtml($i);
                        }
                    } else {
                        for ($i = $this->lastpage() - $this->pagelist + 1; $i <= $this->lastpage(); $i++) {
                            $listhtml .= $this->numberhtml($i);
                        }
                    }
                }
            }
            $listhtml .= "";

            return $listhtml;

        } else {
            return "";
        }
    }


    // 显示分页模块
    function pageshow()
    {
        $pagehtml = '';

        $homelink = $this->pageurl . "=1";
        $prevlink = $this->pageurl . "=" . $this->prevpage();
        $nextlink = $this->pageurl . "=" . $this->nextpage();
        $lastlink = $this->pageurl . "=" . $this->lastpage();


        if ($this->nextpage()) {
            $pagehtml .= "<a href=\"" . $nextlink . "\">下页</a> ";
            // $pagehtml.="<a href=\"".$lastlink."\">尾页</a><br />";
        }
        $pagehtml .= $this->pagelist(); //数字分页导航条
        if ($this->prevpage()) {
            // $pagehtml.="<a href=\"".$homelink."\">首页</a> ";
            $pagehtml .= "<a href=\"" . $prevlink . "\">上页</a> ";
        }
        if ($this->prevpage() or $this->nextpage()) {
            $pagehtml .= "<form action='" . $this->pageurl . "' method='GET'><input name=\"p\" type=\"text\" size=\"3\" value=\"{$this->currpage()}\" /><input type=\"submit\" value=\"跳转\" /></form>";
        }
        $pagehtml .= "[第" . $this->currpage() . "页/共" . $this->lastpage() . "页/总" . $this->total . "条]";

        return $pagehtml;
    }
}
