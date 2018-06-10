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
     * Returns absolute path to file in the storage
     * If a file doesn't exist throws FileSystemException
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
     * Moves file to the storage
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

    /**
     * @param \Filehosting\Models\File $image
     * @param string $absolutePathToImage
     * @throws FileSystemException
     * @throws \ImagickException
     */
    public function generateThumbnail(\Filehosting\Models\File $image, string $absolutePathToImage) // void
    {
        if (!$image->isImage()) {
            throw new FileSystemException('Файл не является изображением');
        }
        $thumbnail = new \Imagick ($absolutePathToImage);
        $thumbnail->thumbnailImage(200, 200, true);
        $path = "{$this->generateAbsolutePathToThumbnail($image->uploaded)}/{$this->generateStorageFilename($image)}"; // здесь функция
        $thumbnail->writeImage($path);
    }

    /**
     * Returns an absolute path to image's thumbnail
     * @param \Filehosting\Models\File $image
     * @return string
     * @throws FileSystemException
     */
    public function getPathToThumbnail(\Filehosting\Models\File $image): string
    {
        if (!($image->isImage())) {
            throw new FileSystemException('Файл не является изображением');
        }
        $uploadDate = new \DateTime($image->uploaded);
        $uploadDate = $uploadDate->format('d-M-Y');
        $path = "/thumbnails/{$uploadDate}/{$this->generateStorageFilename($image)}";
        return $path;
    }

    /**
     * Takes a timestamp as an argument or uses current timestamp by default
     * Checks if there is a directory with the name of the value of given timestamp in format d-M-Y
     * If directory is set, returns a path to it and if not creates it
     * Throws FileSystemException if a directory wasn't createad or doesn't exist
     * @return string
     * @throws FileSystemException
     */
    private function generatePathToStorage($uploadDate = null): string
    {
        if ($uploadDate) {
            $uploadDate = new \DateTime ($uploadDate);
            $uploadDate = $uploadDate->format('d-M-Y');
        } else {
            $uploadDate = new \DateTime ('now');
            $uploadDate = $uploadDate->format('d-M-Y');
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
     * @param $uploadDate
     * @return string
     * @throws FileSystemException
     */
    private function generateAbsolutePathToThumbnail($uploadDate): string
    {
        $uploadDate = new \DateTime ($uploadDate);
        $uploadDate = $uploadDate->format('d-M-Y');
        $path = "{$this->rootDir}/public/thumbnails/{$uploadDate}";
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new FileSystemException('Не удалось создать директорию');
            }
        }
        return $path;
    }

    /**
     * Generates a convenient filename to store on server
     * @param \Filehosting\Models\File $model
     * @return string
     */
    private function generateStorageFilename(\Filehosting\Models\File $file): string
    {
        return "{$file->id}_{$file->safe_name}";
    }

}