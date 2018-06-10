<?php

namespace Filehosting\Validators;


use Filehosting\Exceptions\FileUploadException;

/**
 * Class UploadedFileValidator
 * @package Filehosting\Validators
 */
class UploadedFileValidator
{
    /**
     * @var int
     */
    public $fileSizeLimit;

    /**
     * UploadedFileValidator constructor.
     * @param int $fileSizeLimit
     */
    public function __construct(int $fileSizeLimit)
    {
        $this->fileSizeLimit = $fileSizeLimit;
    }

    /**
     * @param \Slim\Http\UploadedFile $file
     * @throws FileUploadException
     */
    public function validate(\Slim\Http\UploadedFile $file) //void
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new FileUploadException('Не удалось загрузить файл. Ошибка: ' . $file->getError());
        }
        if ($file->getSize() > $this->fileSizeLimit) {
            throw new FileUploadException("Превышение лимита. Размер заруженного файла: {$file->getSize()}. Установленный лимит: {$this->fileSizeLimit}");
        }
    }
}