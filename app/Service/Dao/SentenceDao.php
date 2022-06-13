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

class SentenceDao extends Service
{
    public function create(int $userId, string $content): bool
    {
        $model = new Sentence();
        $model->user_id = $userId;
        $model->content = $content;
        return $model->save();
    }
}
