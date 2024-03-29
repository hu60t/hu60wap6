{extends file='tpl:comm.default'}
{$no_webplug=true}
{config_load file="conf:site.info"}
{block name='title'}
	网页插件
{/block}

{block name='style'}
	<link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl('js/codemirror/codemirror.min.css')}"/>
	<style>
		.CodeMirror {
			border: 1px solid #ddd;
			font-family: ui-monospace,SFMono-Regular,SF Mono,Menlo,Consolas,Liberation Mono,monospace,
				"Apple Color Emoji","Segoe UI Emoji","Twemoji Mozilla","Noto Color Emoji",
				/*优先系统字体*/
				sans-serif,serif,system-ui,fangsong,emoji,math,cursive,fantasy,
				/*用于显示生僻字*/
				'MiSans Normal', 'MiSans L3', 'GlyphWikiFont';
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
{/block}

{block name='script'}
<script src="{$PAGE->getTplUrl("js/codemirror/codemirror.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/xml/xml.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/javascript/javascript.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/css/css.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/mode/htmlmixed/htmlmixed.min.js")}"></script>
<script src="{$PAGE->getTplUrl("js/codemirror/addon/edit/matchbrackets.min.js")}"></script>
<script>
	var editor;
	var webplugList = [];

	function saveWebPlug() {
		var loading = layer.load();
		var data = {
			id: $('#webplug-id').val(),
			load_order: $('#webplug-load-order').val(),
			enabled: $('#webplug-enabled').val(),
			name: $('#webplug-name').val(),
			content: $('#webplug-content').val(),
			author_uid: {$USER->uid},
			webplug_id: '',
		}

		$.post($('#editForm').attr('action'), data, function(data) {
			console.log(data);
			layer.close(loading);
			if (data.errmsg) {
				layer.alert(data.errmsg);
			} else {
				layer.msg('保存成功');
				$('#editForm').css({
					'display': 'none'
				});
			}
			refreshWebPlugList();
		});

		return false;
	}

	function toggleWebPlugEnabled(id, enabled) {
		var loading = layer.load();
		var data = {
			id: id,
			enabled: enabled ? 1 : 0,
		}
		$.post('api.webplug.enable.json', data, function(data) {
			console.log(data);
			layer.close(loading);
			if (data.errmsg) {
				layer.alert(data.errmsg);
			} else {
				layer.msg((enabled ? '启用' : '禁用') + '成功');
			}
			refreshWebPlugList();
		});
	}

	function deleteWebPlug(id, name) {
		if (prompt("确定删除插件“" + name + "”吗？\n删除前建议先导出备份，点击插件大小即可下载备份。\n\n输入yes确定删除。") != 'yes') {
			layer.msg('操作已取消');
			return;
		}

		var loading = layer.load();
		var data = {
			id: id,
		}
		$.post('api.webplug.delete.json', data, function(data) {
			console.log(data);
			layer.close(loading);
			if (data.errmsg) {
				layer.alert(data.errmsg);
			} else {
				layer.msg('删除成功');
			}
			refreshWebPlugList();
		});
	}

	async function editWebPlug(id) {
		var loading = layer.load();
		var result = await $.post('api.webplug.get.json', {
			'id': id
		});
		layer.close(loading);
		if (result.errmsg) {
			layer.alert(result.errmsg);
		} else {
			var item = result.data;
			webplugList.map[item.id] = item;
			webplugList.editId = item.id;

			$('#editForm').attr('action', 'api.webplug.update.json');
			$('#editForm').css({
				'display': 'block'
			});

			$('#webplug-id').val(item.id);
			$('#webplug-load-order').val(item.load_order);
			$('#webplug-enabled').val(item.enabled ? 1 : 0);
			$('#webplug-name').val(item.name);
			setHighLightValue(item.content);
		}
	}

	function addWebPlug() {
		$('#editForm').attr('action', 'api.webplug.add.json');
		$('#editForm').css({
			'display': 'block'
		});

		$('#webplug-id').val('');
		$('#webplug-load-order').val(webplugList.maxLoadOrder ? webplugList.maxLoadOrder + 1 : 1);
		$('#webplug-enabled').val(1);
		$('#webplug-name').val('');
		setHighLightValue('');
	}

	function moveWebPlug(index1, index2) {
		var loadOrderArray = {}
		for (var i=0; i<webplugList.data.length; i++) {
			var order = i + 1;
			if (i == index1) order++;
			if (i == index2) order--;
			var item = webplugList.data[i];
			if (order != item.load_order) {
				loadOrderArray[item.id] = order;
			}
		}

		var loading = layer.load();
		$.post('api.webplug.set_load_order.json', {
			data: JSON.stringify(loadOrderArray)
		}, function(data) {
			console.log(data);
			layer.close(loading);
			if (data.errmsg) {
				layer.alert(data.errmsg);
			} else {
				layer.msg('移动成功');
			}
			refreshWebPlugList();
		});
	}

	function importWebPlug() {
		var file = document.createElement('input');
		file.id = 'file-selection';
		file.style.display = 'none';
		file.type = 'file';
		file.onchange = function (e) {
			Array.from(e.target.files).forEach(f => {
				f.text().then(text => {
					var loading = layer.load();
					$.post('api.webplug.import.json', {
						data: text
					}, function(data) {
						console.log(data);
						layer.close(loading);
						if (data.errmsg) {
							layer.alert(data.errmsg);
						} else {
							layer.msg('成功导入 ' + data.updated + '个网页插件');
						}
						refreshWebPlugList();
					});
				});
			})
		}
		file.click();
	}

	function updateWebPlugList() {
		var ul = document.getElementById('webplugList');
		ul.innerHTML = '';
		webplugList.map = {};
		webplugList.maxLoadOrder = false

		webplugList.data.forEach((item, index) => {
			webplugList.map[item.id] = item;
			if (!webplugList.maxLoadOrder || webplugList.maxLoadOrder < item.load_order) {
				webplugList.maxLoadOrder = item.load_order;
			}

			var li = document.createElement('tr');
			ul.appendChild(li);
			li.classList.add('webplug-li');
			li.id = 'webplug-li-' + item.id;
			li.dataset.id = item.id;

			var loadOrder = document.createElement('td');
			li.appendChild(loadOrder);
			loadOrder.classList.add('webplug-li-load-order');
			loadOrder.innerText = item.load_order + '.';

			var name = document.createElement('td');
			li.appendChild(name);
			name.classList.add('webplug-li-name');
			name.innerText = item.name;

			var size = document.createElement('td');
			li.appendChild(size);
			size.classList.add('webplug-li-size');
			size.innerHTML = '<a href="api.webplug.export.' + item.id + '.json">' + humanize.filesize(item.size) + '</a>';

			var enabledLabel = document.createElement('td');
			li.appendChild(enabledLabel);
			enabledLabel.innerText = item.enabled ? '已启用' : '已禁用';
			var enabled = document.createElement('input');
			enabledLabel.appendChild(enabled);
			enabled.classList.add('webplug-li-enabled');
			enabled.type = 'checkbox';
			enabled.checked = item.enabled;
			enabled.disabled = true;

			var actionBar = document.createElement('td');
			li.appendChild(actionBar);

			var toggleEnabled = document.createElement("button");
			actionBar.appendChild(toggleEnabled);
			toggleEnabled.classList.add('webplug-li-toggle-enabled');
			toggleEnabled.innerText = item.enabled ? '禁用' : '启用';
			toggleEnabled.onclick = function() {
				toggleWebPlugEnabled(item.id, !item.enabled);
			}

			var edit = document.createElement("button");
			actionBar.appendChild(edit);
			edit.classList.add('webplug-li-edit');
			edit.innerText = '编辑';
			edit.onclick = function() {
				editWebPlug(item.id);
			}

			var del = document.createElement("button");
			actionBar.appendChild(del);
			del.classList.add('webplug-li-delete');
			del.innerText = '删除';
			del.onclick = function() {
				deleteWebPlug(item.id, item.name);
			}

			if (webplugList.data.length > 1) {
				var up = document.createElement("input");
				actionBar.appendChild(up);
				up.type = "button";
				up.value = '▲';
				if (index > 0) {
					up.onclick = function() {
						moveWebPlug(index-1, index);
					}
				} else {
					up.disabled = true;
				}

				var down = document.createElement("input");
				actionBar.appendChild(down);
				down.type = "button";
				down.value = '▼';
				if (index < webplugList.data.length - 1) {
					down.onclick = function() {
						moveWebPlug(index, index+1);
					}
				} else {
					down.disabled = true;
				}
			}

		});

		var toolbarTr = document.createElement('tr');
		ul.appendChild(toolbarTr);
		toolbarTr.classList.add('webplug-li');
		toolbarTr.id = 'webplug-li-toolbar';
		toolbarTr.dataset.id = null;

		var toolbar = document.createElement('td');
		toolbarTr.appendChild(toolbar);
		toolbar.colSpan = 5;

		var add = document.createElement("button");
		toolbar.appendChild(add);
		add.classList.add('webplug-li-add');
		add.innerText = '新增插件';
		add.onclick = function() {
			addWebPlug();
		}

		var exportBtn = document.createElement("button");
		toolbar.appendChild(exportBtn);
		exportBtn.classList.add('webplug-li-export');
		exportBtn.innerText = '导出备份';
		exportBtn.onclick = function() {
			location.href = 'api.webplug.export.json';
		}

		var importBtn = document.createElement("button");
		toolbar.appendChild(importBtn);
		importBtn.classList.add('webplug-li-import');
		importBtn.innerText = '导入备份';
		importBtn.onclick = function() {
			importWebPlug();
		}

		var back = document.createElement("button");
		toolbar.appendChild(back);
		back.classList.add('webplug-li-back');
		back.innerText = '返回查看插件效果';
		back.onclick = function() {
			location.href = document.referrer ? document.referrer : '/';
		}
	}

	async function refreshWebPlugList() {
		var loading = layer.load();
		webplugList = await $.get('api.webplug.list.json');
		layer.close(loading);
		if (webplugList.errmsg) {
			layer.alert(webplugList.errmsg);
		} else {
			updateWebPlugList();
		}
	}

	function enableHighlight() {
		editor = CodeMirror.fromTextArea($('#webplug-content')[0], {
			mode: "text/html",
			lineNumbers: true,
			matchBrackets: true,
			extraKeys: {
			'Ctrl-S': saveWebPlug
			}
		});
		editor.on('change', editor => {
			editor.save();
		});
	}
	function toggleHighLight() {
		var enabled = document.querySelector('#enable_highlight').checked;
		if (enabled) {
			enableHighlight();
		} else {
			editor.toTextArea();
		}
		localStorage.webplugEnableHighlight = enabled ? '1' : '0';
	}
	function setHighLightValue(value) {
		var enabled = document.querySelector('#enable_highlight').checked;
		if (enabled) {
			if (!editor) {
				enableHighlight();
			}
			editor.setValue(value);
		} else {		
			$('#webplug-content').val(value);
		}
	}
	function hideEditForm() {
		$('#editForm').css({
			'display': 'none'
		});
	}

	$(document).ready(function(){
		var checkbox = document.querySelector('#enable_highlight');
		checkbox.onclick = toggleHighLight;
		if (localStorage.webplugEnableHighlight == undefined) {
			checkbox.checked = true;
		} else {
			checkbox.checked = (localStorage.webplugEnableHighlight == '1') ? true : false;
		}
		refreshWebPlugList();
	});
</script>
{/block}

{block name='body'}
<div class="breadcrumb">
	<a href="index.index.{$BID}">首页</a> &gt; 网页插件 | <a href="bbs.forum.140.html">论坛：网页插件专版</a>
</div>

<hr>

<div>
	<p>网页插件是一段插入{#SITE_SIMPLE_NAME#}网页底部&lt;/body&gt;标签前的代码，可以在其中添加&lt;script&gt;、&lt;style&gt;等任何html标签来扩展虎绿林网页的功能。</p>
	<p style="color:red">警告：从他人处复制的代码可能含有恶意程序，造成版面错乱、帐户被盗、数据损坏，甚至计算机感染病毒等严重后果！</p>
	<p style="color:green">请仅从信任的人处复制代码，并且仔细检查，避免使用不知用途的代码。</p>
</div>

<hr>
<p>插件列表（<a href="https://hu60.cn/q.php/bbs.topic.103325.1.html#yanshi">导入方法</a>）：</p>
<table id="webplugList">
</table>

<form action="" method="post" id="editForm">
	<hr>
	<div>
		<label>
			<input type="checkbox" id="enable_highlight">启用代码高亮（如果代码高亮导致编辑不方便，可以点此禁用）
		</label>
	</div>
	<p>插件名称：<input type="text" name="name" id="webplug-name" value="" /></p>
	<p>插件代码：</p>
	<p>
		<textarea name="content" id="webplug-content" style="height:300px;"></textarea>
	<p>
	<p style="color:green">保存前请先将本页存为书签，如果插件代码发生意外还能从书签进入本页删除。</p>
	<p>
		<input type="button" onclick="saveWebPlug()" value="保存" />
		<input type="button" onclick="hideEditForm()" value="取消" />
	</p>

	<input type="hidden" name="id" id="webplug-id" value="" />
	<input type="hidden" name="load_order" id="webplug-load-order" value="" />
	<input type="hidden" name="enabled" id="webplug-enabled" value="" />
</form>

<hr>
<div>
	<p>插件存储的自定义数据：{$plugDataCount}条（{str::filesize($plugDataSize)}）</p>
	<p><button onclick="location.href = '{$CID}.{$PID}.data.{$BID}'">管理自定义数据</button></p>
	<p>自定义数据可用于保存网页插件的个性化设置，还可用于存储网页插件的代码本身，比如JS、CSS等文件。</p>
	<p>编写网页插件时，您可创建公开自定义数据，然后将其作为JS、CSS外链引用。这样不仅减小了网页插件代码，您还可随时对插件进行更新。</p>
	<p>同样的，如您使用他人的网页插件，觉得对方提供的外链JS不安全、速度慢，或有失效风险，也可以把JS的内容复制到自定义数据里，然后用生成的JS外链进行替换。</p>
	<p>如您正在开发网页插件，想用自定义数据保存插件设置，可参考<a href="https://hu60.cn/q.php/bbs.topic.83603.html">数据存储API</a>。</p>
</div>

{/block}
