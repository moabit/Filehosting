<?php

namespace Filehosting\Controllers;

use Slim\Http\{
    Request, Response
};
use Filehosting\Exceptions\FileUploadException;
use Filehosting\Models\File;
use Filehosting\Helpers\Util;

/**
 * Class HomeController
 * @package Filehosting\Controllers
 */
class HomeController extends Controller
{

    /**
     * HomeController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container)
    {
        parent::__construct($container);
    }

    /**
     * @param $request
     * @param $response
     * @param array $args
     * @return mixed
     */
    public function index(Request $request, Response $response, array $args = []): Response
    {
        $csrfNameKey = $this->container['csrf']->getTokenNameKey();
        $csrfValueKey = $this->container['csrf']->getTokenValueKey();
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);
        return $this->container['twig']->render($response, 'upload.twig', ['csrfNameKey' => $csrfNameKey,
            'csrfValueKey' => $csrfValueKey,
            'csrfName' => $csrfName,
            'csrfValue' => $csrfValue]);
    }

    /**
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function uploadFile(Request $request, Response $response, array $args = []): Response
    {
        $file = $request->getUploadedFiles()['userFile'];
        if (!$file) {
            throw new FileUploadException('Файл отсутствует');
        }
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new FileUploadException('Не удалось загрузить файл. Ошибка: ' . $file->getError());
        }
        try {
            $this->container['db']->getConnection()->getPDO()->beginTransaction();
            $fileName = Util::normalizeFilename($file->getClientFilename());
            $uploaderToken = Util::generateToken();
            $model = File::create(['original_name' => $fileName,
                'safe_name' => Util::generateSafeFilename($fileName),
                'size' => $file->getSize(),
                'uploader_token' => $uploaderToken]);
            $this->container['fileSystem']->moveUploadedFileToStorage($file, $model);

        } catch (\Exception $e) {
            $this->container['db']->getConnection()->getPDO()->rollback();
            throw new $e;
        }
        $this->container['db']->getConnection()->getPDO()->commit();
        $response = $response->withRedirect('file/' . $model->id);
        return $response = $this->container['uploaderAuth']->setUploaderToken($model->id, $uploaderToken, $response);

    }
}