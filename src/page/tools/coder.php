<?php
$tpl = $PAGE->START();

$content = $_POST['content'];
$code = $_POST['code'];
$action = $_POST['action'];
$result = null;

switch($action)
{
    case 'json2serialize':
        $arr = json_decode($content,true);
        $result = urlencode(serialize($arr));
        break;
    case 'djson':
        ob_start();
        var_dump(json_decode($content,true));
        $result = ob_get_clean();
        break;
    case 'njson2serialize':
        $arr = json_decode(preg_replace('/\s+/su','',$content),true);
        $result = urlencode(serialize($arr));
        break;
    case 'ndjson':
        ob_start();
        var_dump(json_decode(preg_replace('/\s+/su','',$content),true));
        $result = ob_get_clean();
        break;
    case 'ejson':
        $result = json_encode($content);
        break;
    case 'emd5':
        $result = md5($content);
        break;
    case 'esha1':
        $result = sha1($content);
        break;
    case 'esha256':
        $result = hash('sha256', $content);
        break;
    case 'db64x':
        $result = bin2hex(base64_decode($content));
        break;
    case 'xdb64':
        $result = base64_encode(pack('H*',$content));
        break;
    case 'ehex':
        $result = bin2hex($content);
        break;
    case 'dhex':
        $result = mb_convert_encoding(pack('H*',$content),'utf-8',$code);
        break;
    case 'eb64':
        $result = base64_encode($content);
        break;
    case 'db64':
        $result = mb_convert_encoding(base64_decode($content),'utf-8',$code);
        break;
    case 'eurl':
        $result = urlencode($content);
        break;
    case 'durl':
        $result = mb_convert_encoding(urldecode($content),'utf-8',$code);
        break;
    case 'eurls':
        $result=urlencode($content);
        for($i=33;$i<=126;$i  )
        {
            $str=chr($i);
            $result=str_replace(urlencode($str),$str,$content);
        }
        break;
    case 'date':
        $result = date('Y-m-d H:i:s',$content);
        break;
    case 'str2time':
        $result = strtotime($content);
        break;
    case 'jsurd':
        $result = preg_replace_callback('/%(u[0-9a-f]{4}|[0-9a-f]{2})/',"jsurd",$content);
        break;
    case 'jsure':
        $result = preg_replace_callback('/(.)/us',"jsure",$content);
        break;
}

function jsurd($s) {
    $s=$s[1];
    if(strtolower($s[0])=='u') {
        return mb_convert_encoding(pack('H*',substr($s,1,4)),'utf-8','utf-16be');
    } else {
        return rawurldecode("%$s");
    }
}

function jsure($s) {
    $s=$s[1];
    if($s=='.') return '%2e';
    elseif(strlen($s)==1) return strtolower(rawurlencode($s));
    $s=unpack('H*',mb_convert_encoding($s,'utf-16be','utf-8'));
    return '%u'.$s[1];
}

$tpl->assign('result', $result);
$tpl->display('tpl:coder');
