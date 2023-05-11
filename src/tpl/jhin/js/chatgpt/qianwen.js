////////////////// 虎绿林 通义千问 聊天机器人 //////////////////

/**************************************************************
使用方法：
1. 使用最新版的Chrome谷歌浏览器或Firefox火狐浏览器，不要使用QQ浏览器、360浏览器等，不保证兼容。
2. 安装油猴插件：https://www.tampermonkey.net/
3. 在油猴里添加新脚本，粘贴如下代码并保存：

// ==UserScript==
// @name         虎绿林通义千问机器人
// @namespace    https://hu60.cn/
// @version      1.0
// @description  把通义千问接入hu60wap6网站程序
// @author       老虎会游泳
// @match        https://tongyi.aliyun.com/*
// @icon         https://hu60.cn/favicon.ico
// @grant        none
// ==/UserScript==

document.hu60User = ''; // 虎绿林用户名
document.hu60Pwd = ''; // 虎绿林密码
document.hu60AdminUids = [1, 19346, 15953]; // 机器人管理员uid，管理员可以发“@通义千问，刷新页面”来重启机器人
document.hu60Domain = 'https://hu60.cn'; // 如果要对接其他网站，请修改此处的域名（必须是https的否则连不上）
var script = document.createElement("script");
script.src = document.hu60Domain + '/tpl/jhin/js/chatgpt/qianwen.js?r=' + (new Date().getTime());
document.head.appendChild(script);

4. 打开 https://tongyi.aliyun.com/ 并登录。
5. 在来到聊天页面时，会弹出输入虎绿林用户名密码的提示框。
   如果你要把机器人接入虎绿林，请注册一个新帐号。**使用现有帐号运行机器人将被删帖或禁言**。
   输入新帐号用户名密码后，机器人即启动，保持页面不要关闭。
   机器人会使用你在此处输入的帐号与其他用户进行对话，在虎绿林用其他帐号`@该帐号`即可尝试对话。
   注意，使用该帐号自己`@自己`是不会有反应的，必须用另一个账号来和机器人对话。
6. 也可以把用户名密码填在油猴脚本里，这样就不用在对话框里输入了。
7. F12控制台不会有任何日志输出，因为阿里屏蔽了console.log。
   机器人自带一个简易调试控制台，往下滚动页面就能看见。

### 如何把机器人接入其他类型的网站？

你可以在油猴脚本的末尾添加一个自定义主循环，用于把机器人接入其他类型的网站。以下是一个例子：

document.run = async function() {
    while (true) {
        try {
            // 访问你的网站获取要发给通义千问的内容
            // 网站必须是https的，否则连不上。
            // 此外网站还必须设置 Access-Control-Allow-Origin: * 头信息，否则也连不上。
            let response = await fetch('https://example.com/my-message.php');

            // 假设获取到的信息是JSON，把它转换成JSON对象
            // 网站必须设置 content-type: application/json 头信息，否则转换会失败。
            let messages = response.json();

            // 假设JSON结构是这样：
            // {"data": [
            //    {"uid":3, "text":"@通义千问，你好"},
            //    {"uid":2, "text":"@通义千问，我有一个问题"},
            //    {"uid":1, "text":"@通义千问，刷新页面"},
            // ]}
            let exceptionCount = 0;
            for (let i=0; i<messages.data.length; i++) {
                // 要发给通义千问的话，开头包含的“@机器人名称，”会被后续流程自动去除。
                let text = messages.data.text;

                // 用户id，可以是字符串，所以给出用户名也是可以的。
                let uid = messages.data.uid;

                try {
                    // 把对话发给通义千问
                    // 返回的 modelIndex 是为对话选择的模型id（从0开始编号），目前始终是0
                    let modelIndex = await sendRequest(text, uid);

                    // 从通义千问读取回复
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
                await sleep(5000); // 防止实际刷新前执行到后面的代码
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
    'READ_REPLY_FAILED':
        "读取回复出错，请重试。每天第一次和机器人对话时经常发生这种错误，通常再试一次就会好。",
};

// 错误提示文本的最大长度
const errorMaxLen = Math.max(...Object.keys(errorMap).map(x => x.length));

// 模型对应关系
const modelMap = {
    1 : 0, // 通义千问没有模型切换功能，保留接口备用
};

/////////////////////////////////////////////////////////////

// 聊天框的CSS选择器
const chatBoxSelector = 'textarea.ant-input.textarea--g7EUvnQR';

// 发送按钮的CSS选择器
const sendButtonSelector = 'div.chatBtn--RFpkrgo_';

// 正在输入动效（三个点）和加载中动效（转圈）的CSS选择器
const replyNotReadySelector = 'div.loading--xeSR5ofU';

// 停止生成/重新生成按钮
const stopOrRegenButtonSelector = 'div.btn--Bw0FbWYV';

// 聊天内容（包括提问与回复）的CSS选择器
const chatLineSelector = 'div.markdown-body';

// 聊天回答的CSS选择器
const chatReplySelector = 'div.content--BiTVEwIO div.markdown-body';

// 左侧会话列表项的CSS选择器
const sessionListItemSelector = 'div.sessionItem--mW9BBf__';

// 当前会话的CSS选择器
const currentSessionSelector = 'div.sessionItem--mW9BBf__.activeItem--kvBaq8kL';

// 编辑、删除、确认、取消按钮的CSS选择器
const actionButtonSelector = 'span.anticon.icon--VUkaCEcd';

// 删除确认按钮的CSS选择器
const deleteConfirmSelector = 'button.btn--ABIBn5ou';

// 会话名称编辑框的CSS选择器
const sessionNameInputSelector = 'div.edit--NiLV64lY input.ant-input';

// 新建会话按钮的CSS选择器
const newChatButtonSelector = 'button.addBtn--PeYRP6FX';

// 模型下拉框的CSS选择器（通义千问没有模型选择功能，保留此接口备用）
const modelListBoxSelector = 'hu60-none';

// 模型列表项的CSS选择器
const modelListItemSelector = 'hu60-none';

// “Upgrade to Plus”按钮的CSS选择器
const upgradeToPlusSelector = 'hu60-none';

// 会话列表“Show more”按钮的CSS选择器（尚未观察到该按钮）
const showMoreButtonSelector = 'hu60-none';

// 同意按钮的CSS选择器
// “您的账号已在其他站点登录并正在体验中，在本页面操作将会导致其他站点无法体验，是否继续？”
const agreeButtonSelector = 'button.btn--GALkqyh3.primary--PfKRnzJe.default--A6VtuuPJ';

// 登录/注册按钮
const loginButtonSelector = 'div.content--i9W3Qmal button.btn--GALkqyh3';

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
// 以防通义千问自动重命名会话导致我们的名称保存失败。
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
        await sleep(5000); // 防止实际刷新前执行到后面的代码
        wantRefresh = false;
    }
}

/////////////////////////////////////////////////////////////

// 刷新页面
function refreshPage() {
    console.error('刷新页面', Error().stack);
    location.reload();
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
    if (!document.querySelector(modelListBoxSelector)) {
        // 通义千问没有模型选择功能，保留此接口备用
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
async function newChatSession(name, modelIndex) {
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
    // 再多等一会儿，防止意外
    await sleep(100);

    // 选择模型
    await selectModel(modelIndex);

    isNewSession = true;
    wantRename = name;
    console.log('newChatSession', sessionIndex, modelIndex, 'end');
}

// 删除当前会话
async function deleteSession() {
    try {
        let sessionNum = getSessions().length;

        console.log('deleteSession', 'begin', sessionNum);
        let actionButtons = getCurrentSession()?.querySelectorAll(actionButtonSelector);
        if (!actionButtons[1]) {
            throw "找不到删除按钮";
        }

        // 点击删除按钮
        sendClickEvent(actionButtons[1]);
        await sleep(100);

        actionButtons = document.querySelectorAll(deleteConfirmSelector);
        if (!actionButtons[1]) {
            throw "找不到确认按钮";
        }
        actionButtons[1].click(); // 点击确认按钮

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

        // 输入框获取焦点
        nameInput.focus();
        await sleep(100);

        // 设置输入框的值
        setNativeValue(nameInput, newName);
        await sleep(100);

        actionButtons = document.querySelectorAll(actionButtonSelector);
        if (!actionButtons[0]) {
            console.error('renameSession', '找不到确认按钮');
            return;
        }
        actionButtons[0].click(); // 点击确认按钮
        await sleep(100);
    } catch (ex) {
        console.error('会话重命名失败', ex);
    }
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
        // 距离回复不到2秒，等够2秒
        // 防止重命名过程中通义千问同时自动重命名，导致我们的名称保存失败
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
    if (stopOrRegenButton && stopOrRegenButton.textContent == '停止生成') {
        // 会话生成卡住了，先点停止
        stopOrRegenButton.click();
        await sleep(500);
    }

    let session = await findSession(name);
    if (!session) {
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
        || !document.querySelector(chatBoxSelector)
        || !document.querySelector(sendButtonSelector)
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
            await sleep(100);

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
    text = text.trim().replace(/^发言待审核，仅管理员和作者本人可见。/s, '').trim();

    // 分割指令
    //  @通义千问[ 模型序号][ 代码格式[=参数]]，发言内容
    // 示例：
    //  @通义千问，你好
    //  @通义千问 2，你好
    //  @通义千问 html，输出一段html hello world
    //  @通义千问 2 html，输出一段html hello world
    //  @通义千问 html=500，输出一段html hello world
    //  @通义千问 2 html=300x500，输出一段html hello world
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
        isNewSession = true;
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
                    return node.nodeName === 'PRE' && node.querySelector('div.codeContainer--P0gL5w0X');
                },
                replacement: function (content, node, options) {
                    console.log(content, node, options);
                    var lang = node.querySelector('span.codeLang--OLsD9lW6')?.textContent || ''; // lang span可能不存在
                    var code = node.querySelector('code').textContent;
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
                            code +
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
    let stopOrRegenButton = document.querySelector(stopOrRegenButtonSelector);
    let waitButton = document.querySelector(replyNotReadySelector);

    if (stopOrRegenButton && stopOrRegenButton.textContent == '停止生成') {
        return false;
    }
    if (waitButton && waitButton.style.display != "none") {
        return false;
    }
    return true;
}

// 自动重试
async function autoRetry(errorMessage) {
    if (!localStorage.lastAtInfo) {
        return errorMessage;
    }
    let atInfo = JSON.parse(localStorage.lastAtInfo);
    atInfo.retryTimes = atInfo.retryTimes || 0;
    if (errorMessage != '网络错误' && atInfo.retryTimes < 5) {
        refreshPage();
        await sleep(5000);
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
                console.log('载入上次的@消息', atInfo);
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
        if (document.querySelector(loginButtonSelector)?.innerText == '登录/注册') {
            console.log('请登录阿里云');
            document.querySelector(loginButtonSelector).click();
            // 等待登录
            for (let i=0; i<240 && document.querySelector(loginButtonSelector)?.innerText == '登录/注册'; i++) {
                await sleep(500);
            }
        }

        // “您的账号已在其他站点登录并正在体验中，在本页面操作将会导致其他站点无法体验，是否继续？”
        for (let i=0; i<5 && document.querySelector(agreeButtonSelector); i++) {
            document.querySelector(agreeButtonSelector).click();
            await sleep(1000);
        }

        // 跳转到聊天列表页
        if (location.href == 'https://tongyi.aliyun.com/') {
            location.href = 'https://tongyi.aliyun.com/chat';
            await sleep(5000);
        }

        // 等待新建对话按钮出现，出现了说明已经通过浏览器验证
        for (let i=0; i<30 && !document.querySelector(newChatButtonSelector); i++) {
            await sleep(1000);
        }
        // 新建对话按钮还是没出现，刷新页面
        if (!document.querySelector(newChatButtonSelector)) {
            console.error('找不到新建对话按钮');
            refreshPage();
            await sleep(5000);
        }

        // 浏览器用户可能直接输入了问题，等待回答完成
        for (let i=0; i<1200 && !isFinished(); i++) {
            await sleep(100);
        }

        let exceptionCount = 0;
        let atInfo = await readAtInfo();

        // 读取保存的进度
        atInfo.lastPos = atInfo.lastPos || atInfo.msgList.length - 1;

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
        localStorage.lastAtInfo = '';

        // 执行管理员命令
        await runAdminCommand();

        // 异常太多，刷新页面
        if (exceptionCount > 0 && exceptionCount >= atInfo.msgList.length) {
            refreshPage();
            await sleep(5000); // 防止实际刷新前执行到后面的代码
        }
        await sleep(1000);
    } catch (ex) {
        console.error(ex);
        await sleep(5000);
        // 存在未捕捉异常，刷新页面
        refreshPage();
        await sleep(5000); // 防止实际刷新前执行到后面的代码
    }
    runOnceLock.unlock();
}

// 运行机器人
async function run() {
    loadScript(turndownJsUrl);
    loadScript(turndownGfmJsUrl);
    loadScript(vConsoleJsUrl);

    // 如果油猴定义了自定义主循环，则使用该主循环
    // 用于把机器人接入其他类型的网站
    if (document.run) {
        return await document.run();
    }

    await login();
    console.log('等待1秒...');
    await sleep(1000); // 等待页面充分加载
    console.log('虎绿林通义千问机器人已启动');

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
