<?php
/**
 * 内容安全服务的基类
 * 
 * 新增的内容安全服务应该继承该类
 */
abstract class ContentSecurityBase {
    protected $user = null;

    public function __construct($user = null) {
        $this->user = $user;
        if (!is_object($this->user)) {
            $this->user = new UserInfo;
        }
    }

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
    abstract public function auditText($type, $text, $contentTag = null);

    /**
     * 批量审核文本
     * 
     * @param $type ContentSecurity::TYPE_*
     * @param $tasks 待审核任务
     *  [
     *      [
     *          'user' => object UserInfo, // 用户信息对象
     *          'text' => string // 待审核文本（UBB原文）
     *          'contentTag' => string // 用于区分内容来源的标识
     *      ],
     *      ...
     *  ]
     * 
     * @return [
     *     // 审核是否顺利完成
     *     'success' => true | false,
     *     'results' => [
     *          [
     *              // 审核是否顺利完成
     *              'success' => true | false,
     *              // 审核状态
     *              'stat' => ContentSecurity::STAT_PASS | ContentSecurity::STAT_REVIEW | ContentSecurity::STAT_BLOCK,
     *              // 状态得分
     *              'rate' => 0 - 100,
     *              // 原因
     *              'reason' => string,
     *          ],
     *          ...
     *      ],
     *     // 审核接口返回的原始结果
     *     'raw' => mixed,
     * ]
     */
    abstract public function auditTextBatch($type, $tasks);

    protected function getTypeName($type) {
        switch ($type) {
            case ContentSecurity::TYPE_NAME:
                return 'name';
            case ContentSecurity::TYPE_SIGNATURE:
                return 'signature';
            case ContentSecurity::TYPE_TOPIC:
                return 'topic';
            case ContentSecurity::TYPE_REPLY:
                return 'reply';
            case ContentSecurity::TYPE_CHAT:
                return 'chat';
            default:
                return 'text';
        }
    }

    protected function genContentId($type, $contentTag, $uid) {
        if (empty($contentTag)) {
            $contentTag = $this->getTypeName($type);
        }
        return  $contentTag . '/' . (int)($uid) . '/' . time();
    }
}
