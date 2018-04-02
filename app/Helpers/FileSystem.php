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
    protected $baseDir;

    /**
     * FileSystem constructor.
     * @param string $initDir
     */
    public function __construct(string $initDir)
    {

        $this->baseDir = str_replace(DIRECTORY_SEPARATOR . "app", "", $initDir);
    }

    /**
     * @return string
     */
    public function getRootDir(): string
    {
        return $this->baseDir;
    }

    /**
     * @param \Filehosting\Models\File $file
     * @return string
     */
    public function getAbsolutePathToFile(\Filehosting\Models\File $file): string
    {
        $uploadDate = new \DateTime ($file->upload_date);
        $uploadDate = $uploadDate->format('d-M-Y');
        return "{$this->getRootDir()}/storage/{$uploadDate}/{$file->id}_{$file->safe_name}";
    }

    /**
     * @param \Slim\Http\UploadedFile $file
     * @param \Filehosting\Models\File $model
     * @throws FileSystemException
     */
    public function moveUploadedFileToStorage(\Slim\Http\UploadedFile $file, \Filehosting\Models\File $model) //void
    {
        $storageName=$this->generateStorageFilename($model);
        $file->moveTo("{$this->generatePathToStorage()}/{$storageName}");
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function generatePathToStorage(): string
    {
        $timestamp = new \DateTime ('now');
        $date = $timestamp->format('d-M-Y');
        $path="{$this->getRootDir()}/storage/{$date}";
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
    private function generateStorageFilename(\Filehosting\Models\File $model): string
    {
        return "{$model->id}_{$model->safe_name}";
    }

}