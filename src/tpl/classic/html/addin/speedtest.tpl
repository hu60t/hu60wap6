{include file="tpl:comm.head" title="访问速度测试" no_webplug=true}
<script src="/tpl/classic/js/jquery-3.1.1.min.js"></script>
<script src="/tpl/classic/js/humanize/humanize.js"></script>
<style>
	.test_item p {
		display: inline-block;
		text-align: center;
	}
	.name, .reportSize, .successRate {
		min-width: 80px;
	}
	.state {
		min-width: 100px;
	}
	.speed {
		min-width: 130px;
	}
	#title {
		font-weight: bold;
	}
	.itemB {
		background: #F0F0F0;
	}
	.itemA {
		background: #FFFFFF;
	}
</style>
<div class="tp">
	<p>通过网速测试来找到最适合自己的访问速度最快的虎绿林线路。</p>
	<p>注意，某些浏览器无法在采用https连接时测试http线路，所有http线路都会得到“未知错误”。</p>
</div>
<div class="test">
	<div class="test_item itemB" id="title">
		<p class="name">线路</p>
		<p class="state">下载10KB</p>
		<p class="speed">访问速度</p>
		<p class="reportSize">报告数</p>
		<p class="successRate">成功率</p>
	</div>
	{foreach $testResults as $tag=>$item}
	<div class="test_item {cycle values="itemA,itemB"}" id="{$tag}">
		<p class="name"><a href="{$item.urlPrefix}{$smarty.server.REQUEST_URI|code}">{$item.name}</a></p>
		<p class="state">平均 {round($item.time/1000,2)}s</p>
		<p class="speed">平均 {str::filesize($item.speed)}/s</p>
		<p class="reportSize">{$item.size}</p>
		<p class="successRate">{round($item.successRate*100,2)}%</p>
	</div>
	{/foreach}
	<div class="toolbar">
		<input type="button" id="test_button" onclick="startTest()" value="开始测试">
		<input type="button" id="report_button" onclick="sendReport()" value="发送报告" disabled>
		<a href="{$CID}.{$PID}.{$BID}?r={$smarty.server.REQUEST_TIME}">刷新报告</a>
	</div>
</div>
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
		$('.reportSize, .successRate').hide();
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

		{foreach $testSites as $tag=>$item}
		testUrl('{$item.urlPrefix}/q.php/addin.speedtest.json?action=send', '{$tag}');
		{/foreach}
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
				complete = -1;
			}
		});
	}
	function testUrl(url, tag) {
		var stat = $('#' + tag + ' .state');
		var speed = $('#' + tag + ' .speed');
		stat.html('待测试');

		$.ajax({
			cache: false,
			timeout: 15000, //15秒
			url: url,
			_tag: tag,
			_stat: stat,
			_speed: speed,
			beforeSend: function(xhr, options) {
				stat.html('测试中...');
				result[options._tag] = { startTime: new Date().getTime() };
			},
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

  			if (complete >= {count($testSites)}) {
				$('#report_button').removeAttr('disabled');
				$('#test_button').val('重新测试');
				$('#test_button').removeAttr('disabled');

				window.clearInterval(timerInterval);
			}
		});
	});
</script>
{include file="tpl:comm.foot"}
