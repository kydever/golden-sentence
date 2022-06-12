<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Service;

use App\Constants\ErrorCode;
use App\Constants\Event;
use App\Exception\BusinessException;
use EasyWeChat\Work\Application;
use GuzzleHttp\RequestOptions;
use Han\Utils\Service;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;

class WeChatService extends Service
{
    #[Inject]
    protected Application $application;

    #[Inject]
    protected ConfigInterface $config;

    /**
     * 返回所有部门列表.
     */
    public function departments(): array
    {
        $res = $this->application->getClient()->get('cgi-bin/department/list', [
            'query' => [
                'id' => 0,
            ],
        ])->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }

        return $res['department'] ?? [];
    }

    /**
     * 返回部门所有员工.
     * @return [['userid' => '', 'name' => '']]
     */
    public function usersByDepartmentId(int $id): array
    {
        $res = $this->application->getClient()->get('cgi-bin/user/simplelist', [
            'query' => [
                'department_id' => $id,
                'fetch_child' => 1,
            ],
        ])->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }

        return $res['userlist'] ?? [];
    }

    public function setMenu(): void
    {
        $res = $this->application->getClient()->post('/cgi-bin/menu/create', [
            RequestOptions::QUERY => [
                'agentid' => $this->getAgentId(),
            ],
            RequestOptions::JSON => [
                'button' => [
                    [
                        'type' => 'view',
                        'name' => '主页',
                        'url' => 'https://github.com/kydever/golden-sentence',
                    ],
                    [
                        'name' => '快捷入口',
                        'sub_button' => [
                            [
                                'type' => 'click',
                                'name' => '本周金句',
                                'key' => Event::WEEKLY_SENTENCES,
                            ],
                            [
                                'type' => 'click',
                                'name' => '本周统计',
                                'key' => Event::WEEKLY_STATISTICS,
                            ],
                        ],
                    ],
                ],
            ],
        ])->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }
    }

    protected function getAgentId(): int
    {
        return $this->config->get('wechat.default.agent_id');
    }
}
