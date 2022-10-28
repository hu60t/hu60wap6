<?php
/**
 * 关闭机审
 * 
 * 所有审核项目始终返回成功
 */
class ContentSecurityNone extends ContentSecurityBase {
    /**
     * 审核文本
     * 
     * @param $type 内容类型
     * @param $text 待审核文本（UBB原文）
     * @param $contentTag 用于区分内容来源的标识
     * 
     * @return [
     *     // 审核是否顺利完成
     *     'success' => true | false,
     *     // 审核状态
     *     'stat' => ContentSecurity::STAT_PASS | ContentSecurity::STAT_REVIEW | ContentSecurity::STAT_BLOCK,
     *     // 状态得分
     *     'rate' => 0 - 100,
     *     // 原因
     *     'reason' => string,
     *     // 审核接口返回的原始结果
     *     'raw' => mixed,
     * ]
     */
    public function auditText($type, $text, $contentId = null) {
        return [
            'success' => false,
            'stat' => ContentSecurity::STAT_PASS,
            'rate' => 0,
            'reason' => '机审未启用',
            'raw' => null,
        ];
    }
}
