<?php
// Run this script via command line to install all front-end dependencies (bootstrap, videojs)
require_once('../app/Helpers/Util.php');

use Filehosting\Helpers\Util;

$dependencies = [__DIR__ . "/../vendor/videojs/video.js/dist/video.min.js" => "video.min.js",
    __DIR__ . "/../vendor/videojs/video.js/dist/video-js.swf" => "video-js.swf",
    __DIR__ . "/../vendor/videojs/video.js/dist/video-js.min.css" => "video-js.min.css",
    __DIR__ . "/../vendor/videojs/video.js/dist/font/" => "fonts",
    __DIR__ . "/../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" => "bootstrap.min.css",
    __DIR__ . "/../vendor/twbs/bootstrap/dist/js/bootstrap.min.js" => "bootstrap.min.js",
];

function moveFiles(array $files)
{
    $cssDir = __DIR__ . "/../public/assets/css/";
    $jsDir = __DIR__ . "/../public/assets/js/";
    $flashDir = __DIR__ . "/../public/assets/flash/";
    $fontsDir = __DIR__ . "/../public/assets/css/font/";

    foreach ($files as $filePath => $fileName) {
        if (Util::getFileExtension($fileName) == "js") {
            if (!copy($filePath, $jsDir . $fileName)) {
                exit ("При установке произошла ошибка: проблема с файлом {$fileName}\n");
            }

        } elseif (Util::getFileExtension($fileName) == "swf") {
            if (!copy($filePath, $flashDir . $fileName)) {
                exit ("При установке произошла ошибка: проблема с файлом {$fileName}\n");
            }
        } elseif (Util::getFileExtension($fileName) == "css") {
            if (!copy($filePath, $cssDir . $fileName)) {
                exit ("При установке произошла ошибка: проблема с файлом {$fileName}\n");
            }
        } else {
            $files = array_diff(scandir($filePath), ['.', '..']);
            foreach ($files as $file) {
                if (!copy($filePath . $file, $fontsDir . $file)) {
                    exit ("При установке произошла ошибка: проблема с файлом {$file}\n");
                }
            }
        }
    }
}

moveFiles($dependencies);
exit ("Зависимости установлены!\n");