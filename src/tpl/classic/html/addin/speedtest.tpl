{include file="tpl:comm.head" title="访问速度测试" no_webplug=true}
<script src="/tpl/classic/js/jquery/dist/jquery.min.js"></script>
<script src="/tpl/classic/js/humanize/humanize.js"></script>
<script>
	var result = {
		/* tag1: { startTime:111, endTime:222, success:true, speed: 1111 },
		 * tag2: { startTime:112, endTime:245, success:false, errCode:'timeout' },
		 * ... 
		 */
		};

	var errtext = {
		notmodified: '测试数据意外的被缓存',
		nocontent: '获取到的测试数据为空',
		error: '未知错误',
		timeout: '连接超时',
		abort: '测试被中断',
		parsererror: '数据解析错误',
		lengtherror: '测试数据长度不符'
	};

	var complete = 0;
	var timerInterval = null;

	function startTest() {
		$('#report_button').attr('disabled',true);
		$('#test_button').attr('disabled',true);
		$('#test_button').val('测试中...');
		$('#report_button').val('发送报告');

		var timeout = 15;
		timerInterval = window.setInterval(timer, 1000);

		function timer() {
			$('#test_button').val('测试中(' + timeout + 's)');
			timeout--;
		}

		complete = 0;

		testUrl('http://hu60.cn/q.php/addin.speedtest.json?action=send', 'main');
		testUrl('https://ssl.hu60.cn/q.php/addin.speedtest.json?action=send', 'mainssl');
		testUrl('http://360.cdn.hu60.cn/q.php/addin.speedtest.json?action=send', 'cdn360');
		testUrl('https://360.cdn.hu60.cn/q.php/addin.speedtest.json?action=send', 'cdn360ssl');
		testUrl('http://baidu.cdn.hu60.cn/q.php/addin.speedtest.json?action=send', 'baidu');
		testUrl('http://yd.cdn.hu60.cn/q.php/addin.speedtest.json?action=send', 'yundun');
	}
	function sendReport() {
		$('#report_button').attr('disabled',true);
		$('#test_button').attr('disabled',true);
		$('#report_button').val('发送中...');

		var msg = JSON.stringify(result);
		var data = { data: msg };
		
		$.ajax({
			url: '{$CID}.{$PID}.{$BID}?action=report',
			method: 'post',
			data: data,
			complete: function(xhr, stat) {
				if ('success' == stat) {
					$('#report_button').val('发送成功');
					$('#test_button').removeAttr('disabled');
				} else {
					$('#report_button').val(errtext[stat] + '(重新发送)');
					$('#report_button').removeAttr('disabled');
					$('#test_button').removeAttr('disabled');
				}
				
				//防止测试完成事件被错误触发
				complete = 0;
			}
		});
	}
	function testUrl(url, tag) {
		var stat = $('#' + tag + ' .state');
		var speed = $('#' + tag + ' .speed');
		stat.html('测试中...');

		result[tag] = { startTime: new Date().getTime() };
		
		$.ajax({
			cache: false,
			timeout: 15000, //15秒
			url: url,
			_tag: tag,
			_stat: stat,
			_speed: speed,
			complete: function(xhr, stat) {
				var item = result[this._tag];
				item.endTime = new Date().getTime();

				if ('success' != stat || 10240 != xhr.responseText.length) {
					item.success = false;
					
					if ('success' != stat) {
						item.errCode = stat;
					} else {
						item.errCode = 'lengtherror';
					}

					this._stat.html(errtext[item.errCode]);
				} else {
					item.success = true;

					var time = (item.endTime - item.startTime) / 1000;
					item.speed = 10240 / time;
					this._stat.html(time + 's');
					this._speed.html(humanize.filesize(item.speed) + '/s');
				}
				
				result[this._tag] = item;
			}
		});
	}
	$(function() {
		//解决刷新页面后按钮状态不正确的问题
		$('#test_button').removeAttr('disabled');
		$('#report_button').attr('disabled',true);

		//测试完成后触发的操作
		$(document).ajaxComplete(function(event, xhr, options) {
			complete++;

  			if (complete >= 6) {
				$('#report_button').removeAttr('disabled');
				$('#test_button').val('重新测试');
				$('#test_button').removeAttr('disabled');

				window.clearInterval(timerInterval);
			}
		});
	});
</script>
<style>
	.test_item p {
		display: inline-block;
	}
	.name, .state, .speed {
		min-width: 80px;
	}
	#title {
		font-weight: bold;
	}
</style>
<div class="tp">
	<p>感谢您参与虎绿林的访问速度测试。</p>
	<p>您可以将测试结果发送给虎绿林服务器。</p>
	<p>虎绿林将选择一条大部分人都访问较快的线路做为主站线路。</p>
</div>
<div class="test">
	<div class="test_item" id="title">
		<p class="name">线路</p>
		<p class="state">下载10KB</p>
		<p class="speed">访问速度</p>
	</div>
	<div class="test_item" id="main">
		<p class="name"><a href="http://hu60.cn{$smarty.server.REQUEST_URI|code}">主站</a></p>
		<p class="state">待测试</p>
		<p class="speed"></p>
	</div>
	<div class="test_item" id="mainssl">
		<p class="name"><a href="https://ssl.hu60.cn{$smarty.server.REQUEST_URI|code}">主站(ssl)</a></p>
		<p class="state">待测试</p>
		<p class="speed"></p>
	</div>
	<div class="test_item" id="cdn360">
		<p class="name"><a href="http://360.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">360</a></p>
		<p class="state">待测试</p>
		<p class="speed"></p>
	</div>
	<div class="test_item" id="cdn360ssl">
		<p class="name"><a href="https://360.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">360(ssl)</a></p>
		<p class="state">待测试</p>
		<p class="speed"></p>
	</div>
	<div class="test_item" id="baidu">
		<p class="name"><a href="http://baidu.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">百度</a></p>
		<p class="state">待测试</p>
		<p class="speed"></p>
	</div>
	<div class="test_item" id="yundun">
		<p class="name"><a href="http://yd.cdn.hu60.cn{$smarty.server.REQUEST_URI|code}">云盾</a></p>
		<p class="state">待测试</p>
		<p class="speed"></p>
	</div>
	<div class="toolbar">
		<input type="button" id="test_button" onclick="startTest()" value="开始测试">
		<input type="button" id="report_button" onclick="sendReport()" value="发送报告" disabled>
	</div>
</div>
{include file="tpl:comm.foot"}