{extends file='tpl:admin.layout'}
{block name='title'}
    用户管理
{/block}
{block name='body'}
    <div class="row row-mobile">
        <div class="col-4-2"></div>
        <div class="col-4-2">
            排序方式：
            <form>
            <select name="order">
                <option value = "0"{if $order eq 0}selected{/if}>用户ID_升序</option>
                <option value = "1"{if $order eq 1}selected{/if}>用户ID_降序</option>
                <option value = "2"{if $order eq 2}selected{/if}>最后登录时间_升序</option>
                <option value = "3"{if $order eq 3}selected{/if}>最后登录时间_降序</option>
                <option value = "4"{if $order eq 4}selected{/if}>发帖数量_升序</option>
                <option value = "5"{if $order eq 5}selected{/if}>发帖数量_降序</option>
            </select>
                <button type="submit">查看</button>
            </form>
        </div>
        <div class="col-4-4">
            <table class="list">
                <tr>
                    <th>用户名</th>
                    <th>最后登录时间</th>
                    <th>发帖数量</th>
                    <th>操作</th>
                </tr>
                {foreach $users as $user}
                    <tr>
                        <td>{$user.name}</td>
                        <td>{date("Y-m-d H:i",$user.acctime)}</td>
                        <td>{$user.topic_sum}</td>
                        <td>没啥可操作的</td>
                    </tr>
                {/foreach}
            </table>
            {$page}
        </div>
    </div>
{/block}
