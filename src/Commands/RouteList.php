<?php
/**
 * This file is part of T2-Engine.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Tony<dev@t2engine.cn>
 * @copyright Tony<dev@t2engine.cn>
 * @link      https://www.t2engine.cn/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
declare(strict_types=1);

namespace T2\Console\Commands;

use Closure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use App\Route;

class RouteList extends Command
{
    /**
     * @var string
     */
    protected static string $defaultName = 'route:list';

    /**
     * @var string
     */
    protected static string $defaultDescription = 'Route list';

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $headers = ['uri', 'method', 'callback', 'middleware', 'name'];
        $rows    = [];
        foreach (Route::getRoutes() as $route) {
            foreach ($route->getMethods() as $method) {
                $cb     = $route->getCallback();
                $cb     = $cb instanceof Closure ? 'Closure' : (is_array($cb) ? json_encode($cb) : var_export($cb, true));
                $rows[] = [$route->getPath(), $method, $cb, json_encode($route->getMiddleware() ?: null), $route->getName()];
            }
        }

        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
        return self::SUCCESS;
    }
}