{JsonPage::start()}
{$jsonData = []}
{if $wechat.uid}
    {$jsonData.wechat = [
        'time' => $wechat.time,
        'source' => $wechat.source,
        'userName' => $wechat.userName,
        'userHeadImg' => $wechat.userHeadImg
    ]}
{/if}
{if $qrcode}
    {$jsonData.qrcode = $qrcode}
{/if}
{JsonPage::output($jsonData)}