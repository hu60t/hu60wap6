{extends file='tpl:admin.layout'}
{block name='title'}
    用户管理
{/block}
{block name='body'}
    <div class="columns">
        <div class="column is-offset-2 is-8">
            <form>
                <div class="columns">
                    <div class="column">
                        <div class="field">
                            <label class="label">排序方式：</label>
                        </div>
                    </div>
                    <div class="column">
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select name="order">
                                        <option value="0" {if $order eq 0}selected{/if}>用户ID_升序</option>
                                        <option value="1" {if $order eq 1}selected{/if}>用户ID_降序</option>
                                        <option value="2" {if $order eq 2}selected{/if}>最后登录时间_升序</option>
                                        <option value="3" {if $order eq 3}selected{/if}>最后登录时间_降序</option>
                                        <option value="4" {if $order eq 4}selected{/if}>发帖数量_升序</option>
                                        <option value="5" {if $order eq 5}selected{/if}>发帖数量_降序</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="control">
                            <button class="button" type="submit">查看</button>
                        </div>
                    </div>
                    <div class="column  is-narrow"></div>
                </div>
            </form>
            <table class="table">
                <thead>
                <tr>
                    <th>用户名</th>
                    <th>最后登录时间</th>
                    <th>发帖数量</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                {foreach $users as $user}
                    <tr>
                        <td>{$user.name}</td>
                        <td>{date("Y-m-d H:i",$user.acctime)}</td>
                        <td>{$user.topic_sum}</td>
                        <td>没啥可操作的</td>
                    </tr>
                {/foreach}
                </tbody>

            </table>
            {$page}
        </div>
    </div>
{/block}
