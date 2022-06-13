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

use App\Model\User;
use App\Service\Dao\UserDao;
use Han\Utils\Service;
use Hyperf\Di\Annotation\Inject;

class UserService extends Service
{
    #[Inject]
    protected UserDao $dao;

    public function syncFromWeChat(): void
    {
        $id = (int) env('WORK_DEPARTMENT_ID', 0);
        $users = di()->get(WeChatService::class)->usersByDepartmentId($id);
        foreach ($users as $user) {
            $model = $this->dao->firstByOpenId($user['userid']);
            if (! $model) {
                $model = new User();
                $model->openid = $user['userid'];
            }

            $model->name = $user['name'];
            $model->save();
        }
    }

    public function firstByOpenId(string $openid): User
    {
        $model = $this->dao->firstByOpenId($openid);
        if (! $model) {
            $data = di()->get(WeChatService::class)->userInfo($openid);
            $model = new User();
            $model->openid = $data['userid'];
            $model->name = $data['name'];
            $model->save();
        }

        return $model;
    }
}
