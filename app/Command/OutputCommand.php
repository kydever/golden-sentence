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
namespace App\Command;

use App\Service\Dao\SentenceDao;
use App\Service\Dao\UserDao;
use App\Service\SentenceService;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class OutputCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('output');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
        $this->addArgument('date', InputArgument::REQUIRED, '日期');
    }

    public function handle()
    {
        $beginAt = $this->input->getArgument('date');

        $users = di()->get(UserDao::class)->all()->getDictionary();
        $contents = di()->get(SentenceDao::class)->findByCreatedAt($beginAt);

        $path = di()->get(SentenceService::class)->exportCSVToFile($contents, $users);

        echo $path . PHP_EOL;
    }
}
