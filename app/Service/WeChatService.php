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

use EasyWeChat\Work\Application;
use Han\Utils\Service;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Codec\Json;

class WeChatService extends Service
{
    #[Inject]
    protected Application $application;

    /**
     * 返回所有部门列表.
     */
    public function departments(): array
    {
        $res = $this->application->getClient()->get('cgi-bin/department/list', [
            'query' => [
                'id' => 0,
            ],
        ]);

        $content = Json::decode($res->getContent(true));

        return $content['department'] ?? [];
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
        ]);

        $content = Json::decode($res->getContent(true));

        return $content['userlist'] ?? [];
    }
}
