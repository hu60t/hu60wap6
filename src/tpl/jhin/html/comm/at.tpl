<script>
    /* 将内容插入编辑框obj中光标所在的位置 */
    /* 感谢 @666 (uid: 16651) 编写了该函数 */
    /* https://hu60.net/q.php/bbs.topic.86665.html */
    function hu60_insert_text(obj, str) {  
        if (document.selection) {  
            var sel = document.selection.createRange();  
            sel.text = str;  
        } else if (typeof obj.selectionStart === 'number' && typeof obj.selectionEnd === 'number') {  
            var startPos = obj.selectionStart,  
                endPos = obj.selectionEnd,  
                cursorPos = startPos,  
                tmpStr = obj.value;  
            obj.value = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);  
            cursorPos += str.length;  
            obj.selectionStart = obj.selectionEnd = cursorPos;  
        } else {  
            obj.value += str;  
        }  
    }

    /* 将@标记插入光标位置，并使指定对象变色 */
    function atAdd(uid, that) {
        that.style.color = "#FFA500";
        var nr = document.getElementById("content");
        hu60_insert_text(nr, '@'+uid+'，');
    }
</script>
