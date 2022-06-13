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

use App\Model\Sentence;
use Han\Utils\Service;
use Hyperf\Database\Model\Collection;
use Hyperf\DbConnection\Db;

class SentenceDao extends Service
{
    public function create(int $userId, string $content): bool
    {
        $model = new Sentence();
        $model->user_id = $userId;
        $model->content = $content;
        return $model->save();
    }

    /**
     * @return Collection<int, Sentence>
     */
    public function findByCreatedAt(string $createdAt)
    {
        return Sentence::query()->where('created_at', '>=', $createdAt)
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function countByCreatedAt(string $createdAt): array
    {
        $sql = 'SELECT COUNT(0) AS `cnt`, user_id FROM sentences WHERE created_at >= ? GROUP BY user_id;';

        $res = Db::select($sql, [$createdAt]);
        $result = [];
        foreach ($res as $item) {
            $result[$item->user_id] = $item->cnt;
        }
        return $result;
    }
}
