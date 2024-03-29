////////////////// 虎绿林 ChatGPT 聊天机器人 //////////////////

/**************************************************************
使用方法：
1. 使用最新版的Chrome谷歌浏览器或Firefox火狐浏览器，不要使用QQ浏览器、360浏览器等，不保证兼容。
2. 安装油猴插件：https://www.tampermonkey.net/
3. 在油猴里添加新脚本，粘贴如下代码并保存：

// ==UserScript==
// @name         虎绿林ChatGPT机器人
// @namespace    https://hu60.cn/
// @version      1.0
// @description  把ChatGPT接入hu60wap6网站程序
// @author       老虎会游泳
// @match        https://chat.openai.com/*
// @icon         https://hu60.cn/favicon.ico
// @grant        none
// ==/UserScript==

document.hu60VConsole = false; // 是否显示调试控制台，false：隐藏；true：显示。
document.hu60User = ''; // 虎绿林用户名
document.hu60Pwd = ''; // 虎绿林密码
document.hu60AdminUids = [1, 19346, 15953]; // 机器人管理员uid，管理员可以发“@ChatGPT，刷新页面”来重启机器人
document.hu60Domain = 'https://hu60.cn'; // 如果要对接其他网站，请修改此处的域名（必须是https的否则连不上）
var script = document.createElement("script");
script.src = document.hu60Domain + '/tpl/jhin/js/chatgpt/chatgpt.js?r=' + (new Date().getTime());
document.head.appendChild(script);

4. 打开 https://chat.openai.com/ 并登录。
5. 在来到 https://chat.openai.com/chat 页面时，会弹出输入虎绿林用户名密码的提示框。
   如果你要把机器人接入虎绿林，请注册一个新帐号。**使用现有帐号运行机器人将被删帖或禁言**。
   输入新帐号用户名密码后，机器人即启动，保持页面不要关闭。
   机器人会使用你在此处输入的帐号与其他用户进行对话，在虎绿林用其他帐号`@该帐号`即可尝试对话。
   注意，使用该帐号自己`@自己`是不会有反应的，必须用另一个账号来和机器人对话。
6. 建议按F12打开开发者控制台（按F12，点“控制台”或“Console”），可以看到机器人的运行情况，而且好像能提升机器人运行的稳定性。
7. 如何切换登录的帐号？按F12打开开发者工具，点“控制台”或“Console”，然后输入以下代码并回车：
    login(true)
   将会重新弹出用户名密码输入框。
8. 也可以把用户名密码填在油猴脚本里，这样就不用在对话框里输入了。

### 如何把机器人接入其他类型的网站？

你可以在油猴脚本的末尾添加一个自定义主循环，用于把机器人接入其他类型的网站。以下是一个例子：

document.run = async function() {
    while (true) {
        try {
            // 访问你的网站获取要发给ChatGPT的内容
            // 网站必须是https的，否则连不上。
            // 此外网站还必须设置 Access-Control-Allow-Origin: * 头信息，否则也连不上。
            let response = await fetch('https://example.com/my-message.php');

            // 假设获取到的信息是JSON，把它转换成JSON对象
            // 网站必须设置 content-type: application/json 头信息，否则转换会失败。
            let messages = response.json();

            // 假设JSON结构是这样：
            // {"data": [
            //    {"uid":3, "text":"@ChatGPT，你好"},
            //    {"uid":2, "text":"@ChatGPT，我有一个问题"},
            //    {"uid":1, "text":"@ChatGPT，刷新页面"},
            // ]}
            let exceptionCount = 0;
            for (let i=0; i<messages.data.length; i++) {
                // 要发给ChatGPT的话，开头包含的“@机器人名称，”会被后续流程自动去除。
                // 开头写“@机器人名称 2，”可以选择第二个ChatGPT模型（Legacy模型，仅限ChatGPT Plus用户）。
                let text = messages.data.text;

                // 用户id，可以是字符串，所以给出用户名也是可以的。
                let uid = messages.data.uid;

                try {
                    // 把对话发给ChatGPT
                    // 返回的 modelIndex 是为对话选择的模型id（从0开始编号）
                    // 模型id和序号的对应关系见 chatgpt.js 里的 modelMap 变量
                    let modelIndex = await sendRequest(text, uid);

                    // 从ChatGPT读取回复
                    let replyText = await readReply();

                    // 发送回复到你的网站
                    // 创建一个POST表单
                    let formData = new FormData();
                    formData.append('token', '用于用户身份验证的密钥');
                    formData.append('reply', replyText); // 回复内容

                    // 提交POST表单
                    // 网站必须是https的，否则连不上。
                    // 此外网站还必须设置 Access-Control-Allow-Origin: * 头信息，否则也连不上。
                    let response = await fetch('https://example.com/my-reply.php', {
                        body: formData,
                        method: "post",
                        redirect: "manual" // 不自动重定向
                    });

                    // 在控制台打印提交结果
                    if (response.type == 'opaqueredirect') {
                        console.log('提交后收到重定向（目标网址未知，根据标准，浏览器不告诉我们），不清楚提交是否成功');
                    } else {
                        let result = await response.text();
                        console.log('提交结果', result);
                    }

                    // 避免操作太快
                    await sleep(100);
                } catch (ex) {
                    exceptionCount++;  // 统计异常次数
                    console.error(ex); // 打印异常到控制台
                    await sleep(1000); // 异常后等久一点
                }

                // 重命名会话
                await renameWant();
            }

            // 执行管理员命令（比如“刷新页面”）
            await runAdminCommand();

            // 异常太多，自动刷新页面
            if (exceptionCount > 0 && exceptionCount >= messages.data.length) {
                refreshPage();
                await sleep(30000); // 防止实际刷新前执行到后面的代码
            }

            // 限制拉取信息的速度，避免对自己的网站造成CC攻击
            await sleep(1000);
        } catch (ex) {
            console.error(ex);
            await sleep(1000);
        }
    }
}

**************************************************************/

// 与之前的启动方式保持兼容
if (typeof hu60Domain != 'undefined') {
    document.hu60Domain = hu60Domain;
}

// 虎绿林URL
const hu60Url = document.hu60Domain + '/q.php/';

// https://github.com/mixmark-io/turndown
// 老虎会游泳修改了 collapseWhitespace 函数以保留所有空白和换行
const turndownJsUrl = document.hu60Domain + '/tpl/jhin/js/chatgpt/turndown-tigermod.js';

// https://github.com/mixmark-io/turndown-plugin-gfm
const turndownGfmJsUrl = document.hu60Domain + '/tpl/jhin/js/chatgpt/turndown-plugin-gfm.js';

// 虚拟控制台
const vConsoleJsUrl = document.hu60Domain + '/tpl/jhin/js/chatgpt/vconsole.js?r=' + (new Date().getTime());

/////////////////////////////////////////////////////////////

// 错误提示翻译
const errorMap = {
    'Your authentication token has expired. Please try signing in again.':
        "ChatGPT机器人已掉线，请等待 @#1 手动重新登录。",

    'Too many requests in 1 hour. Try again later.':
        "达到OpenAI设置的一小时对话次数上限，请过段时间再试。",

    'An error occurred. Either the engine you requested does not exist or there was another issue processing your request. If this issue persists please contact us through our help center at help.openai.com.':
        "ChatGPT接口报错（会话丢失），请重试。",

    // the request ID 后面是一串随机值，所以没有粘贴过来。匹配时回复将截短到错误提示的最大长度，只要保证该错误提示是最长的，就不需要处理随机ID问题。
    'The server had an error while processing your request. Sorry about that! You can retry your request, or contact us through our help center at help.openai.com if the error persists. (Please include the request ID':
        "ChatGPT接口报错（服务器出错），请重试。",

    'An error occurred. If this issue persists please contact us through our help center at help.openai.com.':
        "ChatGPT接口报错（客户端错误），请重试。",

    'Only one message at a time. Please allow any other responses to complete before sending another message, or wait one minute.':
        "ChatGPT接口报错（并发受限），请稍后重试。",

    'Something went wrong':
        "ChatGPT接口报错（抛出异常），请重试。",

    'network error':
        "连接断开，回复不完整。",
    
    'NetworkError when attempting to fetch resource.':
        "网络错误，读取回复失败",

    'The message you submitted was too long, please reload the conversation and submit something shorter.':
        "内容超过ChatGPT长度限制，请缩短。当前会话已丢失。",
    
    'Something went wrong. If this issue persists please contact us through our help center at help.openai.com.':
        "ChatGPT接口报错（未知错误），请重试。",
    
    'GPT-4 currently has a cap of 25 messages every 3 hours. Expect significantly lower caps, as we adjust for demand.':
        "读取回复出错，请重试。每天第一次和机器人对话时经常发生这种错误，通常再试一次就会好。",
    
    'READ_REPLY_FAILED':
        "读取回复出错，请重试。每天第一次和机器人对话时经常发生这种错误，通常再试一次就会好。",
};

// 错误提示文本的最大长度
const errorMaxLen = Math.max(...Object.keys(errorMap).map(x => x.length));

// 模型对应关系（仅限 ChatGPT Plus 付费用户）
const modelMap = {
    1 : 'text-davinci-002-render-sha', // @ChatGPT 1，对应GPT-3.5模型
    2 : 'gpt-4-browsing',              // @ChatGPT 2，对应GPT-4网页浏览模型
    3 : 'gpt-4-plugins',               // @ChatGPT 3，对应GPT-4插件模型
    4 : 'gpt-4',                       // @ChatGPT 4，对应GPT-4默认模型
    5 : 'gpt-4-code-interpreter',      // @ChatGPT 5，对应GPT-4高级数据分析模型
};

/////////////////////////////////////////////////////////////

// 聊天框的CSS选择器
const chatBoxSelector = 'textarea.w-full.m-0';

// 发送按钮的CSS选择器
const sendButtonSelector = 'button.absolute.p-0\\.5';

// 正在输入动效（三个点）和加载中动效（转圈）的CSS选择器
const replyNotReadySelector = 'button.border-gizmo-gray-950.p-1';

// 顶部模型名称的CSS选择器
const modelNameSelector = 'div.rounded-xl.text-lg.radix-state-open\\:bg-gray-50';

// 停止生成/重新生成按钮
const stopOrRegenButtonSelector = 'button.btn-neutral.border-0';

// 聊天内容（包括提问与回复）的CSS选择器
const chatLineSelector = 'div.flex-col.items-start';

// 聊天回答的CSS选择器
// div.markdown 是正常回复
// div.text-gray-600.border-red-500 是错误信息（比如网络错误）
const chatReplySelector = 'div.markdown, div.text-gray-600.border-red-500';

// 左侧会话列表项的CSS选择器
const sessionListItemSelector = 'a.p-2.rounded-lg';

// 当前会话的CSS选择器
const currentSessionSelector = 'a.p-2.rounded-lg.bg-token-surface-primary';

// 菜单按钮
const actionMenuSelector = 'button.right-0.hover\\:text-token-text-secondary';

// 编辑、删除、确认、取消按钮的CSS选择器
const actionButtonSelector = 'div.py-2\\.5.radix-disabled\\:opacity-50';

// 确认删除按钮的CSS选择器
const deleteButtonSelector = 'button.relative.btn-danger';

// 会话名称编辑框的CSS选择器
const sessionNameInputSelector = 'input.text-sm.w-full';

// 新建会话按钮的CSS选择器
const newChatButtonSelector = 'div.overflow-hidden.text-token-text-primary';

// 模型下拉框的CSS选择器
const modelListBoxSelector = 'ul.flex.w-full.list-none';

// 模型列表项的CSS选择器
const modelListItemSelector = 'div.items-center.rounded-lg';

// “Upgrade to Plus”按钮的CSS选择器
const upgradeToPlusSelector = 'a.px-3.py-1.gizmo\\:px-1';

// 会话列表“Show more”按钮的CSS选择器
const showMoreButtonSelector = 'button.m-auto.mb-2';

/////////////////////////////////////////////////////////////

// 在线机器人列表（自动获取）
var hu60OnlineBot = {};

// 用户自身的虎绿林uid（自动获取）
var hu60MyUid = null;

// 虎绿林sid
var hu60Sid = null;

// 带sid的虎绿林URL（自动获取）
var hu60BaseUrl = null;

// 在切换会话前重命名当前会话
// 缓解重命名失败的方法
var wantRename = null;

// 上次会话的名称
// 在会话历史记录功能不可用时减少不必要的新建会话
var lastSessionName = null;

// 回复结束时间
// 在回复结束2秒后重命名会话，
// 以防ChatGPT自动重命名会话导致我们的名称保存失败。
var replyFinishTime = 0;

// 管理员想要刷新页面
var wantRefresh = false;

// 新会话标识
var isNewSession = false;

// 模型名称
var modelName = null;

// 空白发言标识
var isTextEmpty = false;

// 命令短语回复
var commandPhraseReply = null;

// 指定回复中的代码高亮UBB
var replyCodeFormat = null;
var replyCodeFormatOpts = '';

// 重试对话内容缓存
var retryChatTexts = {};

// 上一条回复，用于防止获取到重复回复
var lastReply = null;

/////////////////////////////////////////////////////////////

// 命令短语
const commandPhrases = {
    '结束会话' : async function(text, uid, modelIndex) {
        if (isNewSession) {
            commandPhraseReply = '会话未开始';
            isNewSession = false;
            wantRename = null;
        } else {
            commandPhraseReply = '会话已结束';
            await deleteSession();
        }
    },
    '刷新页面' : async function(text, uid, modelIndex) {
        if (!document.hu60AdminUids || !document.hu60AdminUids.includes(uid)) {
            commandPhraseReply = '您不是管理员，无法进行该操作';
            return;
        }
        commandPhraseReply = '即将刷新页面';
        wantRefresh = true;
        wantRename = null;
    },
    '重试' : async function(text, uid, modelIndex) {
        text = retryChatTexts[uid];
        if (text === undefined || text === '重试') {
            commandPhraseReply = '找不到可重试的发言';
            return;
        }
        await sendText(text, uid, modelIndex);
    }
};

// 执行管理员命令
// 为什么要定义成单独的函数？因为刷新操作需要在发送回复给管理员后再执行，
// 否则页面刷新了就没办法发送回复了。
async function runAdminCommand() {
    // 重命名对话
    await renameWant();

    // 刷新页面
    if (wantRefresh) {
        refreshPage();
        await sleep(30000); // 防止实际刷新前执行到后面的代码
        wantRefresh = false;
    }
}

/////////////////////////////////////////////////////////////

// 刷新页面
function refreshPage() {
    console.error('刷新页面', Error().stack);
    location.reload();
}

// 跳转页面
function loadPage(url) {
    console.error('跳转页面', url, Error().stack);
    location.href = url;
}

// 休眠指定的毫秒数
// 用法：await sleep(1000)
const sleep = ms => new Promise(r => setTimeout(r, ms));

// 加载外部js
function loadScript(url) {
    var script = document.createElement("script");
    script.src = url;
    document.head.appendChild(script);
}

// Changing a React Input Value from Vanilla Javascript
// 通过原生js修改React输入框的值
// From: <https://chuckconway.com/changing-a-react-input-value-from-vanilla-javascript/>
function setNativeValue(element, value) {
    let lastValue = element.value;
    element.value = value;
    let event = new Event("input", { target: element, bubbles: true });
    // React 15
    event.simulated = true;
    // React 16
    let tracker = element._valueTracker;
    if (tracker) {
        tracker.setValue(lastValue);
    }
    element.dispatchEvent(event);
}

// 模拟点击
function sendClickEvent(element) {
    let event = new Event("click", { target: element, bubbles: true });
    event.simulated = true;
    element.dispatchEvent(event);
}

/////////////////////////////////////////////////////////////

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

    let models = Array.from(box.querySelectorAll(modelListItemSelector));

    // 检查模型URL参数是否匹配
    if (new URLSearchParams(location.search).get('model') === modelIndex) {
        // 模型匹配，直接返回
        console.log("selectModel", modelIndex, location.href);

        // 等待插件加载
        if (modelIndex.indexOf('plugin') >= 0) {
            console.log("等待插件加载……");
            await sleep(3000);
        }

        return;
    }

    // 选择模型
    if (models.length >= 2) {
        // 除了GPT-3.5应该选1，其他都应该选2
        let model = (modelIndex == 'text-davinci-002-render-sha') ? models[0] : models[1];
        model.click();
        await sleep(100);
        console.log("selectModel", modelIndex, location.href, model.innerText);
    }

    // 检查模型URL参数是否匹配
    let urlParams = new URLSearchParams(location.search);
    let currentModel = urlParams.get('model');
    if (currentModel !== modelIndex) {
        // 不匹配，跳转到对应URL
        // 因为展开GPT-4的模型下拉框很难，所以改用URL跳转选择模型
        urlParams.set('model', modelIndex);
        let modelUrl = '?' + urlParams;
        console.log('跳转到模型URL', modelUrl, '当前模型', currentModel, location.href);
        loadPage(modelUrl);
        await sleep(30000);
    }
}

// 创建新会话
async function newChatSession(name, modelIndex) {
    let sessionIndex = getSessions().length + 1;
    console.log('newChatSession', sessionIndex, modelIndex, 'begin');

    // 只有模型不匹配时才有必要点击新会话按钮
    if (new URLSearchParams(location.search).get('model') !== modelIndex) {
        document.querySelector(newChatButtonSelector).click();
    }

    // 等待加载完成
    let i = 0;
    do {
        await sleep(100);
        i++;
    } while (
        (  !document.querySelector(chatBoxSelector)
        || !document.querySelector(sendButtonSelector))
        && i < 100
    );
    // 再多等一会儿，防止意外
    await sleep(100);

    // 选择模型
    await selectModel(modelIndex);

    isNewSession = true;
    wantRename = name;
    console.log('newChatSession', sessionIndex, modelIndex, 'end');
}

// 展开操作菜单
// TODO: 操作无效，待修复
async function popupActionMenu() {
    getCurrentSession().click();
    await sleep(100);
    document.querySelector(actionMenuSelector).focus();
    await sleep(100);
    document.querySelector(actionMenuSelector).click();
    await sleep(100);
}

// 删除当前会话
async function deleteSession() {
    try {
        // 会话不存在，无需删除
        if (!getCurrentSession()) {
            return;
        }

        let sessionNum = getSessions().length;

        console.log('deleteSession', 'begin', sessionNum);

        await popupActionMenu();

        let actionButtons = document.querySelectorAll(actionButtonSelector);
        // 3个按钮：分享、重命名、删除
        if (!actionButtons[2]) {
            throw "找不到删除按钮";
        }
        actionButtons[2].click(); // 点击删除按钮
        await sleep(100);

        actionButtons = document.querySelectorAll(deleteButtonSelector);
        if (!actionButtons[0]) {
            throw "找不到确认按钮";
        }
        actionButtons[0].click(); // 点击确认按钮

        // 等待删除完成
        for (let i=0; i<100 && getSessions().length >= sessionNum; i++) {
            await sleep(100);
        }

        isNewSession = false;
        wantRename = null;
        console.log('deleteSession', 'end', getSessions().length);
    } catch (ex) {
        console.error('会话删除失败', ex);
        if (commandPhraseReply) {
            commandPhraseReply = "会话删除失败：" + ex + "\n\n@老虎会游泳，机器人代码需要更新。";
        }
    }
}

// 重命名会话
async function renameSession(newName) {
    try {
        // 等待加载完成
        for (let i=0; i<100 && (!isFinished() || !getCurrentSession()); i++) {
            await sleep(100);
        }

        // 记录会话URL
        setChatUrl(newName, getCurrentSession().href);

        console.log(getCurrentSession().innerText, '->', newName, getCurrentSession().innerText == newName);

        await popupActionMenu();

        let actionButtons = document.querySelectorAll(actionButtonSelector);
        if (!actionButtons[1]) {
            console.error('renameSession', '找不到编辑按钮');
            return;
        }
        actionButtons[1].click(); // 点击编辑按钮
        await sleep(100);

        let nameInput = document.querySelector(sessionNameInputSelector);
        if (!nameInput) {
            console.error('renameSession', '找不到输入框');
            return;
        }

        // 输入内容
        nameInput.value = newName;
        await sleep(100);

        // 把焦点转移到其他地方来保存输入内容
        document.querySelector(chatBoxSelector).focus();

        // 等待重命名完成
        for (let i=0; i<10 && getCurrentSession()?.innerText != newName; i++) {
            document.querySelector(chatBoxSelector).focus();
            await sleep(100);
            getCurrentSession().click();
            await sleep(100);
        }
    } catch (ex) {
        console.error('会话重命名失败', ex);
    }
}

function getChatUrlList() {
    return JSON.parse(localStorage.chatUrlList || '{}');
}

function getChatUrl(name) {
    let chatUrlList = getChatUrlList();
    return chatUrlList[name];
}

function setChatUrl(name, url) {
    let chatUrlList = getChatUrlList();
    chatUrlList[name] = url;
    localStorage.chatUrlList = JSON.stringify(chatUrlList);
}

function deleteChatUrl(name) {
    let chatUrlList = getChatUrlList();
    delete chatUrlList[name];
    localStorage.chatUrlList = JSON.stringify(chatUrlList);
}

// 获取会话列表
function getSessions() {
    return document.querySelectorAll(sessionListItemSelector);
}

// 查找会话
async function findSession(name) {
    // 等待加载完成
    for (let i=0; i<100 && !isFinished(); i++) {
        await sleep(100);
    }

    // 通过URL跳转加载的会话
    if (name == localStorage.lastChatName) {
        if (location.href == localStorage.lastChatUrl) {
            console.log('从URL加载会话成功', localStorage.lastChatName, localStorage.lastChatUrl, location.href);
            delete localStorage.lastChatUrl;
            delete localStorage.lastChatName;

            // 等待会话列表加载完成
            for (let i=0; i<100 && !getCurrentSession(); i++) {
                await sleep(100);
            }

            return getCurrentSession();
        } else {
            console.error('从URL加载会话失败', localStorage.lastChatName, localStorage.lastChatUrl, location.href);
            deleteChatUrl(localStorage.lastChatName);
            delete localStorage.lastChatUrl;
            delete localStorage.lastChatName;
            return null;
        }
    }

    // 点击切换会话
    let sessions = getSessions();
    for (let i=0; i<sessions.length; i++) {
        // 重命名时会交替使用.和-，有可能保存上的是.而非-
        if (sessions[i].innerText.replace('.', '-') == name) {
            delete localStorage.lastChatUrl;
            delete localStorage.lastChatName;
            return sessions[i];
        }
    }

    // 通过URL跳转加载会话
    let url = getChatUrl(name);
    if (url) {
        localStorage.lastChatUrl = url;
        localStorage.lastChatName = name;
        loadPage(url);
        await sleep(5000);
        return null;
    }

    // 找不到会话
    delete localStorage.lastChatUrl;
    delete localStorage.lastChatName;
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
        // 距离回复不到2秒，等够2秒
        // 防止重命名过程中ChatGPT同时自动重命名，导致我们的名称保存失败
        let timeDiff = 2000 - ((new Date().getTime()) - replyFinishTime);
        if (timeDiff > 0) {
            console.log(timeDiff + 'ms 后重命名会话');
            await sleep(timeDiff);
        }

        await renameSession(wantRename);
        wantRename = null;
    }
}

// 切换会话
async function switchSession(name, modelIndex) {
    isNewSession = false;

    // 在会话历史记录功能不可用时减少不必要的新建会话
    if (getSessions().length < 1 && lastSessionName === name) {
        // 会话相同，无需切换
        return;
    }
    // 需要切换会话，所以清理掉上次的会话名称
    lastSessionName = null;

    let stopOrRegenButton = document.querySelector(stopOrRegenButtonSelector);
    if (stopOrRegenButton && stopOrRegenButton.textContent == 'Stop generating') {
        // 会话生成卡住了，先点停止
        stopOrRegenButton.click();
        await sleep(500);
    }

    let session = await findSession(name);
    if (!session) {
        console.error('未找到会话', name, session);
        await renameWant();
        return await newChatSession(name, modelIndex);
    }

    if (getCurrentSession() == session) {
        if (document.querySelector(chatBoxSelector)
         && document.querySelector(sendButtonSelector)) {
            // 无需切换
            return;
        } else {
            // 找不到发言框，可能出错了，尝试新建一个会话
            await deleteSession();
            await newChatSession(name, modelIndex);
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
        || !getLastChatLine()
        || !document.querySelector(chatBoxSelector)
        || !document.querySelector(sendButtonSelector)
        || (!document.querySelector(modelNameSelector) &&
            !document.querySelector(upgradeToPlusSelector)) // 免费用户没有模型名称
        || !isFinished())
        && i < 100
    );
    // 再多等一会儿，防止意外
    await sleep(100);

    // 找不到发言框或发送按钮，当前会话可能出错
    if (!document.querySelector(chatBoxSelector) || !document.querySelector(sendButtonSelector)) {
        console.warn('找不到发言框或发送按钮，尝试删除会话', name);
        await deleteSession();
        return await newChatSession(name, modelIndex);
    }

    // 记录会话URL
    if (/\/c\//.test(location.href)) {
        console.log('会话URL:', name, location.href);
        setChatUrl(name, location.href);
    }

    console.log('switchSession', name, 'end', i, getSessionName());
}

function makeSessionName(uid, modelIndex) {
    return uid + '-' + modelIndex;
}

// 发送聊天信息
async function sendText(text, uid, modelIndex) {
    try {
        let commandFunc = commandPhrases[text];

        // 保存重试内容
        if (!commandFunc) {
            retryChatTexts[uid] = text;
        }

        // 切换会话
        let sessionName = makeSessionName(uid, modelIndex);
        await switchSession(sessionName, modelIndex);
        lastSessionName = sessionName;

        // 等待加载完成
        for (let i=0; i<100 && !isFinished(); i++) {
            await sleep(100);
        }

        // 增加延时，减少重复发言或发言失败的可能性
        await sleep(1000);

        // 执行命令短语
        if (commandFunc) {
            return await commandFunc(text, uid, modelIndex);
        }

        if (text.length < 1) {
            // 空白发言，用于取回上一条回复
            return;
        }

        let chatBox, sendButton, lastChatLine;
        lastReply = getLastReply();
        let i = 0;
        do {
            chatBox = document.querySelector(chatBoxSelector);
            sendButton = document.querySelector(sendButtonSelector);

            // 输入框获取焦点
            chatBox.focus();
            await sleep(100);

            // 设置输入框的值
            setNativeValue(chatBox, text);
            await sleep(100);

            // 点击发送按钮
            sendButton.click();
            await sleep(3000);

            i++;
            lastChatLine = getLastChatLine();
        } while (i < 10 && chatBox && sendButton && lastChatLine &&
                // 防止读取到上一条回复
                lastChatLine.querySelector(chatReplySelector) === lastReply);

        if (lastChatLine && lastChatLine.querySelector(chatReplySelector) === lastReply) {
            throw '发言未上屏';
        }
    } catch (ex) {
        wantRefresh = true;
        console.error('发言失败', ex);
        commandPhraseReply = '发言失败，请重试。当前会话已丢失。';
        await deleteSession();
    }
}

// 执行聊天信息中的指令
async function sendRequest(text, uid) {
    // 等待现有任务完成
    for (let i=0; i<1200 && !isFinished(); i++) {
        await sleep(100);
    }

    console.log('sendRequest', '@#'+uid, text);

    // 去除待审核提示
    text = text.trim().replace(/^发言[^\s]+可见。[\r\n]+/s, '').trim();

    // 分割指令
    //  @ChatGPT[ 模型序号][ 代码格式[=参数]]，发言内容
    // 示例：
    //  @ChatGPT，你好
    //  @ChatGPT 2，你好
    //  @ChatGPT html，输出一段html hello world
    //  @ChatGPT 2 html，输出一段html hello world
    //  @ChatGPT html=500，输出一段html hello world
    //  @ChatGPT 2 html=300x500，输出一段html hello world
    let parts = text.match(/^(?:[\s,，:：]*[@＠][#＃a-zA-Z0-9_\-\p{Script=Han}]+)*(?:[\s,，:：]+(\d+))?(?:[\s,，:：]+(html|text|latex|math|raw)(=[0-9,x]+)?)?(?:[\s,，:：]+(.*))?$/isu);

    modelName = null;
    replyCodeFormat = null;
    replyCodeFormatOpts = '';
    let modelIndex = modelMap[1];

    if (parts) {
        let model = parts[1];
        let codeFormat = parts[2];
        let codeFormatOpts = parts[3];
        text = parts[4] || '';

        // 选择模型
        if (undefined !== model && undefined !== modelMap[Number(model)]) {
            modelName = Number(model);
            modelIndex = modelMap[modelName];
        }

        // 指定代码格式
        if (undefined !== codeFormat) {
            replyCodeFormat = codeFormat.toLowerCase();
            replyCodeFormatOpts = codeFormatOpts || '';
        }
    }

    isTextEmpty = (text.length == 0);

    await sendText(text, uid, modelIndex);
    return modelIndex;
}

function getLastChatLine() {
    return Array.from(document.querySelectorAll(chatLineSelector)).at(-1);
}

function getLastReply(index = -1) {
    return Array.from(document.querySelectorAll(chatReplySelector)).at(index);
}

// 读取响应
async function readReply() {
    if (commandPhraseReply) {
        let reply = commandPhraseReply;
        commandPhraseReply = null;
        return reply;
    }

    // 等待回答完成
    // 先等个1秒，防止过早读取，获取到上一条回复
    await sleep(1000);
    // 因为状态转换的瞬间存在错判，所以多等几轮，防止还没回复完就返回
    for (let x=0; x<10; x++) {
        let i = 0;
        do {
            await sleep(100);
            i++;
        } while (i<120 && !isFinished());
        await sleep(100);
    }

    if (!isFinished()) {
        // 发言卡住了，回复完成后自动刷新
        wantRefresh = true;
    }

    replyFinishTime = new Date().getTime();

    // 检查会话是否需要重命名
    let sessionName = getSessionName();
    if (sessionName != lastSessionName) {
        wantRename = lastSessionName;
    }

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
                    var fence = (() => {
                        switch (replyCodeFormat) {
                            case 'html':
                                return ['[html' + replyCodeFormatOpts + ']', '[/html]'];
                            case 'text':
                                return ['[text]', '[/text]'];
                            case 'math':
                                return ['[math]', '[/math]'];
                            case 'latex':
                                return [options.fence + 'latex', options.fence];
                            default:
                                return [options.fence + lang, options.fence];
                        }
                    })();
                    return (
                        '\n\n' + fence[0] + '\n' +
                            code.replace(/[\r\n]+$/s, '') +
                        '\n' + fence[1] + '\n\n'
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
    i = 0;
    do {
        reply = getLastReply();
        i++;
    } while (i<50 && !reply && !await sleep(100));
    // 如果内容不为空，至少会有一个Text子节点
    if (!reply || !reply.childNodes || reply === lastReply) {
        if (isNewSession && isTextEmpty) {
            return "会话不存在，无法读取上一条回复。请发送非空留言。";
        }
        return translateErrorMessage(await autoRetry("READ_REPLY_FAILED"));
    }

    let errorMessage = '';
    if (errorMap[reply.textContent.substr(0, errorMaxLen)]) {
        errorMessage = translateErrorMessage(await autoRetry(reply.textContent));
        // 获取部分回复
        reply = getLastReply(-2);
        if (!reply || !reply.childNodes || reply === lastReply) {
            // 没有部分回复，直接返回错误信息
            return errorMessage;
        }
        errorMessage = "\n\n----------\n\n" + errorMessage;
    }

    // 用户要求原始回复，或内容包含数学公式，直接回复HTML代码
    if (replyCodeFormat == 'raw' || reply.querySelector('math,mjx-container')) {
        return `[html${replyCodeFormatOpts}]
<!doctype html>
<head>
    <link rel="stylesheet" href="https://hu60.cn/tpl/jhin/css/default.css"/>
    <link rel="stylesheet" href="https://hu60.cn/tpl/jhin/css/new.css"/>
    <link rel="stylesheet" href="https://hu60.cn/tpl/jhin/css/github-markdown.css"/>
    <link rel="stylesheet" href="https://hu60.cn/tpl/jhin/js/katex/dist/katex.min.css">
</head>
<body style="background-color: white">
    <div class="markdown-body">
        ${reply.innerHTML}
    </div>
    <div class="error-message">${errorMessage}</div>
</body>
[/html]`;
    }

    // 用插件 html 转 markdown
    if (turndownService) {
        try {
            return turndownService.turndown(reply) + errorMessage;
        } catch (ex) {
            console.error('turndown 转换失败', ex);
        }
    }

    // 插件加载或转换失败，手动 html 转 markdown
    let lines = [];
    reply.childNodes.forEach(x => {
        if (x.tagName == 'PRE') { // 代码
            let lang = x.querySelector('span')?.innerText || '';
            let code = x.querySelector('code').innerText.replace(/[\r\n]+$/s, '');
            lines.push("\n```" + lang + "\n" + code + "\n```\n");
        } else { // 正文
            lines.push(x.innerText);
        }
    });
    return lines.join("\n\n") + errorMessage;
}

// 判断响应是否结束
function isFinished() {
    return document.querySelector(chatBoxSelector)
        && document.querySelector(sendButtonSelector)
        && !document.querySelector(replyNotReadySelector);
}

// 自动重试
async function autoRetry(errorMessage) {
    if (!localStorage.lastAtInfo) {
        return errorMessage;
    }
    let atInfo = JSON.parse(localStorage.lastAtInfo);
    atInfo.retryTimes = atInfo.retryTimes || 0;
    if (errorMessage != 'network error' && atInfo.retryTimes < 5) {
        console.warn('自动重试', errorMessage);
        refreshPage();
        await sleep(30000);
    }
    return errorMessage;
}

// 读取@消息
async function readAtInfo() {
    // 读取保存的进度
    if (localStorage.lastAtInfo) {
        try {
            let atInfo = JSON.parse(localStorage.lastAtInfo);
            atInfo.retryTimes = atInfo.retryTimes || 0;
            if (atInfo.retryTimes < 5) {
                atInfo.retryTimes++;
                console.log('载入上次的@消息', localStorage.lastAtInfo);
                return atInfo;
            }
        } catch (ex) {
            console.log('读取保存的@消息出错', ex);
        }
    }

    let response = await fetch(hu60BaseUrl + 'msg.index.@.no.json?_origin=*&_json=compact&_content=json&_time=1', {
        redirect: "manual" // 不自动重定向
    });
    if (response.type == 'opaqueredirect') {
        // 登录失效，要求重新登录
        await login(true);
        return await readAtInfo();
    }
    return await response.json();
}

// 读取帖子内容
async function readTopicContent(path) {
    for (let i=0; i<5; i++) {
        try {
            let url = hu60BaseUrl + path.replace('{$BID}', 'json')
                .replace(/#.*$/s, '') // 去掉锚链接
                .replace(
                    /\?|$/s, // 注意主题帖的@链接不含问号
                    '?_origin=*&_json=compact&_content=text&pageSize=1&'
                );
            console.log('readTopicContent', url);
            let response = await fetch(url);
            return await response.json();
        } catch (ex) {
            console.error('readTopicContent failed:', i, path);
            await sleep(1000 * i); // 退避重试，第一次不等待，第二次等待1s，第三次等待2s
        }
    }
    throw '读取楼层，已重试5次，放弃重试';
}

// 翻译错误信息
function translateErrorMessage(replyText) {
    // 翻译错误提示并追加在线机器人列表
    if (errorMap[replyText.substr(0, errorMaxLen)]) {
        console.error('translateErrorMessage', replyText);
        replyText = errorMap[replyText.substr(0, errorMaxLen)];
        if (hu60MyUid) {
            if (replyText == '连接断开，回复不完整。') {
                wantRefresh = true;
                replyText += `\n\n可发送“@#${hu60MyUid}${modelName ? '，'+modelName : ''}，”来重新获取完整内容。`;
            } else {
                replyText += `\n\n可发送“@#${hu60MyUid}${modelName ? '，'+modelName : ''}，重试”来快速重试。`;
            }
        }
        if (hu60OnlineBot.length > 0) {
            replyText += `\n\n您也可以尝试@[empty]其他机器人，当前在线的机器人有：\n`;
            for (const botUid in hu60OnlineBot) {
                if (hu60OnlineBot[botUid] > 0) {
                    replyText += `* @#${botUid}\n`;
                }
            }
        }
    }
    return replyText;
}

// 回复帖子
async function replyTopic(uid, replyText, topicObject) {
    let content = "<!md>\n";
    if (modelName) {
        content += '[' + modelName + '] ';
    }
    if (isNewSession) {
        // 添加模型名称标记
        let modelTitle = document.querySelector(modelNameSelector)?.innerText.replace('Model: ', '') || '';
        if (modelTitle != '') {
            content += '[' + modelTitle + '] ';
        } else {
            content += '[默认模型] ';
        }

        content += '[新会话] ';
    } else if (isTextEmpty) {
        content += '[上一条回复] ';
    }
    content += "@#" + uid + "，";

    // 如果开头是ASCII中的非字母数字，则添加换行。
    // 开头可能是markdown标记，比如“```”、“*”、“#”等。
    if (/^[!"#$%&'()*+,\-./:;<=>?@\[\\\]^_`{|}~]/.test(replyText)) {
        content += "\n";
    }

    content += replyText;
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

    for (let i=0; i<5; i++) {
        try {
            let response = await fetch(hu60BaseUrl + url + '?_origin=*&_json=compact', {
                body: formData,
                method: "post",
                redirect: "manual" // 不自动重定向
            });
            return response;
        } catch (ex) {
            console.error('replyTopic failed:', i, url);
            await sleep(1000 * i); // 退避重试，第一次不等待，第二次等待1s，第三次等待2s
        }
    }
    throw '发言失败，已重试5次，放弃重试';
}

// 回复@信息
async function replyAtInfo(info, currentTime) {
    try {
        let uid = info.byuid;
        let url = info.content[0].url;

        // 防止自己和自己对话
        if (uid == hu60MyUid || uid < 1) {
            return;
        }

        console.log('replyAtInfo', hu60Url + url.replace('{$BID}', 'html'));

        if (currentTime > 0 && currentTime - info.ctime > 600) {
            console.warn('忽略', (currentTime - info.ctime) / 60, '分钟前的对话');
            return;
        }

        let topicObject = await readTopicContent(url);
        let text = null;
        if (topicObject.tContents) {
            text = topicObject.tContents[0].content;
        } else {
            text = topicObject.chatList[0].content;
        }

        let modelIndex = await sendRequest(text, uid);
        let replyText = await readReply();

        try {
            let response = await replyTopic(uid, replyText, topicObject);
            if (response.type == 'opaqueredirect') {
                console.log('success:', true);
            } else {
                console.log(await response.text());
            }
        } catch (ex) {
            console.error(ex);
        }
    } catch (ex) {
        console.error(ex);
    }

    // 重命名会话
    await renameWant();
}

// 登录虎绿林
async function login(relogin) {
    let isEmpty = (str) => {
        return ((typeof str) != 'string') || (str.length == 0);
    };
    let loginAlert = (text) => {
        console.error(text);
    };

    try {
        console.log('登录虎绿林');

        let hu60User = document.hu60User;
        let hu60Pwd = document.hu60Pwd;

        if (isEmpty(hu60User) || isEmpty(hu60Pwd)) {
            if (relogin || isEmpty(localStorage.hu60User) || isEmpty(localStorage.hu60Pwd)) {
                localStorage.hu60User = prompt("虎绿林用户名：") || '';
                localStorage.hu60Pwd = prompt("虎绿林密码：") || '';

                // 只在要求用户输入密码时弹出错误对话框
                loginAlert = (text) => {
                    console.error(text);
                    alert(text);
                };
            }

            hu60User = localStorage.hu60User;
            hu60Pwd = localStorage.hu60Pwd;
        }

        if (isEmpty(hu60User) || isEmpty(hu60Pwd)) {
            loginAlert('登录失败：用户名或密码为空。5秒后重试');
            await sleep(5000);
            return await login(true);
        }

        let formData = new FormData();
        formData.append('type', '1'); // 用户名登录
        formData.append('name', hu60User);
        formData.append('pass', hu60Pwd);
        formData.append('go', '1');

        let response = await fetch(hu60Url + 'user.login.json?_origin=*&_json=compact', {
            body: formData,
            method: "post",
            redirect: "manual" // 不自动重定向
        });
        let result = await response.json();
        if (!result.success) {
            throw result.notice;
        }

        hu60Sid = result.sid;
        hu60BaseUrl = hu60Url + hu60Sid + '/';
        hu60MyUid = result.uid;
    } catch (ex) {
        console.log(ex);
        loginAlert('登录失败：' + ex + '5秒后重试');
        await sleep(5000);
        return await login(true);
    }
}

function connectToWebSocket() {
    const socket = new WebSocket(document.hu60Domain.replace('http', 'ws') + "/ws/msg?_sid=" + hu60Sid);
    let keepAliveTimer = null;

    // 开启 WebSocket 连接时触发
    socket.onopen = (event) => {
        console.log("WebSocket 连接已经建立");

        // 请求在线机器人列表
        socket.send('{"action": "lsol"}');

        // 连上推送服务器后还要再查询一次消息接口，防止错过还没连上的这段时间发来的消息
        runOnce();

        // 每隔一分钟发送一个 keep alive 消息，防止连接断开，顺便更新在线机器人列表
        keepAliveTimer = setInterval(() => {
            socket.send('{"action": "lsol"}');
        }, 60000);
    }

    // 接收到 WebSocket 消息时触发
    socket.onmessage = (event) => {
        console.log("收到 WebSocket 消息", event.data);

        // 处理消息
        try {
            let msg = JSON.parse(event.data);
            switch (msg.event) {
                // 更新在线机器人列表
                case 'lsol':
                    hu60OnlineBot = msg.data;
                    break;
                case 'online':
                    hu60OnlineBot[msg.data.uid] = msg.data.count;
                case 'offline':
                    delete hu60OnlineBot[msg.data.uid];
                default:
                    // ignore
                    break;
            }
        } catch (ex) {
            console.error(ex);
        }

        // 检查是否有新的@消息
        runOnce();
    };

    // 当 WebSocket 连接出错时触发
    socket.onerror = (event) => {
        console.error("WebSocket 连接出错", event);
        // 关闭当前 WebSocket 连接
        socket.close();

        // ws服务不可用，改用轮询
        runOnce();
    };

    // 当 WebSocket 连接关闭时触发
    socket.onclose = (event) => {
        // 取消 keep alive 定时器
        clearInterval(keepAliveTimer);

        console.log("WebSocket 连接已关闭", event);

        // 重新连接 WebSocket
        setTimeout(() => {
            console.log("重新连接 WebSocket");
            connectToWebSocket();
        }, 5000); // 延迟 5 秒重新连接
    };
}

// 定义一个锁对象
const runOnceLock = {
    isLocked: false,
    queue: [],

    // 加锁方法
    lock: function () {
        if (this.isLocked) {
            // 如果锁已经被其他线程占用，则将当前线程加入队列等待
            return new Promise(resolve => this.queue.push(resolve));
        } else {
            // 如果锁没有被占用，则直接占用锁
            this.isLocked = true;
            return Promise.resolve();
        }
    },

    // 解锁方法
    unlock: function () {
        if (this.queue.length > 0) {
            // 如果队列中有等待的线程，则唤醒队列中的第一个线程
            const resolve = this.queue.shift();
            resolve();
        } else {
            // 如果队列中没有等待的线程，则释放锁
            this.isLocked = false;
        }
    }
};

async function runOnce() {
    await runOnceLock.lock();
    try {
        // 等待 New Chat 按钮出现，出现了说明已经通过浏览器验证
        for (let i=0; i<30 && !document.querySelector(newChatButtonSelector); i++) {
            await sleep(1000);
        }
        // New Chat 按钮还是没出现，刷新页面
        if (!document.querySelector(newChatButtonSelector)) {
            console.error('找不到 New Chat 按钮');
            refreshPage();
            await sleep(30000);
        }

        // 浏览器用户可能直接输入了问题，等待回答完成
        for (let i=0; i<1200 && !isFinished(); i++) {
            await sleep(100);
        }

        let exceptionCount = 0;
        let atInfo = await readAtInfo();

        // 读取保存的进度
        if (atInfo.lastPos === undefined) {
            // @消息是后收到的在前面，所以最后一个是第一个
            atInfo.lastPos = atInfo.msgList.length - 1;
        }

        // @消息是后收到的在前面，所以从后往前循环，先发的先处理
        for (let i = atInfo.lastPos; i>=0; i--) {
            // 保存进度以便自动刷新页面后重试
            atInfo.lastPos = i;
            localStorage.lastAtInfo = JSON.stringify(atInfo);

            try {
                await replyAtInfo(atInfo.msgList[i], atInfo._time);
                atInfo.retryTimes = 0; // 读取回复成功了，重置重试计数器，避免多个会话的重试次数累积超过5
                await sleep(100);
            } catch (ex) {
                exceptionCount++;
                console.error(ex);
                await sleep(1000);
            }
        }

        // 清空保存的进度
        delete localStorage.lastAtInfo;

        // 执行管理员命令
        await runAdminCommand();

        // 异常太多，刷新页面
        if (exceptionCount > 0 && exceptionCount >= atInfo.msgList.length) {
            refreshPage();
            await sleep(30000); // 防止实际刷新前执行到后面的代码
        }
        await sleep(1000);
    } catch (ex) {
        console.error(ex);
        await sleep(5000);
        // 存在未捕捉异常，刷新页面
        refreshPage();
        await sleep(30000); // 防止实际刷新前执行到后面的代码
    }
    runOnceLock.unlock();
}

// 运行机器人
async function run() {
    // 把重试文本保存在localStorage，在刷新后自动载入
    window.addEventListener("beforeunload", () => {
        localStorage.retryChatTexts = JSON.stringify(retryChatTexts);
    });
    retryChatTexts = JSON.parse(localStorage.retryChatTexts || '{}') || {};

    loadScript(turndownJsUrl);
    loadScript(turndownGfmJsUrl);
    if (document.hu60VConsole) {
        loadScript(vConsoleJsUrl);
    }

    // 如果油猴定义了自定义主循环，则使用该主循环
    // 用于把机器人接入其他类型的网站
    if (document.run) {
        return await document.run();
    }

    await login();
    console.log('等待1秒...');
    await sleep(1000); // 等待页面充分加载
    console.log('虎绿林ChatGPT机器人已启动');

    connectToWebSocket();
}

try {
    run();
} catch (ex) {
    console.error(ex);
    sleep(1000).then(() => {
        // 存在未捕捉异常，刷新页面
        refreshPage();
    });
}
