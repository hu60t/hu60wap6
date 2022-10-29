<?php
/**
 * 内容安全
 * 
 * 提供文本、图片等内容的机审功能
 */
class ContentSecurity {
    // 机审操作者UID
    const ACTION_UID = -100;

    // 支持的服务
    const SERVICE_NONE   = 0; // 关闭机审
    const SERVICE_ALIYUN = 1; // 阿里云内容安全

    // 审核状态
    const STAT_PASS   = 1; // 通过
    const STAT_REVIEW = 2; // 人工复核
    const STAT_BLOCK  = 3; // 屏蔽

    // 内容类型
    const TYPE_NAME      = 1; // 用户名
    const TYPE_SIGNATURE = 2; // 个性签名
    const TYPE_TOPIC     = 3; // 主题帖
    const TYPE_REPLY     = 4; // 回帖
    const TYPE_CHAT      = 5; // 聊天室

    public static function getInstance($user = null) {
        switch (CONTENT_SECURITY_SERVICE) {
            case self::SERVICE_NONE:
                return new ContentSecurityNone($user);
            case self::SERVICE_ALIYUN:
                return new ContentSecurityAliyun($user);
            default:
                throw new Exception("未知的内容安全类型: ".CONTENT_SECURITY_SERVICE, 500);
        }
    }

    protected static function getForumReviewStat($stat) {
        switch ($stat) {
            case ContentSecurity::STAT_PASS:
                return bbs::REVIEW_MACHINE_PASS;
            case ContentSecurity::STAT_REVIEW:
                return bbs::REVIEW_NEED_MANUAL_REVIEW;
            case ContentSecurity::STAT_BLOCK:
                return bbs::REVIEW_MACHINE_BLOCK;
        }
    }

    /**
     * 审核文本
     * 
     * @param $user 当前用户
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
    public static function auditText($user, $type, $text, $contentTag = null) {
        try {
            return self::getInstance($user)->auditText($type, $text, $contentTag);
        } catch (Throwable $ex) {
            self::log('exception', $ex);
            return [
                'success' => false,
                'stat' => ContentSecurity::STAT_REVIEW,
                'rate' => 0,
                'reason' => '机审代码报错',
                'raw' => $ex,
            ];
        }
    }

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
    public static function auditTextBatch($type, $tasks) {
        try {
            return self::getInstance()->auditTextBatch($type, $tasks);
        } catch (Throwable $ex) {
            self::log('exception', $ex);

            $results = [];

            for ($i=0; $i<count($tasks); $i++) {
                $results[] = [
                    'stat' => ContentSecurity::STAT_PASS,
                    'rate' => 0,
                    'reason' => '机审代码报错',
                ];
            }

            return [
                'success' => false,
                'results' => $results,
                'raw' => null,
            ];
        }
    }

    public static function getReviewComment($reviewResult) {
        $comment = $reviewResult['reason'];
        if ($reviewResult['success']) {
            $comment .= "（置信度：$reviewResult[rate]%）";
        }
        return $comment;
    }

    public static function getReviewLog($reviewResult) {
        // 未开启机审
        if (CONTENT_SECURITY_SERVICE == self::SERVICE_NONE) {
            return null;
        }
        // 开启了机审
        return [
            'time' => time(),
            'uid' => self::ACTION_UID,
            'stat' => self::getForumReviewStat($reviewResult['stat']),
            'comment' => self::getReviewComment($reviewResult),
        ];
    }

    public static function log($id, $object) {
        file_put_contents(
            CONTENT_SECURITY_LOG,
            date('[Y-m-d H:i:s] ').$id.' '.json_encode(
                $object,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )."\n",
            FILE_APPEND
        );
    }
}
