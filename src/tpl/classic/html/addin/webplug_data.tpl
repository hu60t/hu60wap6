{include file="tpl:comm.head" title="网页插件自定义数据" no_webplug=true}
{config_load file="conf:site.info"}
<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('js/codemirror/codemirror.min.css')}"/>
<style>
	.CodeMirror {
		border: 1px solid #ddd;
	}
	#webplug-name {
		width: 90%;
	}
	#editForm {
		display: none;
	}
	.webplug-li div {
		display: inline-block;
		margin: 5px;
	}
	.webplug-li button {
		display: inline-block;
		margin-right: 5px;
	}
	.webplug-li-name {
		padding-right: 10px;
	}
	.webplug-li-size {
		padding-right: 5px;
	}
</style>

<script src="{$PAGE->getTplUrl("js/humanize/humanize.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/codemirror.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/xml/xml.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/javascript/javascript.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/css/css.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/htmlmixed/htmlmixed.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/addon/edit/matchbrackets.min.js")}"></script>
<script>
	var editor;
	var webplugData = [];

	function saveWebPlugData() {
		var key = $('#webplug-name').val();
		var value = $('#webplug-content').val();
		var oldKey = $('#webplug-old-name').val();

		if (oldKey != key && webplugData && webplugData.data && key in webplugData.data) {
			if (prompt("数据名称“" + key + "”已存在，继续保存将覆盖原始数据。\n覆盖前建议先导出备份，点击数据大小即可下载备份。\n\n输入yes确认覆盖。") != 'yes') {
				layer.msg('操作已取消');
				return;
			}
		}

		var data = {
			key: key,
			value: value,
		}

		var loading = layer.load();
		var finish = function () {
			$('#editForm').css({
				'display': 'none'
			});
			layer.close(loading);
			layer.msg('保存成功');
			refreshWebPlugDataList();
		};

		$.post('api.webplug-data.json', data, function(data) {
			console.log(data);
			if (data.errmsg) {
				layer.alert(data.errmsg);
			} else {
				if (oldKey != '' && oldKey != key) {
					// 删除旧key
					$.post('api.webplug-data.json', {
						key: oldKey,
						value: '',
					}, () => finish());
				} else {
					finish();
				}
			}
		});

		return false;
	}

	async function toggleWebPlugDataPublic(key, public) {
		var isPublic = /^public_/.test(key);

		if (isPublic == public) {
			return;
		}

		if (public) {
			var newKey = 'public_' + key;
		} else {
			var newKey = key.replace(/^public_/, '');
		}

		if (webplugData && webplugData.data && newKey in webplugData.data) {
			if (prompt("数据名称“" + newKey + "”已存在，继续保存将覆盖原始数据。\n覆盖前建议先导出备份，点击数据大小即可下载备份。\n\n输入yes确认覆盖。") != 'yes') {
				layer.msg('操作已取消');
				return;
			}
		}

		var loading = layer.load();

		// 获取现有数据
		var params = {
			key: key,
		}
		data = await $.post('api.webplug-data.json', params);
		if (data.errmsg) {
			layer.alert(data.errmsg);
			return;
		}
		// 设置新key
		params = {
			key: newKey,
			value: data.data,
		}
		data = await $.post('api.webplug-data.json', params);
		if (data.errmsg) {
			layer.alert(data.errmsg);
			return;
		}
		// 删除旧key
		params = {
			key: key,
			value: '',
		}
		data = await $.post('api.webplug-data.json', params);
		if (data.errmsg) {
			layer.alert(data.errmsg);
			return;
		}

		layer.close(loading);
		layer.msg((public ? '已设为公开' : '已设为私有') + "，名称已由“" + key + "”变为“" + newKey + "”");
		refreshWebPlugDataList();
	}

	function deleteWebPlugData(key) {
		if (prompt("确定删除数据“" + key + "”吗？\n删除前建议先导出备份，点击数据大小即可下载备份。\n\n输入yes确定删除。") != 'yes') {
			layer.msg('操作已取消');
			return;
		}

		var loading = layer.load();
		var data = {
			key: key,
			value: '',
		}
		$.post('api.webplug-data.json', data, function(data) {
			console.log(data);
			layer.close(loading);
			if (data.errmsg) {
				layer.alert(data.errmsg);
			} else {
				layer.msg('删除成功');
			}
			refreshWebPlugDataList();
		});
	}

	async function editWebPlugData(key) {
		var loading = layer.load();
		var result = await $.post('api.webplug-data.json', {
			'key': key
		});
		layer.close(loading);
		if (result.errmsg) {
			layer.alert(result.errmsg);
		} else {
			$('#editForm').css({
				'display': 'block'
			});

			$('#webplug-old-name').val(key);
			$('#webplug-name').val(key);
			setHighLightValue(result.data);
		}
	}

	function addWebPlugData() {
		$('#editForm').css({
			'display': 'block'
		});

		$('#webplug-old-name').val('');
		$('#webplug-name').val('');
		setHighLightValue('');
	}

	function updateWebPlugDataList() {
		var ul = document.getElementById('webplugData');
		ul.innerHTML = '';

		Object.entries(webplugData.data).forEach(item => {
			var [key, sizeNum] = item;

			var li = document.createElement('tr');
			ul.appendChild(li);
			li.classList.add('webplug-li');
			li.id = 'webplug-li-' + key;
			li.dataset.id = key;

			var name = document.createElement('td');
			li.appendChild(name);
			name.classList.add('webplug-li-name');
			name.innerText = key;

			var size = document.createElement('td');
			li.appendChild(size);
			size.classList.add('webplug-li-size');
			size.innerHTML = '<a href="api.webplug-file.' + key + '.json?mime=application/octet-stream&array=1&filename={'网页插件自定义数据'|urlencode}-' + key + '_{date('Y-m-d_H-i-s')}.json">' + humanize.filesize(sizeNum) + '</a>';

			var isPublic = /^public_/.test(key);

			var publicLabel = document.createElement('td');
			li.appendChild(publicLabel);
			publicLabel.innerText = isPublic ? '公开' : '私有';
			var public = document.createElement('input');
			publicLabel.appendChild(public);
			public.classList.add('webplug-li-public');
			public.type = 'checkbox';
			public.checked = isPublic;
			public.disabled = true;

			var actionBar = document.createElement('td');
			li.appendChild(actionBar);

			var togglePublic = document.createElement("button");
			actionBar.appendChild(togglePublic);
			togglePublic.classList.add('webplug-li-toggle-public');
			togglePublic.innerText = isPublic ? '设为私有' : '设为公开';
			togglePublic.onclick = function() {
				toggleWebPlugDataPublic(key, !isPublic);
			}

			var edit = document.createElement("button");
			actionBar.appendChild(edit);
			edit.classList.add('webplug-li-edit');
			edit.innerText = '编辑';
			edit.onclick = function() {
				editWebPlugData(key);
			}

			var del = document.createElement("button");
			actionBar.appendChild(del);
			del.classList.add('webplug-li-delete');
			del.innerText = '删除';
			del.onclick = function() {
				deleteWebPlugData(key);
			}

			var share = document.createElement("button");
			actionBar.appendChild(share);
			share.classList.add('webplug-li-share');
			share.innerText = '外链';
			share.onclick = function() {
				shareWebPlugData(key);
			}
		});

		var toolbarTr = document.createElement('tr');
		ul.appendChild(toolbarTr);
		toolbarTr.classList.add('webplug-li');
		toolbarTr.id = 'webplug-li-toolbar';
		toolbarTr.dataset.id = null;

		var toolbar = document.createElement('td');
		toolbarTr.appendChild(toolbar);
		toolbar.colSpan = 4;

		var add = document.createElement("button");
		toolbar.appendChild(add);
		add.classList.add('webplug-li-add');
		add.innerText = '新增数据';
		add.onclick = function() {
			addWebPlugData();
		}

		var exportBtn = document.createElement("button");
		toolbar.appendChild(exportBtn);
		exportBtn.classList.add('webplug-li-export');
		exportBtn.innerText = '导出备份';
		exportBtn.onclick = function() {
			location.href = 'api.webplug-file.json?mime=application/octet-stream&filename={'网页插件自定义数据-全部导出'|urlencode}_{date('Y-m-d_H-i-s')}.json';
		}

		var importBtn = document.createElement("button");
		toolbar.appendChild(importBtn);
		importBtn.classList.add('webplug-li-import');
		importBtn.innerText = '导入备份';
		importBtn.onclick = function() {
			importData();
		}

		var add = document.createElement("button");
		toolbar.appendChild(add);
		add.classList.add('webplug-li-delete-all');
		add.innerText = '删除所有数据';
		add.onclick = function() {
			deleteAllData();
		}

		var back = document.createElement("button");
		toolbar.appendChild(back);
		back.classList.add('webplug-li-back');
		back.innerText = '返回插件管理';
		back.onclick = function() {
			location.href = 'addin.webplug.html';
		}
	}

	function shareWebPlugData(key) {
		var isPublic = /^public_/.test(key);
		var linkKey = isPublic ? '{$USER->uid}_' + key : key;

		var text = (isPublic ? '公开' : '私有') + '数据“' + key + '”的外链：\n\n' +
			location.protocol + '//' + location.host + '/q.php/api.webplug-file.' + linkKey + '.js\n' +
			location.protocol + '//' + location.host + '/q.php/api.webplug-file.' + linkKey + '.css\n' +
			location.protocol + '//' + location.host + '/q.php/api.webplug-file.' + linkKey + '.html\n' +
			location.protocol + '//' + location.host + '/q.php/api.webplug-file.' + linkKey + '.txt\n' +
		'\n请根据文件类型自行选择扩展名。\n' +
		(isPublic ? '公开类型的外链可分享给其他用户，但他们需要登录虎绿林才能看到内容。' : '私有类型的外链仅限您本人使用，需要登录虎绿林才能看到内容。') +
		'\n\n嵌入网页插件中的方法：\n\n' +
		'<' + 'script src="' + location.protocol + '//' + location.host + '/q.php/api.webplug-file.' + linkKey + '.js"></' + 'script>\n\n' +
		'<link rel="stylesheet" href="' + location.protocol + '//' + location.host + '/q.php/api.webplug-file.' + linkKey + '.css" />\n\n' ;

		$('#editForm').css({
			'display': 'block'
		});
		$('#webplug-old-name').val('');
		$('#webplug-name').val(key + '-link');
		setHighLightValue(text);
	}

	async function refreshWebPlugDataList() {
		var loading = layer.load();
		webplugData = await $.get('api.webplug-data.json?onlylen=1');
		layer.close(loading);
		if (webplugData.errmsg) {
			layer.alert(webplugData.errmsg);
		} else {
			updateWebPlugDataList();
		}
	}

	function deleteAllData() {
		if (prompt("确定删除所有自定义数据？\n由插件存储的所有自定义数据都将永久丢失。\n强烈建议您先下载数据进行备份。\n\n输入yes确定删除。") == 'yes') {
			var loading = layer.load();
			$.post('api.webplug-data.json', {
				key: '',
				value: '',
				prefix: 1
			}, function(result) {
				layer.close(loading);
				if (result.success) {
					layer.msg('删除成功');
					refreshWebPlugDataList();
				} else {
					layer.alert('删除失败' + (result.errmsg || result.notice || result.errInfo.message));
				}
			});
		} else {
			layer.msg('操作已取消');
		}
	}

	function importData() {
		// 导入备份文件选择器
		var fileSelector = document.createElement('input');
		fileSelector.id = 'fileSelector';
		fileSelector.style.display = 'none';
		fileSelector.type = 'file';
		fileSelector.onchange = function (e) {
			if (e.target.files && e.target.files[0]) {
				importFile(e.target.files[0]);
			}
		}
		fileSelector.click();
	}

	function importFile(file) {
		console.log('importFile:', file);

		var fd = new FormData();
		fd.append('file', file);
		
		var loading = layer.load();
		$.ajax({
			type: 'POST',
			url: '/q.php/api.webplug-data-import.json',
			data: fd,
			processData: false,
			contentType: false
		}).done(function (ret) {
			layer.close(loading);
			console.debug(ret);
			if (ret.success) {
				layer.msg('成功导入 ' + ret.count.success + ' 条数据');
				refreshWebPlugDataList();
			} else {
				layer.alert('导入失败：' + ret.errmsg);
			}
		}).fail(function (ret) {
			layer.close(loading);
			layer.alert('文件上传失败：' + JSON.stringify(ret));
		});
	}

	function enableHighlight() {
		editor = CodeMirror.fromTextArea($('#webplug-content')[0], {
			mode: "text/html",
			lineNumbers: true,
			matchBrackets: true,
			extraKeys: {
			'Ctrl-S': saveWebPlugData
			}
		});
		editor.on('change', editor => {
			editor.save();
		});
	}
	function toggleHighLight() {
		var public = document.querySelector('#enable_highlight').checked;
		if (public) {
			enableHighlight();
		} else {
			editor.toTextArea();
		}
		localStorage.webplugEnableHighlight = public ? '1' : '0';
	}
	function setHighLightValue(value) {
		var public = document.querySelector('#enable_highlight').checked;
		if (public) {
			if (!editor) {
				enableHighlight();
			}
			editor.setValue(value);
		} else {		
			$('#webplug-content').val(value);
		}
	}

	$(document).ready(function(){
		var checkbox = document.querySelector('#enable_highlight');
		checkbox.onclick = toggleHighLight;
		if (localStorage.webplugEnableHighlight == undefined) {
			checkbox.checked = true;
		} else {
			checkbox.checked = (localStorage.webplugEnableHighlight == '1') ? true : false;
		}
		refreshWebPlugDataList();
	});
</script>

<div class="tp">
	<a href="index.index.{$BID}">首页</a> &gt; <a href="{$CID}.{$PID}.{$BID}">网页插件</a> &gt; 自定义数据管理 | <a href="bbs.forum.140.html">论坛：网页插件专版</a>
</div>

<p>自定义数据列表：</p>
<table id="webplugData">
</table>

<form action="api.webplug-data.json" method="post" id="editForm">
	<hr>
	<div>
		<label>
			<input type="checkbox" id="enable_highlight">启用代码高亮（如果代码高亮导致编辑不方便，可以点此禁用）
		</label>
	</div>
	<p>名称：<input type="text" name="key" id="webplug-name" value="" /></p>
	<p>　　　名称只能包含英文小写字母(a-z)、数字(0-9)、下划线(_)和减号(-)。</p>
	<p>内容：</p>
	<p>
		<textarea name="value" id="webplug-content" style="height:300px;"></textarea>
	<p>
	<p>
		<input type="button" onclick="saveWebPlugData()" value="保存" />
	</p>

	<input type="hidden" name="oldKey" id="webplug-old-name" value="" />
</form>

<hr>
<div>
	<p>自定义数据可用于保存网页插件的个性化设置，还可用于存储网页插件的代码本身，比如JS、CSS等文件。</p>
	<p>编写网页插件时，您可创建公开自定义数据，然后将其作为JS、CSS外链引用。这样不仅减小了网页插件代码，您还可随时对插件进行更新。</p>
	<p>如您正在开发网页插件，想用自定义数据保存插件设置，可参考<a href="https://hu60.cn/q.php/bbs.topic.83603.html">数据存储API</a>。</p>
</div>

{include file="tpl:comm.foot"}
