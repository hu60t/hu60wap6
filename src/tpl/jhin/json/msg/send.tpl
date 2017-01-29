{JsonPage::start()}

{if $send}
	{$jsonData = ['success'=>true]}
{else if $send === false}
	{$jsonData = ['success'=>false]}
{else if $error !== null}
	{$jsonData = ['success'=>false, 'notice'=>$error->getMessage()]}
{else}
	{$jsonData = []}
{/if}
{if $send !== true}
	{$jsonData.toUid = $toUser->uid}
	{$jsonData.toUinfo = ['name'=>$toUser->name]}
{/if}

{JsonPage::output($jsonData)}