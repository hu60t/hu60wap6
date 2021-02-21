<form method="get" action="{$CID}.{$PID}.{$BID}" class="search-form">
    <input name="keywords" value="{$smarty.get.keywords|code}" class="search-form-keyword" placeholder="搜索词"/>
    <input name="username" value="{$smarty.get.username|code}" class="serch-form-user" placeholder="用户名"/>
    <input type="submit" class="search-form-submit" value="搜索"/>
    <div>
        <label for="searchType" id="searchType-label">
            <input name="searchType" id="searchType" type="checkbox" value="reply" {if $smarty.get.searchType=='reply'}checked{/if} />搜索用户回复
        </label>
        {if $USER->hasPermission(userinfo::PERMISSION_REVIEW_POST)}
            <label>
                <input name="onlyReview" type="checkbox" value="1" {if $smarty.get.onlyReview == 1}checked{/if} />仅看待审核
            </label>
            <label>
                <input name="onlyReview" type="checkbox" value="-1" {if $smarty.get.onlyReview == -1}checked{/if} />我审核的内容
            </label>
            <label>
                <input name="onlyReview" type="checkbox" value="3" {if $smarty.get.onlyReview == 3}checked{/if} />未审核通过
            </label>
            <label>
                <input name="onlyReview" type="checkbox" value="2" {if $smarty.get.onlyReview == 2}checked{/if} />被站长屏蔽
            </label>
        {/if}
    </div>
</form>
