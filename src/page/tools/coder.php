<?php
$tpl = $PAGE->START();

$content = $_POST['content'];
$code = $_POST['code'];
$action = $_POST['action'];
$result = null;

try {
	switch($action)
	{
		case 'markdown2html':
			$parsedown = new Parsedown();
			$parsedown->setBreaksEnabled(true); //自动换行
			$result = $parsedown->text($content);
			break;
		case 'markdown2html_nolink':
			$parsedown = new Parsedown();
			$parsedown->setBreaksEnabled(true); //自动换行
			$parsedown->setUrlsLinked(false); //不解析链接
			$result = $parsedown->text($content);
			break;
		case 'markdown2html_nohtml':
			$parsedown = new Parsedown();
			$parsedown->setBreaksEnabled(true); //自动换行
			$parsedown->setMarkupEscaped(true); //转义html
			$result = $parsedown->text($content);
			break;

		case 'json2serialize':
			$arr = json_decode($content,true);
			$result = serialize($arr);
			break;
		case 'djson':
			ob_start();
			var_dump(json_decode($content,true));
			$result = ob_get_clean();
			break;
		case 'njson2serialize':
			$arr = json_decode(preg_replace('/\s+/su','',$content),true);
			$result = serialize($arr);
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
			$result = md5(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'esha1':
			$result = sha1(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'esha256':
			$result = hash('sha256', mb_convert_encoding($content,$code,'utf-8'));
			break;

		case 'db64x':
			$result = bin2hex(base64_decode($content));
			break;
		case 'xdb64':
			$result = base64_encode(pack('H*',$content));
			break;

		case 'db32x':
			$result = bin2hex(base32_decode($content));
			break;
		case 'xdb32':
			$result = base32_encode(pack('H*',$content));
			break;
		case 'eb32':
			$result = base32_encode(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'db32':
			$result = mb_convert_encoding(base32_decode($content),'utf-8',$code);
			break;

		case 'ehex':
			$result = bin2hex(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'dhex':
			$result = mb_convert_encoding(pack('H*',$content),'utf-8',$code);
			break;

		case 'db58x':
			$result = bin2hex(base58_decode($content));
			break;
		case 'xdb58':
			$result = base58_encode(pack('H*',$content));
			break;
		case 'eb58':
			$result = base58_encode(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'db58':
			$result = mb_convert_encoding(base58_decode($content),'utf-8',$code);
			break;

		case 'eb64':
			$result = base64_encode(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'db64':
			$result = mb_convert_encoding(base64_decode($content),'utf-8',$code);
			break;
		case 'eb64u':
			$result = url::b64e(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'db64u':
			$result = mb_convert_encoding(url::b64d($content),'utf-8',$code);
			break;
		case 'db64ux':
			$result = bin2hex(url::b64d($content));
			break;
		case 'xdb64u':
			$result = url::b64e(pack('H*',$content));
			break;

		case 'eurl':
			$result = urlencode(mb_convert_encoding($content,$code,'utf-8'));
			break;
		case 'durl':
			$result = mb_convert_encoding(urldecode($content),'utf-8',$code);
			break;
		case 'eurls':
			$result=urlencode(mb_convert_encoding($content,$code,'utf-8'));
			foreach([':','/','?','&','='] as $i)
			{
				$str=$i;
				$result=str_replace(urlencode($str),$str,$result);
			}
			break;

		case 'date':
			$result = date('Y-m-d H:i:s',$content);
			break;
		case 'str2time':
			$result = strtotime($content);
			break;

		case 'jsurd':
			$result = preg_replace_callback('/%(u[0-9a-f]{4}|[0-9a-f]{2})/i',"jsurd",$content);
			break;
		case 'jsure':
			$result = preg_replace_callback('/(.)/us',"jsure",$content);
			break;

		case 'str2lower':
			$result = strtolower($content);
			break;
		case 'str2upper':
			$result = strtoupper($content);
			break;
		case 'str2ucwords':
			$result = ucwords($content);
			break;

		case 'nbsp2space':
			$result = str::nbsp2space($content);
			break;
		
		case 'htmlspecialchars':
			$result = htmlspecialchars($content);
			break;
		case 'htmlentities':
			$result = htmlentities($content);
			break;
		case 'html_entity_decode':
			$result = html_entity_decode($content);
			break;
	}
} catch (Exception $ex) {
	$result = '';
}

$tpl->assign('result', $result);
$tpl->display('tpl:coder');


/* 编码解码函数 */

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

function base32_encode($input) {
	try {
		$BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$output = '';
		$v = 0;
		$vbits = 0;
		$j = strlen($input);

		for ($i = 0; $i < $j; $i++) {
			$v <<= 8;
			$v += ord($input[$i]);
			$vbits += 8;

			while ($vbits >= 5) {
				$vbits -= 5;
				$output .= $BASE32_ALPHABET[$v >> $vbits];
				$v &= ((1 << $vbits) - 1);
			}
		}

		if ($vbits > 0) {
			$pad = 5 - $vbits;
			$v <<= ($pad);
			$output .= $BASE32_ALPHABET[$v] . str_repeat('=', $pad);
		}

		return $output;

	} catch (Exception $e) {
		return '';
	}
}

function base32_decode($input) {
	try {
		$input = strtoupper($input);
		$output = '';
		$v = 0;
		$vbits = 0;
		$j = strlen($input);

		for ($i = 0; $i < $j; $i++) {
			$v <<= 5;
			if ($input[$i] >= 'A' && $input[$i] <= 'Z') {
				$v += (ord($input[$i]) - 65);
			} elseif ($input[$i] >= '2' && $input[$i] <= '7') {
				$v += (24 + $input[$i]);
			} else {
				//忽略无关字符
				continue;
			}

			$vbits += 5;
			while ($vbits >= 8) {
				$vbits -= 8;
				$output .= chr($v >> $vbits);
				$v &= ((1 << $vbits) - 1);
			}
		}

		return $output;

	} catch (Exception $e) {
		return '';
	}
}

function base58_encode($string)
{
    $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $base = strlen($alphabet);

    if (is_string($string) === false || !strlen($string)) {
        return false;
    }

    $bytes = array_values(unpack('C*', $string));
    $decimal = $bytes[0];
    for ($i = 1, $l = count($bytes); $i < $l; ++$i) {
        $decimal = bcmul($decimal, 256);
        $decimal = bcadd($decimal, $bytes[$i]);
    }

    $output = '';
    while ($decimal >= $base) {
        $div = bcdiv($decimal, $base, 0);
        $mod = bcmod($decimal, $base);
        $output .= $alphabet[$mod];
        $decimal = $div;
    }
    if ($decimal > 0) {
        $output .= $alphabet[$decimal];
    }
    $output = strrev($output);

    return (string) $output;
}

function base58_decode($base58)
{
    $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $base = strlen($alphabet);

    if (is_string($base58) === false || !strlen($base58)) {
        return false;
    }
    $indexes = array_flip(str_split($alphabet));
    $chars = str_split($base58);
    foreach ($chars as $char) {
        if (isset($indexes[$char]) === false) {
            return false;
        }
    }
    $decimal = $indexes[$chars[0]];
    for ($i = 1, $l = count($chars); $i < $l; ++$i) {
        $decimal = bcmul($decimal, $base);
        $decimal = bcadd($decimal, $indexes[$chars[$i]]);
    }
    $output = '';
    while ($decimal > 0) {
        $byte = bcmod($decimal, 256);
        $output = pack('C', $byte).$output;
        $decimal = bcdiv($decimal, 256, 0);
    }
    return $output;
}

