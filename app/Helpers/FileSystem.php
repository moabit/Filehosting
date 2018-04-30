<?php


namespace Filehosting\Helpers;


use Filehosting\Exceptions\FileSystemException;

/**
 * Class FileSystem
 * @package Filehosting\Helpers
 */
class FileSystem
{
    /**
     * @var mixed
     */
    public $rootDir;

    /**
     * FileSystem constructor.
     * @param string $initDir
     */
    public function __construct(string $initDir)
    {

        $this->rootDir = str_replace(DIRECTORY_SEPARATOR . "app", "", $initDir);
    }

    /**
     * @param \Filehosting\Models\File $file
     * @return string
     */
    public function getAbsolutePathToFile(\Filehosting\Models\File $file): string
    {
        $path = "{$this->generatePathToStorage($file->uploaded)}/{$this->generateStorageFilename($file)}";
        if (!file_exists($path)) {
           throw new FileSystemException('Файл отсутствует в хранилище');
       }
        return $path;
    }

    /**
     * @param \Slim\Http\UploadedFile $file
     * @param \Filehosting\Models\File $model
     * @throws FileSystemException
     */
    public function moveUploadedFileToStorage(\Slim\Http\UploadedFile $file, \Filehosting\Models\File $model) //void
    {
        $storageName = $this->generateStorageFilename($model);
        $path = "{$this->generatePathToStorage()}/{$storageName}";
        $file->moveTo($path);
    }

    public function generateThumbnail(\Filehosting\Models\File $image, string $absolutePathToImage) // void
    {
        if (!$image->isImage()) {
            throw new FileSystemException('Файл не является изображением');
        }
        $thumbnail = new \Imagick ($absolutePathToImage);
        $thumbnail->thumbnailImage(200, 0);
        $path = "{$this->generatePathToStorage($image->uploaded)}/thumbnail_{$image->id}_{$image->safe_name}";
        $thumbnail->writeImage($path);
    }

    public function getPathToThumbnail(\Filehosting\Models\File $image): string
    {
        if (!($image->isImage())) {
            throw new FileSystemException('Файл не является изображением');
        }
        $path = "{$this->generatePathToStorage($image->uploaded)}/{$this->generateStorageThumbnailName ($image)}";
        if (!file_exists($path)) {
            throw new FileSystemException('Картинка для предпросмотра отсутствует в хранилище');
        }
        return $path;
    }

    /**
     * Throws FileSystemException if a directory wasn't createad
     * @return string
     * @throws FileSystemException
     */
    private function generatePathToStorage($date = null): string
    {
        if ($date) {
            $uploadDate = new \DateTime ($date);
            $uploadDate = $uploadDate->format('d-M-Y');
        } else {
            $timestamp = new \DateTime ('now');
            $uploadDate= $timestamp->format('d-M-Y');
        }
        $path = "{$this->rootDir}/storage/{$uploadDate}";
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new FileSystemException('Не удалось создать директорию');
            }
        }
        return $path;
    }

    /**
     * @param \Filehosting\Models\File $model
     * @return string
     */
    private function generateStorageFilename(\Filehosting\Models\File $file): string
    {
        return "{$file->id}_{$file->safe_name}";
    }

    private function generateStorageThumbnailName (\Filehosting\Models\File $image) :string
    {
        return "thumbnail_{$image->id}_{$image->safe_name}";
    }
}