////////////////// 虎绿林 ChatGPT 聊天机器人 //////////////////

/**************************************************************
使用方法：
1. （可选）付费订阅 ChatGPT Plus，以便消除时不时弹出的浏览器安全检查。
2. 打开 https://chat.openai.com/chat 并登录。
3. 打开开发者控制台（F12）。
4. 把以下代码粘贴到控制台，回车运行：

const hu60Domain = 'https://hu60.cn';
var script = document.createElement("script");
script.src = hu60Domain + '/tpl/jhin/js/chatgpt/chatgpt.js';
document.head.appendChild(script);

5. 在弹出框里输入虎绿林用户名和密码。
6. 控制台打印“虎绿林ChatGPT机器人已启动”即启动成功，保持页面打开即可。
**************************************************************/

// 虎绿林URL
const hu60Url = hu60Domain + '/q.php/';

// https://github.com/mixmark-io/turndown
// 老虎会游泳修改了 collapseWhitespace 函数以保留所有空白和换行
const turndownJsUrl = hu60Domain + '/tpl/jhin/js/chatgpt/turndown-tigermod.js';

// https://github.com/mixmark-io/turndown-plugin-gfm
const turndownGfmJsUrl = hu60Domain + '/tpl/jhin/js/chatgpt/turndown-plugin-gfm.js';

/////////////////////////////////////////////////////////////

// 聊天框的CSS选择器
const chatBoxSelector = 'textarea.w-full.p-0';

// 发送按钮的CSS选择器
const sendButtonSelector = 'button.absolute.p-1';

// 正在输入动效（取代发送按钮）的CSS选择器
const replyNotReadySelector = 'div.text-2xl';

// 聊天回答的CSS选择器
const chatReplySelector = 'div.markdown, div.text-gray-600';

// 左侧会话列表项的CSS选择器
const sessionListItemSelector = 'a.relative.rounded-md';

// 当前会话的CSS选择器
const currentSessionSelector = 'a.relative.rounded-md.bg-gray-800';

// 新建会话按钮的CSS选择器
const newChatButtonSelector = 'a.flex-shrink-0.border';

// 默认会话（从0开始计数）
const defaultSession = 0;

/////////////////////////////////////////////////////////////

// 用户自身的虎绿林uid（自动获取）
var hu60MyUid = null;

// 带sid的虎绿林URL（自动获取）
var hu60BaseUrl = null;

/////////////////////////////////////////////////////////////

// 休眠指定的毫秒数
// 用法：await sleep(1000)
const sleep = ms => new Promise(r => setTimeout(r, ms));

// 加载外部js
function loadScript(url) {
    var script = document.createElement("script");
    script.src = url;
    document.head.appendChild(script);
}

// 发送聊天信息
async function sendText(text) {
    let chatBox = document.querySelector(chatBoxSelector);
    let sendButton = document.querySelector(sendButtonSelector);

    if (!chatBox || !sendButton) {
        // 找不到聊天框或发送按钮，可能之前发生了网络错误，尝试来回切换会话解决
        let sessions = document.querySelectorAll(sessionListItemSelector);
        let currentSession = document.querySelector(currentSessionSelector);

        // 寻找当前会话索引
        let currentIndex = 0;
        for (; currentIndex<sessions.length && sessions[currentIndex] != currentSession; currentIndex++);

        await switchSession((currentIndex + 1) % sessions.length);
        await switchSession(currentIndex);
    }

    chatBox.value = text;
    sendButton.click();
}

// 创建新会话
async function newChatSession() {
    let sessionIndex = document.querySelectorAll(sessionListItemSelector).length;
    console.log('newChatSession', sessionIndex, 'begin');
    document.querySelector(newChatButtonSelector).click();
    // 等待新建完成
    do {
        await sleep(100);
    } while (
        !document.querySelector(chatBoxSelector) ||
        !document.querySelector(sendButtonSelector)
    );
    console.log('newChatSession', sessionIndex, 'end');
}

// 切换会话
async function switchSession(sessionIndex) {
    let sessions = document.querySelectorAll(sessionListItemSelector);
    if (sessions.length < 1 || (sessionIndex == 1 && sessions.length < 2)) {
        return await newChatSession();
    }
    if (sessions[sessionIndex]) {
        if (document.querySelector(currentSessionSelector) == sessions[sessionIndex]) {
            return;
        }

        console.log('switchSession', sessionIndex, 'begin');
        sessions[sessionIndex].click();

        // 等待切换完成
        do {
            await sleep(100);
            sessions = document.querySelectorAll(sessionListItemSelector);
        } while (
            document.querySelector(currentSessionSelector) != sessions[sessionIndex]
            || !document.querySelector(chatBoxSelector)
            || !document.querySelector(sendButtonSelector)
        );
        console.log('switchSession', sessionIndex, 'end');
    }
}

// 执行聊天信息中的指令
async function sendRequest(text) {
    console.log('sendRequest', text);

    // 去除待审核提示
    text = text.trim().replace(/^发言待审核，仅管理员和作者本人可见。/s, '').trim();

    // 分割指令
    // 示例：
    //  @ChatGPT，你好
    //  @ChatGPT 2，你好
    let parts = text.match(/^\s*@[^，,：:\s]+(?:\s+([^，,：;\s]+))?[，,：:\s]+(.*)$/s);

    if (!parts) {
        return await sendText(text);
    }

    let cmd = parts[1];
    text = parts[2];

    if (cmd == undefined) {
        // 使用默认会话
        await switchSession(defaultSession);
    } else if (/^\d+$/.test(cmd)) {
        // 切换会话
        // 示例，切换到会话2：
        //  @ChatGPT 2，你好
        await switchSession(Number(cmd - 1));
    }

    return await sendText(text);
}

// 读取响应
async function readReply() {
    // 加载 html 转 markdown 插件
    let turndownService = null;
    try {
        if (typeof TurndownService == 'function') {
            turndownService = new TurndownService({
                'headingStyle': 'atx',
            });
        } else {
            console.error("找不到 TurndownService，无法处理复杂Markdown排版。\n请确认 " + TurndownService + " 是否正常加载。");
        }

        // 加载 github flavored markdown 插件
        if (turndownService && typeof turndownPluginGfm == 'object') {
            turndownService.use(turndownPluginGfm.tables);
            turndownService.use(turndownPluginGfm.taskListItems);

            // 删除线
            // turndownPluginGfm.strikethrough 实现的不正确，虎绿林只支持 ~~删除线~~，不支持 ~删除线~
            turndownService.addRule('strikethrough', {
                filter: ['del', 's', 'strike'],
                replacement: function (content) {
                    return '~~' + content + '~~';
                }
            });

            // 代码高亮
            turndownService.addRule('highlightedCodeBlock', {
                filter: function (node) {
                    return node.nodeName === 'PRE' && node.querySelector('code.hljs');
                },
                replacement: function (content, node, options) {
                    var lang = node.querySelector('span').textContent;
                    var code = node.querySelector('code.hljs').textContent;
                    return (
                        '\n\n' + options.fence + lang + '\n' +
                            code.replace(/[\r\n]+$/s, '') +
                        '\n' + options.fence + '\n\n'
                    )
                }
            });
        } else {
            console.error("找不到 turndownPluginGfm，无法处理复杂Markdown排版。\n请确认 " + turndownGfmJsUrl + " 是否正常加载。");
        }
    } catch (ex) {
        console.error('turndown 加载失败', ex);
    }

    let reply = Array.from(document.querySelectorAll(chatReplySelector)).at(-1);
    if (!reply) {
        return '读取回复出错';
    }

    // 用插件 html 转 markdown
    if (turndownService) {
        try {
            return turndownService.turndown(reply);
        } catch (ex) {
            console.error('turndown 转换失败', ex);
        }
    }

    ///////// 插件加载或转换失败，手动 html 转 markdown /////////

    if (!reply.childNodes) {
        // 内容为错误提示
        return reply.innerText;
    }

    let lines = [];
    reply.childNodes.forEach(x => {
        if (x.tagName == 'PRE') { // 代码
            let lang = x.querySelector('span').innerText;
            let code = x.querySelector('code').innerText.replace(/[\r\n]+$/s, '');
            lines.push("\n```" + lang + "\n" + code + "\n```\n");
        } else { // 正文
            lines.push(x.innerText);
        }
    });
    return lines.join("\n\n");
}

// 判断响应是否结束
function isFinished() {
    return document.querySelector(replyNotReadySelector) == null;
}

// 读取@消息
async function readAtInfo() {
    let response = await fetch(hu60BaseUrl + 'msg.index.@.no.json?_origin=*&_content=json', {
        redirect: "manual" // 不自动重定向
    });
    if (response.type == 'opaqueredirect') {
        // 登录失效，要求重新登录
        await login();
        return await readAtInfo();
    }
    return await response.json();
}

// 读取帖子内容
async function readTopicContent(path) {
    let url = hu60BaseUrl + path.replace('{$BID}', 'json').replace('?', '?_origin=*&_content=text&pageSize=1&');
    let response = await fetch(url);
    return await response.json();
}

// 回复帖子
async function replyTopic(uid, replyText, topicObject) {
    let content = "<!md>\n@#" + uid + "，" + replyText;
    console.log('replyTopic', content);

    let url = null;
    if (topicObject.tMeta) { // 帖子
        url = 'bbs.newreply.' + encodeURIComponent(topicObject.tContents[0].topic_id) + '.json';
    } else { // 聊天室
        url = 'addin.chat.' + encodeURIComponent(topicObject.chatRomName) + '.json';
    }

    let formData = new FormData();
    formData.append('content', content);
    formData.append('token', topicObject.token);
    formData.append('go', '1');

    let response = await fetch(hu60BaseUrl + url + '?_origin=*', {
        body: formData,
        method: "post",
        redirect: "manual" // 不自动重定向
    });
    return response;
}

// 回复@信息
async function replyAtInfo(info) {
    try {
        let uid = info.byuid;
        let url = info.content[0].url;

        // 防止自己和自己对话
        if (uid == hu60MyUid || uid < 1) {
            return;
        }

        console.log('replyAtInfo', hu60Url + url.replace('{$BID}', 'html'));

        let topicObject = await readTopicContent(url);
        let text = null;
        if (topicObject.tContents) {
            text = topicObject.tContents[0].content;
        } else {
            text = topicObject.chatList[0].content;
        }

        // 等待现有任务完成
        do {
            await sleep(500);
        } while (!isFinished());

        await sendRequest(text);

        // 等待回答完成
        do {
            await sleep(500);
        } while (!isFinished());

        let replyText = await readReply();
        let response = await replyTopic(uid, replyText, topicObject);
        console.log('success:', response.type == 'opaqueredirect');
    } catch (ex) {
        console.error(ex);
    }
}

// 登录虎绿林
async function login() {
    try {
        console.log('登录虎绿林');

        if (!localStorage.hu60User || !localStorage.hu60Pwd) {
            localStorage.hu60User = prompt("虎绿林用户名：");
            localStorage.hu60Pwd = prompt("虎绿林密码：");
        }

        let formData = new FormData();
        formData.append('type', '1'); // 用户名登录
        formData.append('name', localStorage.hu60User);
        formData.append('pass', localStorage.hu60Pwd);
        formData.append('go', '1');

        let response = await fetch(hu60Url + 'user.login.json?_origin=*', {
            body: formData,
            method: "post",
            redirect: "manual" // 不自动重定向
        });
        let result = await response.json();
        if (!result.success) {
            throw result.notice;
        }

        hu60BaseUrl = hu60Url + result.sid + '/';
        hu60MyUid = result.uid;
    } catch (ex) {
        console.log(ex);
        alert('登录失败：' + ex);

        localStorage.hu60User = null;
        localStorage.hu60Pwd = null;

        return await login();
    }
}

// 运行机器人
async function run() {
    loadScript(turndownJsUrl);
    loadScript(turndownGfmJsUrl);

    await login();
    console.log('虎绿林ChatGPT机器人已启动');

    while (true) {
        try {
            // 浏览器用户可能直接输入了问题，等待回答完成
            if (!isFinished()) {
                do {
                    await sleep(500);
                } while (!isFinished());
            }

            let atInfo = await readAtInfo();
            for (let i = 0; i < atInfo.msgList.length; i++) {
                await replyAtInfo(atInfo.msgList[i]);
            }
            await sleep(1000);
        } catch (ex) {
            console.error(ex);
            await sleep(1000);
        }
    }
}

run();
