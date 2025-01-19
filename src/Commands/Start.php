<?php
/**
 * This file is part of t2.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Tony<lucky@t2engine.cn>
 * @copyright Tony<lucky@t2engine.cn>
 * @link      http://www.t2engine.cn/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace T2\Console\Commands;

use App\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{
    /**
     * @var string
     */
    protected static string $defaultName = 'start';

    /**
     * @var string
     */
    protected static string $defaultDescription = 'Start worker in DEBUG mode. Use mode -d to start in DAEMON mode.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addOption('daemon', 'd', InputOption::VALUE_NONE, 'DAEMON mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Application::run();
        return self::SUCCESS;
    }
}