{JsonPage::start()}
{$jsonData = []}
{if $wechat.uid}
    {$jsonData.wechat = [
        'time' => $wechat.time,
        'userName' => $wechat.userName,
        'userHeadImg' => $wechat.userHeadImg
    ]}
{/if}
{if $qrcode}
    {$jsonData.qrcode = $qrcode}
{/if}
{JsonPage::output($jsonData)}