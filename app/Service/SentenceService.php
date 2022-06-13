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

use App\Service\Dao\SentenceDao;
use Han\Utils\Service;
use Hyperf\Di\Annotation\Inject;

class SentenceService extends Service
{
    #[Inject]
    protected WeChatService $wechat;

    #[Inject]
    protected SentenceDao $dao;

    public function handle(string $openid, string $content): void
    {
        $user = di()->get(UserService::class)->firstByOpenId($openid);

        if (mb_strlen($content) < 80) {
            $this->wechat->sendText($openid, '字数不够');
            return;
        }

        $this->dao->create($user->id, $content);

        $this->wechat->sendText($openid, '感谢您的无私付出，祝生活愉快');
    }
}
