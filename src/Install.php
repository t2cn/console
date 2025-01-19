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

class Install
{
    const bool T2_INSTALL = true;

    /**
     * @var array 目录关系映射
     * 用于定义源路径和目标路径之间的关系，用于安装和卸载时的文件操作。
     */
    protected static array $pathRelation = [
        'config' => 'config',
    ];

    /**
     * 安装方法
     * 检查是否已存在指定的安装目录，如果存在则提示失败，否则执行安装逻辑。
     * @return void
     */
    public static function install(): void
    {
        $dest = base_path() . "/engine"; // 定义目标安装目录

        // 检查目标目录是否已存在
        if (is_dir($dest)) {
            echo "Installation failed, please remove directory $dest\n";
            return;
        }

        // 复制安装文件到目标目录
        copy(__DIR__ . "/engine", $dest);

        // 设置目标目录权限
        chmod(base_path() . "/engine", 0755);

        // 执行基于路径关系的安装逻辑
        static::installByRelation();
    }

    /**
     * 卸载方法
     * 删除安装目录和相关文件。
     * @return void
     */
    public static function uninstall(): void
    {
        // 检查并删除目标安装文件
        if (is_file(base_path() . "/engine")) {
            unlink(base_path() . "/engine");
        }

        // 执行基于路径关系的卸载逻辑
        self::uninstallByRelation();
    }

    /**
     * 根据路径关系安装相关文件
     * 遍历路径关系数组，将源文件复制到目标位置。
     * @return void
     */
    public static function installByRelation(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            // 解析目标路径的父级目录
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);

                // 如果父级目录不存在则创建
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }

            // 将源目录内容复制到目标目录
            copy_dir(__DIR__ . "/$source", base_path() . "/$dest");
        }
    }

    /**
     * 根据路径关系卸载相关文件
     * 遍历路径关系数组，仅删除目标路径下的 `console.php` 文件。
     * @return void
     */
    public static function uninstallByRelation(): void
    {
        foreach (static::$pathRelation as $dest) {
            $file = base_path() . "/$dest/console.php"; // 目标文件路径

            // 如果目标文件存在，删除该文件
            if (is_file($file)) {
                unlink($file);
                echo "Deleted: $file\n";
            }
        }
    }
}