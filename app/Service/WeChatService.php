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
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;
use EasyWeChat\Work\Application;
use GuzzleHttp\RequestOptions;
use Han\Utils\Service;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use JetBrains\PhpStorm\ArrayShape;

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

    #[ArrayShape(['userid' => 'string', 'name' => 'string'])]
    public function userInfo(string $openid): array
    {
        $res = $this->application->getClient()->get('cgi-bin/user/get', [
            'query' => [
                'userid' => $openid,
            ],
        ])->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }

        return $res;
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
                                'key' => Event::WEEKLY_SENTENCES_OPTIONS,
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

    public function sendText(string $openId, string $content): void
    {
        $res = $this->application->getClient()->post('/cgi-bin/message/send', [
            RequestOptions::JSON => [
                'msgtype' => 'text',
                'agentid' => $this->getAgentId(),
                'text' => [
                    'content' => $content,
                ],
                'touser' => $openId,
            ],
        ])->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }
    }

    public function uploadMedia(string $path, ?string $name = null): string
    {
        $options = Form::create([
            'media' => File::fromPath($path, $name),
        ])->toArray();

        $res = $this->application->getClient()->post('/cgi-bin/media/upload', array_merge([
            RequestOptions::QUERY => [
                'type' => 'file',
            ],
        ], $options))->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }

        return $res['media_id'];
    }

    public function sendTemplateCard(string $openId, array $card): void
    {
        $res = $this->application->getClient()->post('/cgi-bin/message/send', [
            RequestOptions::JSON => [
                'msgtype' => 'template_card',
                'agentid' => $this->getAgentId(),
                'template_card' => $card,
                'touser' => $openId,
            ],
        ])->toArray();

        if ($res['errcode'] !== 0) {
            throw new BusinessException(ErrorCode::SERVER_ERROR, $res['errmsg']);
        }
    }

    public function sendMedia(string $openId, string $mediaId): void
    {
        $res = $this->application->getClient()->post('/cgi-bin/message/send', [
            RequestOptions::JSON => [
                'msgtype' => 'file',
                'agentid' => $this->getAgentId(),
                'file' => [
                    'media_id' => $mediaId,
                ],
                'touser' => $openId,
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
