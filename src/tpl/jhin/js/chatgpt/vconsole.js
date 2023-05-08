///////////// 虚拟调试控制台 /////////////

// 保留控制台日志以供分析
let consoleMessages = [];

let consoleLog = console.log;
let consoleWarn = console.warn;
let consoleError = console.error;

// 把控制台调用变成非阻塞的，避免网页检测到性能下降，从而判定为开发者工具已打开。
// 讯飞星火在判定为开发者工具打开时会跳转到空白页。
console._log = async (...args) => consoleLog(...args);
console._warn = async (...args) => consoleWarn(...args);
console._error = async (...args) => consoleError(...args);

console.log = function(...args) {
    console._log(...args);
    addConsoleMessages('log', args);
};
console.warn = function (...args) {
    console._warn(...args);
    addConsoleMessages('warn', args);
};
console.error = function (...args) {
    console._error(...args);
    addConsoleMessages('error', args);
};

// 添加控制台日志
async function addConsoleMessages(tag, args) {
    try {
        // 忽略无意义日志
        if ((args.length > 0 && ['PageURL', 'PagePath', 'ClickClass', 'ClickID', 'FormText'].indexOf(args[0]) != -1)) {
            return;
        }

        args.unshift('[' + tag + ']');
        args.unshift(new Date().toLocaleTimeString());

        let line = args.join(' ');
        appendVConsole(line);
        consoleMessages.push(line);
    } catch (ex) {
        console._error(ex);
    }
};

// 保存控制台日志
function saveConsoleMessages() {
    try {
        let value = consoleMessages.join("\n");
        let key = 'console:' + new Date().toISOString();
        localStorage.setItem(key, value);
    } catch (ex) {
        console._error(ex);
    }
};

// 清理过多的日志
// 最多保留5份
function cleanConsoleStorage() {
    try {
        let count = 0;
        let deleted = 0;
        Object.keys(localStorage).sort().reverse().forEach(key => {
            if (key.startsWith('console:')) {
                count++;
                if (count > 5) {
                    deleted++;
                    localStorage.removeItem(key);
                }
            }
        });
        if (deleted > 0) {
            console.log('cleanConsoleStorage', deleted);
        }
    } catch (ex) {
        console.error(ex);
    }
}

// 初始化虚拟控制台
function initVConsole() {
    // 由张小强设计的调试控制台UI <https://hu60.cn/q.php/bbs.topic.104950.html?floor=3#3>
    let html = `
<div id="vConsoleButton" onclick="openVConsole()">
    <svg style="width: 1em;height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;"
        viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2695">
        <path
            d="M992 128 32 128C14.336 128 0 142.336 0 160l0 704c0 17.696 14.336 32 32 32l960 0c17.696 0 32-14.304 32-32L1024 160C1024 142.336 1009.696 128 992 128zM960 800c0 17.696-14.304 32-32 32L96 832c-17.664 0-32-14.304-32-32L64 224c0-17.664 14.336-32 32-32l832 0c17.696 0 32 14.336 32 32L960 800zM303.936 388.512 180.576 265.12c-12-12-31.456-12-43.456 0s-12 31.456 0 43.456L244.576 416l-107.456 107.456c-12 12-12 31.424 0 43.424s31.456 12 43.456 0l123.36-123.36c2.432-1.344 4.896-2.56 6.944-4.608C317.184 432.608 320 424.256 319.648 416c0.32-8.256-2.464-16.608-8.768-22.88C308.832 391.072 306.368 389.856 303.936 388.512zM608 512l-192 0c-17.664 0-32 14.336-32 32 0 17.696 14.336 32 32 32l192 0c17.696 0 32-14.304 32-32C640 526.336 625.696 512 608 512z"
            fill="#ffffff"></path>
    </svg>
    <span> 打开控制台</span>
</div>
<div id="vConsole" class="vConsoleHidden">
    <div id="vConsoleContainer">
        <div id="vConsoleBar">
            <label>调试控制台</label>
            <div class="vConsoleClean" onclick="clearVConsole()">
                <svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                    <path d="M274.56 798.997l19.435-25.13-33.792 68.565a18.133 18.133 0 0 0 11.562 25.536l59.734 16a18.133 18.133 0 0 0 17.28-4.48c20.522-19.819 35.626-35.99 45.29-48.47l19.456-25.13-33.813 68.565a18.133 18.133 0 0 0 11.563 25.536l84.48 22.635a18.133 18.133 0 0 0 17.28-4.48c20.522-19.84 35.626-35.99 45.269-48.47l19.456-25.13-33.813 68.565a18.133 18.133 0 0 0 11.584 25.558l72.106 19.328a18.133 18.133 0 0 0 17.28-4.48c20.523-19.84 35.627-36.011 45.27-48.491l19.456-25.13-33.814 68.586a18.133 18.133 0 0 0 11.584 25.515l86.422 23.338 3.84-0.213c13.269-0.704 29.056-5.035 43.84-12.8 29.781-15.701 48.17-43.2 52.181-78.25 2.133-18.518 4.779-38.55 8.405-63.531 1.643-11.222 2.944-20.011 6.23-41.835 11.05-73.323 14.634-101.035 17.13-133.675l0.939-12.373 2.837-2.923 12.331-1.344a41.813 41.813 0 0 0 24.81-11.221c10.731-10.24 14.806-25.387 11.094-42.197l-37.547-171.584c-3.029-13.696-11.264-27.947-23.146-39.83-11.648-11.626-25.92-20.138-39.894-23.893l-113.258-30.357-2.262-3.926 52.886-197.248c8.32-31.061-11.755-63.744-44.971-72.64l-79.51-21.312c-33.194-8.896-66.922 9.366-75.263 40.427l-52.843 197.27-3.925 2.26-118.102-31.637c-13.994-3.754-30.634-3.498-46.506 0.747-16.256 4.352-30.507 12.587-39.958 22.933L194.86 397.973c-11.606 12.715-15.659 27.84-11.52 42.091 4.16 14.23 15.85 25.195 32.896 30.528l13.61 4.267 2.134 3.882-3.627 13.803c-21.12 79.85-52.885 136.917-85.717 150.89-47.531 20.203-72.939 49.43-78.422 85.035-5.034 32.683 9.28 67.115 37.59 91.542l22.037 8.341 74.667 20.01a42.667 42.667 0 0 0 41.216-11.05c15.274-15.275 26.88-28.032 34.837-38.293z m551.381-396.565c14.144 3.797 29.952 19.2 32.768 32l34.56 157.781a10.667 10.667 0 0 1-13.184 12.587L240.64 433.493A10.667 10.667 0 0 1 235.52 416l108.8-119.36c8.832-9.685 30.23-15.147 44.373-11.35l141.334 37.867a21.333 21.333 0 0 0 26.133-15.082l58.304-217.643a21.333 21.333 0 0 1 26.133-15.083L717.653 96a21.333 21.333 0 0 1 15.083 26.133l-58.325 217.643a21.333 21.333 0 0 0 15.082 26.112l136.448 36.565zM315.456 701.568c-33.664 45.141-64.597 79.083-92.8 101.803l-5.91 4.778-2.837 0.598-88.106-24.107-2.923-3.2c-13.035-14.165-19.37-31.04-16.981-46.592 3.285-21.333 22.058-39.339 53.205-52.587 31.723-13.482 59.819-47.104 82.923-99.904 10.026-22.954 18.88-48.725 26.389-76.586l3.883-14.4 3.904-2.262 566.165 151.702 2.347 3.306-0.79 12.224c-1.984 30.592-30.336 229.398-32.128 244.907-2.346 20.416-11.306 34.987-27.605 44.395a73.237 73.237 0 0 1-21.397 8.106l-5.014 0.726-60.373-16.171 11.243-20.288c8.277-14.976 22.656-43.84 43.093-86.613a21.12 21.12 0 0 0-9.963-28.16l-3.136-1.494a21.333 21.333 0 0 0-26.261 6.486c-33.643 45.056-64.533 78.912-92.672 101.546l-5.91 4.758-2.837 0.597-52.544-14.08 11.115-20.267a858.608 858.608 0 0 0 10.453-19.626c7.04-13.504 17.899-35.798 32.598-66.816a21.29 21.29 0 0 0-9.984-28.31l-3.03-1.45a21.333 21.333 0 0 0-26.368 6.442c-33.6 45.014-64.469 78.827-92.608 101.483l-5.909 4.757-2.837 0.598-52.139-13.974 11.115-20.266A871.566 871.566 0 0 0 441.28 824c6.997-13.461 17.963-35.947 32.896-67.435a20.97 20.97 0 0 0-10.112-28.01l-3.328-1.536a21.333 21.333 0 0 0-26.07 6.613c-33.642 45.056-64.554 78.976-92.778 101.696l-5.91 4.757-2.837 0.598-32.64-8.747 11.094-20.245c3.541-6.507 7.04-13.035 10.453-19.627 6.976-13.483 17.941-35.968 32.875-67.456a21.056 21.056 0 0 0-10.07-28.075l-3.242-1.514a21.333 21.333 0 0 0-26.155 6.549z" fill="#ffffff"></path>
                </svg>
            </div>
            <div class="vConsoleCloseBtn" onclick="closeVConsole()">
                <svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3554">
                    <path d="M548.992 503.744L885.44 167.328a31.968 31.968 0 1 0-45.248-45.248L503.744 458.496 167.328 122.08a31.968 31.968 0 1 0-45.248 45.248l336.416 336.416L122.08 840.16a31.968 31.968 0 1 0 45.248 45.248l336.416-336.416L840.16 885.44a31.968 31.968 0 1 0 45.248-45.248L548.992 503.744z" fill="#ffffff"></path>
                </svg>
            </div>
        </div>
        <textarea id="vConsoleOutput" readonly></textarea>
        <div class="vConsoleCommand">
            <textarea type="text" id="vConsoleInput" placeholder="请键入命令..."></textarea>
        </div>
    </div>
</div>
<style>
#vConsoleButton {
    position: fixed;
    z-index: 9999;
    bottom: 20px;
    right: 20px;
    color: white;
    padding: 5px 10px;
    background-color: #53b1a8;
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgb(0 0 0 / 10%);
    opacity: 1;
    transition: opacity 1s ease-in-out;
    cursor: pointer;
}

#vConsole {
    position: fixed;
    z-index: 9999;
    bottom: -400px;
    left: 0;
    width: 100%;
    background-color: #fff;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    box-shadow: 0px 4px 12px rgb(0 0 0 / 10%);
    /* padding: 20px 20px 0; */
    transition: bottom 0.3s ease-in-out;
    background-color: #272727;
    box-sizing: border-box;
}

.vConsoleHidden {
    display: none;
    opacity: 0;
}

#vConsoleBar label {
    color: white;
}

.vConsoleCloseBtn {}

#vConsoleBar svg {
    width: 20px;
    margin: 15px 5px;
    height: 20px;
    cursor: pointer;
}

#vConsoleBar:hover {
    cursor: ns-resize;
    user-select: none;
}  

#vConsoleContainer {
    display: flex;
    flex-direction: column;
    height: 300px;
    min-height: 200px;
}

.vConsoleCommand {
    width: 100%;
    margin-bottom: 25px;
    position: relative;
}

#vConsoleOutput {
    flex: 1;
    padding: 0 10px 0 20px;
    color: lime;
    background-color: transparent;
    border: 0;
    overflow-x: hidden;
}

#vConsoleBar {
    display: flex;
    align-items: center;
    margin: 0 20px;
}

.vConsoleClean {
    margin-left: auto;
    margin-right: 5px;
}

#vConsoleInput {
    width: 100%;
    box-sizing: border-box;
    height: 40px;
    background-color: #272727;
    border-top: 1px solid rgb(140 140 140);
    border-bottom: 1px solid rgb(140 140 140);
    caret-color: rgb(140 140 140);
    padding: 10px 20px;
    color: white;
    overflow-x: hidden;
}

.vConsoleCommand::before {
    content: ">";
    display: inline-block;
    color: white;
    position: absolute;
    line-height: 40px;
    margin: 0 5px;
}

#vConsoleInput:focus,
#vConsoleOutput:focus {
    outline: none;
    box-shadow: none;
}

#vConsoleInput::placeholder {
    color: rgb(140 140 140);
}
</style>
`;
    document.body.insertAdjacentHTML('beforeend', html);

    let commandHistory = [];
    let commandHistoryPos = null;
    let currentCommand = null;
    document.querySelector('#vConsoleInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (e.shiftKey) { // Shift+回车键入换行
                var start = this.selectionStart;
                var end = this.selectionEnd;
                var value = this.value;
                this.value = value.substring(0, start) + '\n' + value.substring(end);
                this.selectionStart = this.selectionEnd = start + 1;
            } else { // 直接回车执行命令
                let cmd = this.value;
                try {
                    // 保存命令历史记录
                    if (commandHistory.at(-1) !== cmd) {
                        commandHistory.push(cmd);
                    }
                    commandHistoryPos = null;
                    currentCommand = null;
                    // 提供 clear() 命令
                    let clear = () => {
                        document.querySelector('#vConsoleOutput').value = '';
                    }
                    // 提供 log(...args) 命令
                    let log = (...args) => {
                        appendVConsole(args.map(x => {
                            if (typeof x === 'string') {
                                return x;
                            }
                            return JSON.stringify(x);
                        }).join(' '));
                    }
                    // 执行用户命令
                    appendVConsole('> ' + cmd);
                    let result = eval(cmd);
                    // 如果不是直接操作控制台的命令，就打印返回值
                    if (!/^(clear\(|log\(|console\.)/.test(cmd)) {
                        appendVConsole('= ' + JSON.stringify(result));
                    }
                } catch (ex) {
                    appendVConsole(ex);
                }
                this.value = '';
            }
        } else if (e.key === 'ArrowUp' && !e.shiftKey) {
            if (commandHistoryPos === null || commandHistoryPos < 0 || commandHistoryPos >= commandHistory.length) {
                commandHistoryPos = commandHistory.length - 1;
            } else if (commandHistoryPos > 0) {
                commandHistoryPos--;
            } else {
                // 到头了
                return;
            }
            if (commandHistoryPos >= 0 && commandHistoryPos < commandHistory.length) {
                this.value = commandHistory[commandHistoryPos];
            }
        } else if (e.key === 'ArrowDown' && !e.shiftKey) {
            if (commandHistoryPos === null || commandHistoryPos < 0 || commandHistoryPos >= commandHistory.length) {
                commandHistoryPos = 0;
            } else if (commandHistoryPos < commandHistory.length - 1) {
                commandHistoryPos++;
            } else { // 到头了
                if (currentCommand !== null) {
                    // 还原当前命令
                    this.value = currentCommand;
                }
                return;
            }
            if (commandHistoryPos >= 0 && commandHistoryPos < commandHistory.length) {
                this.value = commandHistory[commandHistoryPos];
            }
        } else {
            if (this.value.length > 0) {
                // 保存当前命令
                currentCommand = this.value;
            }
        }
    });

    /////////////////// 调整控制台窗口大小 ///////////////////
    var vConsoleContainer = document.getElementById("vConsoleContainer");
    var vConsoleBar = document.getElementById("vConsoleBar");
    var isResizing = false;
    var lastY;

    vConsoleBar.addEventListener("mousedown", function(e) {
        isResizing = true;
        lastY = e.clientY;
    });

    document.addEventListener("mousemove", function(e) {
        if (isResizing) {
            var deltaY = lastY - e.clientY;
            var containerHeight = vConsoleContainer.offsetHeight;

            vConsoleContainer.style.height = containerHeight + deltaY + "px";

            lastY = e.clientY;
        }
    });

    document.addEventListener("mouseup", function(e) {
        if (isResizing) {
            isResizing = false;
        }
    });
}

// 写入虚拟控制台
function appendVConsole(line) {
    let vConsoleOutput = document.querySelector('#vConsoleOutput');
    if (!vConsoleOutput) return;
    vConsoleOutput.value += line;
    vConsoleOutput.value += "\n";
    vConsoleOutput.scrollTop = vConsoleOutput.scrollHeight; // 滚动到末尾
}

function openVConsole() {
    let vConsole = document.getElementById("vConsole");
    let vConsoleButton = document.getElementById("vConsoleButton");
    vConsole.classList.remove("vConsoleHidden");
    vConsoleButton.classList.add("vConsoleHidden");
    window.setTimeout(function () {
        vConsole.style.bottom = "0px";
    }, 10);
}

function closeVConsole() {
    let vConsole = document.getElementById("vConsole");
    let vConsoleButton = document.getElementById("vConsoleButton");
    vConsole.style.bottom = "-400px";
    window.setTimeout(function () {
        vConsoleButton.classList.remove("vConsoleHidden");
        vConsole.classList.add("vConsoleHidden");
    }, 300);
}

function clearVConsole() {
    let vConsoleOutput = document.getElementById("vConsoleOutput");
    vConsoleOutput.value = '';
}

// 初始化
setTimeout(function() {
    cleanConsoleStorage();
    initVConsole();

    // 刷新前自动保存控制台日志以供分析
    window.addEventListener("beforeunload", saveConsoleMessages);
}, 1000);
