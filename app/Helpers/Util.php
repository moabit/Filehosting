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

    public static function generateToken ($length = 16)
    {
        return $token = bin2hex(random_bytes($length));
    }

    public static function normalizeFilename( $filename)
    {
        if (mb_strlen($filename) > 150) {
            preg_match('/\.[^\.]+$/i', $filename, $extension);
            $filename = substr($filename, 0, 150 - mb_strlen($extension[0])) . $extension[0];
        }

        return $filename;

    }

    public static function generateSafeFilename (string $normalizedFilename)
    {
        $safeName= transliterator_transliterate('Any-Latin; Latin-ASCII', $normalizedFilename);
        $safeName=self::normalizeFilename($safeName);
        return  preg_replace('/.(htaccess|php|html|phtml)$/', '.txt', $safeName);
    }

    public static function getFileExtension (string $filename)
    {
        preg_match('/\.[^\.]+$/i', $filename, $extension);
        return $extension[0];
    }
}