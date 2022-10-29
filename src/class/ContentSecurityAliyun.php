<?php
require_once ROOT_DIR . '/nonfree/class/AliyunGreen/vendor/autoload.php';

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Green\Green;

/**
 * 阿里云内容安全
 */
class ContentSecurityAliyun extends ContentSecurityBase {
    public function __construct($user = null) {
        parent::__construct($user);

        AlibabaCloud::accessKeyClient(CONTENT_SECURITY_AK, CONTENT_SECURITY_SK)
            ->timeout(CONTENT_SECURITY_TIMEOUT)
            ->connectTimeout(CONTENT_SECURITY_TIMEOUT)
            ->regionId(ALIYUN_GREEN_REGION)
            ->asDefaultClient();
    }

    protected function getBizType($type) {
        switch ($type) {
            case ContentSecurity::TYPE_NAME:
                return ALIYUN_GREEN_BIZ_TYPE_NAME;
            case ContentSecurity::TYPE_SIGNATURE:
                return ALIYUN_GREEN_BIZ_TYPE_SIGNATURE;
            case ContentSecurity::TYPE_TOPIC:
                return ALIYUN_GREEN_BIZ_TYPE_TOPIC;
            case ContentSecurity::TYPE_REPLY:
                return ALIYUN_GREEN_BIZ_TYPE_REPLY;
            case ContentSecurity::TYPE_CHAT:
                return ALIYUN_GREEN_BIZ_TYPE_CHAT;
            default:
                return ALIYUN_GREEN_BIZ_TYPE_TOPIC;
        }
    }

    protected function getReasonName($reasonId) {
        switch ($reasonId) {
            case 'normal':
                return '内容正常';
            case 'spam':
                return '含垃圾信息';
            case 'ad':
                return '属于广告';
            case 'politics':
                return '涉政';
            case 'terrorism':
                return '含暴恐信息';
            case 'abuse':
                return '含辱骂信息';
            case 'porn':
                return '含色情信息';
            case 'flood':
                return '属于灌水';
            case 'contraband':
                return '包含违禁内容';
            case 'meaningless':
                return '无意义';
            case 'harmful':
                return '含不良场景';
            case 'customized':
                return '命中自定义关键词';
        }
    }

    // 获取审核状态
    protected function getReviewStat($suggestion, $label) {
        switch ($suggestion) {
            case 'pass':
                return ContentSecurity::STAT_PASS;
            case 'review':
                return ContentSecurity::STAT_REVIEW;
        }

        // 下面是 $suggestion == 'block' 的情况
        switch ($label) {
            case 'normal':
                return ContentSecurity::STAT_PASS;
            // 类型不严重就人工复核
            case 'spam':
            case 'ad':
            case 'abuse':
            case 'flood':
            case 'meaningless':
            case 'harmful':
            case 'customized':
                return ContentSecurity::STAT_REVIEW;
            // 类型严重就直接屏蔽
            case 'politics':
            case 'terrorism':
            case 'porn':
            case 'contraband':
                return ContentSecurity::STAT_BLOCK;
            default:
                return ContentSecurity::STAT_REVIEW;
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
    public function auditText($type, $text, $contentTag = null) {
        $task = [
            'dataId' => $this->genContentId($type, $contentTag, $this->user->uid),
            'content' => $text,
        ];

        $raw = Green::v20180509()->textScan()->body(json_encode([
            'tasks' => [$task],
            'scenes' => ['antispam'],
            'bizType' => $this->getBizType($type),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        ->scheme('https')->request()->toArray();

        if (CONTENT_SECURITY_LOG) {
            ContentSecurity::log($task['dataId'], $raw);
        }

        if ($raw['code'] != 200) {
            return [
                'success' => false,
                'stat' => ContentSecurity::STAT_REVIEW,
                'rate' => 0,
                'reason' => '机审接口报错',
                'raw' => $raw,
            ];
        }

        $result = $raw['data'][0]['results'][0];

        return [
            'success' => true,
            'stat' => $this->getReviewStat($result['suggestion'], $result['label']),
            'rate' => $result['rate'],
            'reason' => $this->getReasonName($result['label']),
            'raw' => $raw,
        ];
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
    public function auditTextBatch($type, $tasks) {
        foreach ($tasks as &$task) {
            $task = [
                'dataId' => $this->genContentId($type, $task['contentTag'], $task['user']->uid),
                'content' => $task['text'],
            ];
        }

        $raw = Green::v20180509()->textScan()->body(json_encode([
            'tasks' => $tasks,
            'scenes' => ['antispam'],
            'bizType' => $this->getBizType($type),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        ->scheme('https')->request()->toArray();

        if (CONTENT_SECURITY_LOG) {
            ContentSecurity::log('batch/'.$this->getTypeName($type).'/'.time(), $raw);
        }

        $success = true;
        $results = [];
        for ($i=0; $i<count($tasks); $i++) {
            if ($raw['code'] != 200) {
                $success = false;
                $results[] = [
                    'success' => false,
                    'stat' => ContentSecurity::STAT_REVIEW,
                    'rate' => 0,
                    'reason' => '机审接口报错',
                ];
            } else {
                $result = $raw['data'][$i]['results'][0];
                $results[] = [
                    'success' => true,
                    'stat' => $this->getReviewStat($result['suggestion'], $result['label']),
                    'rate' => $result['rate'],
                    'reason' => $this->getReasonName($result['label']),
                ];
            }
        }

        return [
            'success' => $success,
            'results' => $results,
            'raw' => $raw,
        ];
    }
}
