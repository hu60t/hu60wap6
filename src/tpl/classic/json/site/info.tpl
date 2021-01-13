{config_load file="conf:site.info"}
{JsonPage::start()}
{$data=[
    'SITE_URL_PREFIX' => $SITE_URL_PREFIX,
    'SITE_NAME' => #SITE_NAME#,
    'SITE_SIMPLE_NAME' => #SITE_SIMPLE_NAME#,
    'BBS_NAME' => #BBS_NAME#,
    'BBS_INDEX_NAME' => #BBS_INDEX_NAME#,
    'CLOCK_NAME' => #CLOCK_NAME#,
    'SITE_REG_ENABLE' => $SITE_REG_ENABLE,
    'SITE_REG_CLOSE_REASON' => "{if not $SITE_REG_ENABLE}{#SITE_REG_CLOSE_REASON#}{/if}",
    'SITE_RECORD_NUMBER' => #SITE_RECORD_NUMBER#
]}
{JsonPage::output($data)}