{extends file='tpl:comm.default'}

{block name='title'}
    修改联系信息
{/block}

{block name='style'}
    <link rel="stylesheet" type="text/css" href="{$PAGE->getTplUrl("js/cropper/cropper.css")|code}"/>
    <style>
        .cropper-box{
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        .Cropper-case{
            width: 512px;
            height: 512px;
            background-color: #EEE;
        }
        .Cropper-avatar{
            display: flex;
            justify-content: center;
            flex-direction:column;
        }
        .Cropper-case img {
            max-width: 100%; /* This rule is very important, please do not ignore this! */
        }
        #avatar{
            width: 128px;
            height: 128px;
        }
    </style>
{/block}

{block name='script'}
    <script src="{$PAGE->getTplUrl("js/cropper/cropper.js")}"></script>
    <script>
        $("#select-btn").on("click",function () {
            $("#select-file").click();
        });
        $("#select-file").on("change",function () {
            var reader = new FileReader();
            reader.onload = (function () {
                $("#cropper").cropper('destroy').attr("src",reader.result).cropper(
                    {
                        viewMode:1,
                        minCanvasWidth:256,
                        minCanvasHeight:256,
                        aspectRatio: 1,
                        crop: function(e) {
                            // Output the result data for cropping image.
                            $("#avatar").attr("src",$("#cropper").cropper("getCroppedCanvas",{
                                width:128,height:128
                            }).toDataURL());
                        }
                    }
                );
            });
            reader.readAsDataURL(this.files[0]);
        });
        $("#save-btn").on("click",function () {
            $("#cropper").cropper("getCroppedCanvas",{
                width:256,height:256
            }).toBlob(function (blob) {
                var formData = new FormData();

                formData.append('avatar', blob);

                $.ajax('{$CID}.{$PID}.{$BID}', {
                    method: "POST",
                    data: formData,
                    processData: false,
                    dataType:"json",
                    contentType: false,
                    success: function (result) {
                        if(result.error){
                            $(".text-notice").html(result.error);
                            return;
                        }else if(result.message){

                            $(".text-notice").html(result.message)
                        }else{
                            $(".text-notice").html("为止错误.");
                        }
                    },
                    error: function () {
                        $(".text-notice").html('Upload error');
                    }
                });
            },"image/jpeg");
        })
    </script>
{/block}

{block name='body'}
    <div class="cropper-box">
        <div class="cropper-bg Cropper-case" id="Cropper-case">
            <img src="" id="cropper">
        </div>
        <div class="Cropper-avatar">
            <div class="text-notice">
                <p class="failure">{$errMsg}</p>
                <p></p>
            </div>
            <div><img id="avatar" src="{$USER->getinfo('avatar.url')}"></div>
            <div>
                <input type="file" id="select-file" style="display: none;">
                <button id="select-btn">选择图片</button>
                <button id="save-btn">保存</button>
            </div>
        </div>
    </div>
{/block}
