<div id="review_buttons" class="widget-page tp info-box" style="display: none">
	<span id="review_status" style="margin-right: 5px"></span>
	<input id="review_all_button" type="button" value="批量提交审核" onclick='hu60_review_all()' />
    <div id="review_result"></div>
</div>
<script>
	function hu60_update_review_status() {
		let pass = 0;
		let nopass = 0;
		let hasreason = 0;
		let radios = document.querySelectorAll('form.hu60_review input[type=radio]');
		for (let i=0; i<radios.length; i++) {
			let radio = radios[i];
			if (radio.checked) {
				if (radio.value == "1") {
					pass++;
				} else {
					nopass++;
				}
			}
		}
		let texts = document.querySelectorAll('form.hu60_review input[type=input]');
		for (let i=0; i<texts.length; i++) {
			let text = texts[i];
			if (text.value.length > 0) {
				hasreason++;
			}
		}
		let msgs = [];
		if (pass > 0) {
			msgs.push('通过 ' + pass);
		}
		if (nopass > 0) {
			msgs.push('未通过 ' + nopass);
		}
		if (nopass - hasreason > 0) {
			msgs.push('缺少理由 ' + (nopass - hasreason));
			document.querySelector('#review_all_button').onclick = function() {
				document.querySelector('#review_result').innerText = '审核未通过理由不能为空';
			};
		} else {
			document.querySelector('#review_all_button').onclick = hu60_review_all;
		}
		document.querySelector('#review_status').innerText = msgs.join('，');
	}
	async function hu60_review_all() {
		let forms = document.querySelectorAll('form.hu60_review');
		let datas = [];
		for (let i=0; i<forms.length; i++) {
			let form = forms[i];
			console.log(form);
			let data = {
				'contentId': form.dataset.contentId,
				'topicId': form.dataset.topicId,
			};
			$(forms[i]).serializeArray().forEach(x => data[x.name] = x.value);
			datas.push(data);
		}
        try {
            let result = await $.ajax({
                type: "POST",
                url: 'bbs.review-all.json',
                data: JSON.stringify(datas),
                dataType: 'json',
                contentType: "application/json; charset=utf-8",
            });
            if (result.errInfo) {
                throw result.errInfo;
            }

            let success = 0;
            let fail = 0;
            let reason = [];
            result.forEach(x => {
                if (x.success) success++; else fail++;
                if (x.errmsg) reason.push(x.errmsg);
            });

            if (fail == 0 && reason.length == 0) {
                document.querySelector('#review_result').innerText = '审核提交成功';
                setTimeout(function() {
                    location.reload();
                }, 500);
                return;
            }

            let msgs = [];
            if (success > 0) msgs.push('成功 ' + success);
            if (fail > 0) msgs.push('失败 ' + fail);
            if (reason.length > 0) msgs.push("失败原因：\n" + reason.join("\n"));
            document.querySelector('#review_result').innerText = msgs.join("\n");
        } catch (ex) {
            document.querySelector('#review_result').innerText = JSON.stringify(ex);
        }
	}
	if (document.querySelectorAll('form.hu60_review').length > 0) {
		document.querySelector('#review_buttons').style.display = 'block';
		let inputs = document.querySelectorAll('form.hu60_review input[type=radio], form.hu60_review input[type=input]');
		for (let i=0; i<inputs.length; i++) {
			inputs[i].addEventListener('input', function () {
				hu60_update_review_status();
			});
		}
		hu60_update_review_status();

		let submits = document.querySelectorAll('form.hu60_review');
		for (let i=0; i<submits.length; i++) {
			submits[i].onsubmit = function () {
				let pass = this.querySelector('input[type=radio]').checked;
				let comment = this.querySelector('input[type=input]');
				if (!pass && comment.value.length == 0) {
					comment.placeholder = '未通过理由不能为空';
					return false;
				}
				return true;
			};
		}
	}
</script>
