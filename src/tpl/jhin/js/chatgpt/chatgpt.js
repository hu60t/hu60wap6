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

// 错误提示翻译
const errorMap = {
    'Too many requests in 1 hour. Try again later.' : "达到一小时对话次数上限，请过段时间再试，或尝试@[empty]其他机器人。\n\n已知机器人列表：\n* @[empty]ChatGPT\n* @[empty]罐子2号",
    'An error occurred. Either the engine you requested does not exist or there was another issue processing your request. If this issue persists please contact us through our help center at help.openai.com.' : 'ChatGPT接口报错，请重试。',
};

// 模型对应关系（仅限 ChatGPT Plus 付费用户）
const modelMap = {
    1 : 0, // @ChatGPT 1，对应第一个Default模型
    2 : 1, // @ChatGPT 2，对应第二个Legacy模型
};

/////////////////////////////////////////////////////////////

// 聊天框的CSS选择器
const chatBoxSelector = 'textarea.w-full.p-0';

// 发送按钮的CSS选择器
const sendButtonSelector = 'button.absolute.p-1';

// 正在输入动效（取代发送按钮）的CSS选择器
const replyNotReadySelector = 'div.text-2xl';

// 聊天回答的CSS选择器
// div.markdown 是正常回复
// div.text-gray-600 是错误信息（比如网络错误）
const chatReplySelector = 'div.markdown, div.text-gray-600';

// 左侧会话列表项的CSS选择器
const sessionListItemSelector = 'a.relative.rounded-md';

// 当前会话的CSS选择器
const currentSessionSelector = 'a.relative.rounded-md.bg-gray-800';

// 编辑、删除、确认、取消按钮的CSS选择器
const actionButtonSelector = 'button.p-1.hover\\:text-white';

// 会话名称编辑框的CSS选择器
const sessionNameInputSelector = 'input.text-sm.w-full';

// 新建会话按钮的CSS选择器
const newChatButtonSelector = 'a.flex-shrink-0.border';

// 模型下拉框的CSS选择器
const modelListBoxSelector = 'button.w-full.cursor-default';

// 模型列表项的CSS选择器
const modelListItemSelector = 'li.select-none.items-center';

// “Upgrade to Plus”按钮的CSS选择器
const upgradeToPlusSelector = 'span.gold-new-button.flex';

/////////////////////////////////////////////////////////////

// 用户自身的虎绿林uid（自动获取）
var hu60MyUid = null;

// 带sid的虎绿林URL（自动获取）
var hu60BaseUrl = null;

// 在切换会话前重命名当前会话
// 缓解重命名失败的方法
var wantRename = null;

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

// 选择模型
async function selectModel(modelIndex) {
    if (!document.querySelector(modelListBoxSelector) && document.querySelector(upgradeToPlusSelector)) {
        // 免费用户没有模型选择器
        return;
    }

    // 等待模型选择器出现
    for (let i=0; i<50 && !document.querySelector(modelListBoxSelector); i++) {
        await sleep(100);
    }

    let box = document.querySelector(modelListBoxSelector);
    if (!box) {
        // 找不到模型选择器
        return;
    }

    let models = document.querySelectorAll(modelListItemSelector);
    if (models.length < 2) {
        // 弹出模型下拉框
        box.click();
        await sleep(100);
        for (let i=0; i<10 && document.querySelectorAll(modelListItemSelector).length < 2; i++) {
            await sleep(100);
        }
        models = document.querySelectorAll(modelListItemSelector);
    }

    // 选择模型
    if (modelIndex < models.length) {
        console.log("selectModel", modelIndex, models[modelIndex].innerText);
        models[modelIndex].click();
        await sleep(100);
    }
}

// 创建新会话
async function newChatSession(modelIndex) {
    let sessionIndex = getSessions().length + 1;
    console.log('newChatSession', sessionIndex, modelIndex, 'begin');
    document.querySelector(newChatButtonSelector).click();
    // 等待新建完成
    let i = 0;
    do {
        await sleep(100);
        i++;
    } while (
        (  !document.querySelector(chatBoxSelector)
        || !document.querySelector(sendButtonSelector))
        && i < 100
    );

    // 选择模型
    await selectModel(modelIndex);

    console.log('newChatSession', sessionIndex, modelIndex, 'end');
}

// 删除当前会话
async function deleteSession() {
    let sessionNum = getSessions().length;

    let actionButtons = document.querySelectorAll(actionButtonSelector);
    if (!actionButtons[1]) {
        console.error('deleteSession', '找不到删除按钮');
        return;
    }
    actionButtons[1].click(); // 点击删除按钮
    await sleep(100);

    actionButtons = document.querySelectorAll(actionButtonSelector);
    if (!actionButtons[0]) {
        console.error('deleteSession', '找不到确认按钮');
        return;
    }
    actionButtons[0].click(); // 点击确认按钮

    // 等待删除完成
    for (let i=0; i<100 && getSessions().length >= sessionNum; i++) {
        await sleep(100);
    }
}

// 重命名会话
async function renameSession(newName) {
    // 刚开始创建标题的时候，当前会话获取不到
    for (let i=0; i<50 && !getCurrentSession(); i++) {
        await sleep(100);
    }

    // 重命名总是失败，多重试几次
    for (let i=0; i<3; i++) {
        getCurrentSession().click();
        await sleep(100);

        let actionButtons = document.querySelectorAll(actionButtonSelector);
        if (!actionButtons[0]) {
            console.error('renameSession', '找不到编辑按钮');
            return;
        }
        actionButtons[0].click(); // 点击编辑按钮
        await sleep(100);

        let nameInput = document.querySelector(sessionNameInputSelector);
        if (!nameInput) {
            console.error('renameSession', '找不到输入框');
            return;
        }

        // 交替改变新名称，以免毫无变化不尝试保存
        nameInput.value = newName.replace('-', (i==1) ? '.' : '-');
        await sleep(100);

        actionButtons = document.querySelectorAll(actionButtonSelector);
        if (!actionButtons[0]) {
            console.error('renameSession', '找不到确认按钮');
            return;
        }
        actionButtons[0].click(); // 点击确认按钮
        await sleep(100);
    }
}

// 获取会话列表
function getSessions() {
    return document.querySelectorAll(sessionListItemSelector);
}

// 查找会话
function findSession(name) {
    let sessions = getSessions();
    for (let i=0; i<sessions.length; i++) {
        // 重命名时会交替使用.和-，有可能保存上的是.而非-
        if (sessions[i].innerText.replace('.', '-') == name) {
            return sessions[i];
        }
    }
    return null;
}

// 获取当前session
function getCurrentSession() {
    return document.querySelector(currentSessionSelector);
}

// 获取当前session的名称
function getSessionName() {
    let session = getCurrentSession();
    if (session) {
        // 重命名时会交替使用.和-，有可能保存上的是.而非-
        return session.innerText.replace('.', '-');
    }
    return null;
}

// 切换会话前重命名当前会话
// 缓解重命名失败的方法
async function renameWant() {
    if (wantRename !== null) {
        await renameSession(wantRename);
        wantRename = null;
    }
}

// 切换会话
async function switchSession(name, modelIndex) {
    let session = findSession(name);
    if (!session) {
        await renameWant();
        return await newChatSession(modelIndex);
    }

    if (getCurrentSession() == session) {
        if (document.querySelector(chatBoxSelector)
         && document.querySelector(sendButtonSelector)) {
            // 无需切换
            return;
        } else {
            // 找不到发言框，可能出错了，尝试来回切换标签页
            // 先重命名当前会话
            await renameWant();
            // 不发言不会保留新建的会话，后续代码会尝试切换回当前会话
            await newChatSession(modelIndex);
        }
    } else {
        // 切换前先重命名当前会话
        await renameWant();
    }

    console.log('switchSession', name, 'begin');
    session.click();

    // 等待切换完成
    let i = 0;
    do {
        await sleep(100);
        i++;
    } while (
        (getSessionName() != name
        || !document.querySelector(chatBoxSelector)
        || !document.querySelector(sendButtonSelector))
        && i < 100
    );

    // 找不到发言框或发送按钮，当前会话可能出错
    if (!document.querySelector(chatBoxSelector) || !document.querySelector(sendButtonSelector)) {
        console.warn('找不到发言框或发送按钮，尝试删除会话', name);
        await deleteSession();
        return await newChatSession(modelIndex);
    }

    console.log('switchSession', name, 'end');
}

function makeSessionName(uid, modelIndex) {
    return uid + '-' + modelIndex;
}

// 发送聊天信息
async function sendText(text, uid, modelIndex) {
    await switchSession(makeSessionName(uid, modelIndex), modelIndex);

    let chatBox = document.querySelector(chatBoxSelector);
    let sendButton = document.querySelector(sendButtonSelector);

    chatBox.value = text;
    sendButton.click();
}

// 执行聊天信息中的指令
async function sendRequest(text, uid) {
    console.log('sendRequest', '@#'+uid, text);

    // 去除待审核提示
    text = text.trim().replace(/^发言待审核，仅管理员和作者本人可见。/s, '').trim();

    // 分割指令
    // 示例：
    //  @ChatGPT，你好
    //  @ChatGPT 2，你好
    let parts = text.match(/^\s*@[^，,：:\s]+(?:\s+(\d+))?[，,：:\s]+(.*)$/s);

    let modelIndex = modelMap[1];

    if (parts) {
        let cmd = parts[1];
        text = parts[2];
    
        if (undefined !== cmd && undefined !== modelMap[Number(cmd)]) {
            modelIndex = modelMap[Number(cmd)];
        }
    }

    await sendText(text, uid, modelIndex);
    return modelIndex;
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
                    var lang = node.querySelector('span')?.textContent || ''; // lang span可能不存在
                    var code = node.querySelector('code.hljs').textContent;
                    return (
                        '\n\n' + options.fence + lang + '\n' +
                            code.replace(/[\r\n]+$/s, '') +
                        '\n' + options.fence + '\n\n'
                    )
                }
            });
        } else if (turndownService) {
            console.error("找不到 turndownPluginGfm，无法处理复杂Markdown排版。\n请确认 " + turndownGfmJsUrl + " 是否正常加载。");
        }
    } catch (ex) {
        console.error('turndown 加载失败', ex);
    }

    // 获取内容DOM
    let reply = null;
    // 等待内容出现
    let i = 0;
    do {
        reply = Array.from(document.querySelectorAll(chatReplySelector)).at(-1);
        i++;
    } while (i<50 && !reply && !await sleep(100));
    // 如果内容不为空，至少会有一个Text子节点
    if (!reply || !reply.childNodes) {
        return "读取回复出错，请重试。\n@老虎会游泳，可能需要检查机器人代码问题。";
    }

    // 用插件 html 转 markdown
    if (turndownService) {
        try {
            return turndownService.turndown(reply);
        } catch (ex) {
            console.error('turndown 转换失败', ex);
        }
    }

    // 插件加载或转换失败，手动 html 转 markdown
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
    return !document.querySelector(replyNotReadySelector);
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
    replyText = errorMap[replyText] || replyText; // 翻译错误提示

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
        while (!isFinished()) {
            await sleep(100);
        }

        let modelIndex = await sendRequest(text, uid);

        // 等待回答完成
        do {
            await sleep(100);
        } while (!isFinished());

        let replyText = await readReply();
        let response = await replyTopic(uid, replyText, topicObject);
        console.log('success:', response.type == 'opaqueredirect');

        // 重命名会话
        let sessionName = makeSessionName(uid, modelIndex);
        if (getSessionName() != sessionName) {
            await renameSession(sessionName);
            wantRename = sessionName;
        }
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
            while (!isFinished()) {
                await sleep(100);
            }

            let atInfo = await readAtInfo();
            // @消息是后收到的在前面，所以从后往前循环，先发的先处理
            for (let i = atInfo.msgList.length - 1; i>=0; i--) {
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
