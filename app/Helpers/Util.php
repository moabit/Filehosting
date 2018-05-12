<?php


namespace Filehosting\Helpers;

use Filehosting\Exceptions\ConfigException;


class Util
{
    /**
     * Reads JSON config file and returns array with config settings
     * If config file doesn't exist or there is an error, throws ConfigException
     *
     * @param $JSONpath
     * @return array
     * @throws ConfigException
     */
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

    /**
     * Returns random string token
     *
     * @param int $length
     * @return string
     */
    public static function generateToken ($length = 16) : string
    {
        return $token = bin2hex(random_bytes($length));
    }

    /**
     * Transliterates filename
     * If file extension is not safe to store on server, changes it to .txt
     *
     * @param string $normalizedFilename
     * @return null|string|string[]
     */
    public static function generateSafeFilename (string $normalizedFilename)
    {
        $safeName= transliterator_transliterate('Any-Latin; Latin-ASCII', $normalizedFilename);
        // normalizes file's name one more time because after transliteration it can contain more characters than allowed
        $safeName=self::normalizeFilename($safeName);
        return  preg_replace('/.(htaccess|php|html|phtml)$/', '.txt', $safeName);
    }

    /**
     * Checks if a filename contains more than 150 characters
     * If it does, shortens it
     *
     * @param $filename
     * @return bool|string
     */
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

    /**
     *
     * @param string $filename
     * @return string
     */
    private static function getFileExtension (string $filename)
    {
        if (!strpos($filename, '.')){
            return '';
        }
        preg_match('/.([^\.]+$)/i', $filename, $extension);
        return $extension[1];
    }
}