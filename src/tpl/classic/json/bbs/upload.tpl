{JsonPage::start()}
{if !isset($ex)}
    {$jsonData = [
        'success' => true,
        'url' => $url,
        'name' => $name,
        'size' => $size,
        'content' => $content
	]}
{else}
	{$jsonData = [
        'success' => false,
        'notice' => $ex->getMessage()
    ]}
{/if}
{JsonPage::output($jsonData)}
