<?php

namespace Filehosting\Controllers;

use \Illuminate\Database\Capsule\Manager as DB;
use Slim\Http\{
    Request, Response
};
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use Filehosting\Models\File;
use Filehosting\Helpers\{
    Util, SphinxSearch, FileSystem
};
use Filehosting\Auth\UploaderAuth;
use Filehosting\Validators\UploadedFileValidator;

/**
 * Handles main page
 *
 * Class HomeController
 * @package Filehosting\Controllers
 */
class HomeController extends Controller
{
    /**
     * @var Guard
     */
    protected $csrf;
    /**
     * @var SphinxSearch
     */
    protected $sphinxSearch;
    /**
     * @var UploaderAuth
     */
    protected $uploaderAuth;
    /**
     * @var FileSystem
     */
    protected $fileSystem;
    /**
     * @var UploadedFileValidator
     */
    protected $uploadedFileValidator;
    /**
     * @var \getID3
     */
    protected $getID3;

    /**
     * HomeController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(Twig $twig, Guard $csrf, SphinxSearch $sphinxSearch, UploaderAuth $uploaderAuth, FileSystem $fileSystem, UploadedFileValidator $uploadedFileValidator, \getID3 $getID3)
    {
        parent::__construct($twig);
        $this->csrf = $csrf;
        $this->sphinxSearch = $sphinxSearch;
        $this->uploaderAuth = $uploaderAuth;
        $this->fileSystem = $fileSystem;
        $this->uploadedFileValidator = $uploadedFileValidator;
        $this->getID3 = $getID3;
    }

    /**
     *
     * Shows main page with file upload form
     *
     * @param $request
     * @param $response
     * @param array $args
     * @return mixed
     */
    public function index(Request $request, Response $response, array $args = []): Response
    {
        return $this->twig->render($response, 'upload.twig', ['csrfNameKey' => $this->csrf->getTokenNameKey(),
            'csrfValueKey' => $this->csrf->getTokenValueKey(),
            'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()),
            'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()),
            'isDeleted' => $request->getParam('deleted') ? true : false
        ]);
    }

    /**
     *
     * Creates and saves file entity into the database
     * Moves uploaded file to the storage
     * Creates rt index
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function uploadFile(Request $request, Response $response, array $args = []): Response
    {
        $file = $request->getUploadedFiles()['userFile'];
        $this->uploadedFileValidator->validate($file); // checks if a file exists and etc
        try {
            DB::beginTransaction();
            $fileName = Util::normalizeFilename($file->getClientFilename());
            $uploaderToken = Util::generateToken();
            $model = File::create(['original_name' => $fileName,
                'safe_name' => Util::generateSafeFilename($fileName),
                'size' => $file->getSize(),
                'uploader_token' => $uploaderToken,
                'media_type' => $file->getClientMediaType()]);
            $this->fileSystem->moveUploadedFileToStorage($file, $model);
            $path = $this->fileSystem->getAbsolutePathToFile($model);
            $model->info = json_encode($this->getID3->analyze($path));
            if ($model->isImage()) {
                $this->fileSystem->generateThumbnail($model, $path);
            }
            $model->save();
        } catch (\Exception $e) {
            DB::rollback();
            throw new $e;
        }
        DB::commit();
        $this->sphinxSearch->indexFile($model->id, $model->original_name);
        $response = $response->withRedirect('file/' . $model->id);
        $response = $this->uploaderAuth->setUploaderToken($uploaderToken, $response, $model->id);
        return $response;
    }
}