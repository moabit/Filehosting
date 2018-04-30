<?php


namespace Filehosting\Helpers;

use Filehosting\Exceptions\ConfigException;


class Util
{
    public static function readJSON($JSONpath):array
    {
        if (!file_exists($JSONpath)) {
            throw new ConfigException('Файл конфигурации не существует');
        }
        $fileContent = file_get_contents($JSONpath);
        $fileContent = json_decode($fileContent, true);
        if ($fileContent == null) {
            throw new ConfigException('Ошибка в файле конфигурации. Ошибка: ' . json_last_error_msg());
        }
        return $fileContent;
    }

    public static function generateToken ($length = 16) : string
    {
        return $token = bin2hex(random_bytes($length));
    }
    // протестить строковые функции


    public static function generateSafeFilename (string $normalizedFilename)
    {
        $safeName= transliterator_transliterate('Any-Latin; Latin-ASCII', $normalizedFilename);
        $safeName=self::normalizeFilename($safeName);
        return  preg_replace('/.(htaccess|php|html|phtml)$/', '.txt', $safeName);
    }

    public static function normalizeFilename($filename)
    {
        $ext=self::getFileExtension($filename);
        if (mb_strlen($ext)>10) {
            $filename = substr($filename, 0, 150);
        }
        elseif (mb_strlen($filename) > 150) {
            $filename = substr($filename, 0, 150 - (mb_strlen($ext)+1)) . '.' . $ext;
        }
        return $filename;
    }

    private static function getFileExtension (string $filename)
    {
        if (!strpos($filename, '.')){
            return '';
        }
        preg_match('/.([^\.]+$)/i', $filename, $extension);
        return $extension[1];
    }
}