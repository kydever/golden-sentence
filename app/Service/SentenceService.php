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

use App\Constants\Event;
use App\Service\Dao\SentenceDao;
use App\Service\Dao\UserDao;
use Carbon\Carbon;
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

    public function handleEvent(string $openid, string $event): void
    {
        $beginAt = $this->getStartDayOfWeek();
        switch ($event) {
            case Event::WEEKLY_STATISTICS:
                // 返回当周统计数据
                $users = di()->get(UserDao::class)->all();
                $data = di()->get(SentenceDao::class)->countByCreatedAt($beginAt);
                $content = '';
                foreach ($users as $user) {
                    $content .= sprintf('%s 本周共 %d 条', $user->name, $data[$user->id] ?? 0) . PHP_EOL;
                }
                $this->wechat->sendText($openid, $content);
                break;
            case Event::WEEKLY_SENTENCES:
                // 返回当周所有金句
                $contents = di()->get(SentenceDao::class)->findByCreatedAt($beginAt);
                $content = '';
                foreach ($contents as $item) {
                    $content .= $item . PHP_EOL;
                }
                $this->wechat->sendText($openid, $content);
                break;
        }
    }

    public function getStartDayOfWeek(?Carbon $today = null): string
    {
        $now = $today ?? Carbon::today();
        return $now->subDays($now->dayOfWeek)->toDateTimeString();
    }
}
