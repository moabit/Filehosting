<?php

namespace Filehosting\Validators;


use Filehosting\Exceptions\FileUploadException;

class UploadedFileValidator extends Validator
{
    public $fileSizeLimit;
    public function __construct (int $fileSizeLimit)
    {
        $this->fileSizeLimit=$fileSizeLimit;
    }
    public function validate (\Slim\Http\UploadedFile $file) //void
    {
        if (!$file) {
            throw new FileUploadException('Файл отсутствует');
        }
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new FileUploadException('Не удалось загрузить файл. Ошибка: ' . $file->getError());
        }
        if ($file->getSize()>$this->fileSizeLimit) {
            throw new FileUploadException("Превышение лимита. Размер заруженного файла: {$file->getSize()}. Установленный лимит: {$this->fileSizeLimit}");
        }
    }
}