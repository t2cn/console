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

namespace T2\Console;

use App\Container;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command as Commands;
use Symfony\Component\Console\Application;

class Command extends Application
{
    /**
     * @return void
     */
    public function installInternalCommands(): void
    {
        $this->installCommands(__DIR__ . '/Commands', 'T2\Console\Commands');
    }

    /**
     * @param $path
     * @param string $namspace
     * @return void
     */
    public function installCommands($path, string $namspace = 'app\command'): void
    {
        $dir_iterator = new RecursiveDirectoryIterator($path);
        $iterator     = new RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            /**
             * @var SplFileInfo $file
             */
            if (str_starts_with($file->getFilename(), '.')) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }
            // abc\def.php
            $relativePath = str_replace(str_replace('/', '\\', $path . '\\'), '', str_replace('/', '\\', $file->getRealPath()));
            // app\command\abc
            $realNamespace = trim($namspace . '\\' . trim(dirname(str_replace('\\', DIRECTORY_SEPARATOR, $relativePath)), '.'), '\\');
            $realNamespace = str_replace('/', '\\', $realNamespace);
            // app\command\doc\def
            $class_name = trim($realNamespace . '\\' . $file->getBasename('.php'), '\\');
            if (!class_exists($class_name) || !is_a($class_name, Commands::class, true)) {
                continue;
            }
            $reflection = new ReflectionClass($class_name);
            if ($reflection->isAbstract()) {
                continue;
            }
            $properties = $reflection->getStaticProperties();
            $name       = $properties['defaultName'] ?? null;
            if (!$name) {
                throw new RuntimeException("Command {$class_name} has no defaultName");
            }
            $description = $properties['defaultDescription'] ?? '';
            $command     = Container::get($class_name);
            $command->setName($name)->setDescription($description);
            $this->add($command);
        }
    }
}