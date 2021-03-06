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
namespace App\Service\Dao;

use App\Model\User;
use Han\Utils\Service;
use Hyperf\Database\Model\Collection;

class UserDao extends Service
{
    public function firstByOpenId(string $openid): ?User
    {
        return User::query()->where('openid', $openid)->first();
    }

    /**
     * @return Collection<int, User>
     */
    public function all()
    {
        return User::query()->where('is_deleted', 0)->get();
    }
}
