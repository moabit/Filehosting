<?php

namespace Filehosting\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Slim\Csrf\Guard;
use Illuminate\Database\Capsule\Manager as DB;
use Filehosting\Auth\UploaderAuth;
use Filehosting\Validators\CommentValidator;
use Filehosting\Helpers\{FileSystem, SphinxSearch};
use Filehosting\Models\{
    File, Comment
};
use Filehosting\Exceptions\CommentAdditionException;

/**
 * Handles File Download Page
 * Class DownloadController
 * @package Filehosting\Controllers
 */
class DownloadController extends Controller
{
    /**
     * @var Guard
     */
    protected $csrf;
    /**
     * @var UploaderAuth
     */
    protected $uploaderAuth;
    /**
     * @var FileSystem
     */
    protected $fileSystem;
    /**
     * @var CommentValidator
     */
    protected $commentValidator;

    protected $sphinxSearch;

    /**
     * DownloadController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(Twig $twig, Guard $csrf, UploaderAuth $uploaderAuth, FileSystem $fileSystem, CommentValidator $commentValidator, SphinxSearch $sphinxSearch)
    {
        parent::__construct($twig);
        $this->csrf = $csrf;
        $this->uploaderAuth = $uploaderAuth;
        $this->fileSystem = $fileSystem;
        $this->commentValidator = $commentValidator;
        $this->sphinxSearch=$sphinxSearch;
    }

    /**
     *
     * Shows file's download page
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function index(Request $request, Response $response, array $args = []): Response
    {
        $csrfNameKey = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);
        $fileId = intval($args['id']);
        $file = File::find($fileId);
        if (!$file) {
            return $response->withStatus(404);
        }
        $comments = $file->getSortedComments();
        $commentsAllowed = true;
        if ($file->countRootComments() > 998) {
            $commentsAllowed = false;
        }
        if ($file->isImage()) {
            $pathToThumbnail=$this->fileSystem->getPathToThumbnail($file);
        }
        $link = $request->getUri();
        return $this->twig->render($response, 'download.twig', ['csrfNameKey' => $csrfNameKey,
            'csrfValueKey' => $csrfValueKey,
            'csrfName' => $csrfName,
            'csrfValue' => $csrfValue,
            'file' => $file,
            'link' => $link,
            'comments' => $comments,
            'commentsAllowed' => $commentsAllowed,
            'isAuth' => $this->uploaderAuth->isAuth($request),
            'pathToThumbnail'=>$pathToThumbnail
        ]);
    }

    /**
     *
     * Forces file download
     * Uses xsendfile module if it is installed on a server, if not uses php
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function forceFileDownload(Request $request, Response $response, array $args = []): Response
    {
        $file = File::find(intval($args['id']));
        $filePath = $this->fileSystem->getAbsolutePathToFile($file);
        $file->download_counter++;
        $file->save();
        if (!(in_array('mod_xsendfile', apache_get_modules()))) {
            $response = $response->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Disposition', 'attachment;filename="' . $file->original_name . '"')
                ->write($filePath);
        } else {
            $response = $response->withHeader('X-SendFile', $filePath)
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Disposition', 'attachment;filename="' . $file->original_name . '"');
        }
        return $response;
    }

    /**
     *
     * Deletes file from storage and it's entity from database
     * Also deletes uploader's cookie
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Filehosting\Exceptions\FileSystemException
     */
    public function deleteFile(Request $request, Response $response, array $args = []): Response
    {
        $file = File::find(intval($args['id']));
        $filePath = $this->fileSystem->getAbsolutePathToFile($file);
        try {
            DB::beginTransaction();
            unlink($filePath);
            File::destroy($file->id);
            $response = $this->uploaderAuth->deleteUploaderToken($file->id, $response);
        } catch (\Exception $e) {
            DB::rollback();
            throw new $e;
        }
        DB::commit();
        $this->sphinxSearch->deleteIndexedFile($file->id);
        return $response->write('удолил');
    }

    /**
     *
     * Creates and adds a comment from parsed post-vars
     *  If there are more than 998 comments throws CommentAdditionException
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws CommentAdditionException
     */
    public function addComment(Request $request, Response $response, array $args = []): Response
    {
        $file = File::find(intval($args['id']));
        if ($file->countRootComments() > 998) {
            // throws an Exception because matpath format which is used in app doesn't expect more than 999 root comments
            throw new CommentAdditionException('Комментарии закрыты');
        }
        // Parses POST-vars
        $commentAuthor = $request->getParam('author');
        $commentText = $request->getParam('comment');
        $parentId =intval( $request->getParam('parentId'));
        $comment = new Comment (['file_id' => $file->id, 'comment_text' => $commentText, 'author' => $commentAuthor]);
        if ($parentId) {
            $comment->parent_id=$parentId;
        }
        $comment->generateMatpath();
        $errors = $this->commentValidator->validate($comment);
        if (empty($errors)) {
            $comment->save();
            $date=new \DateTime('now');
            $comment->posted=$date->format('m/d/Y');
            if ($request->isXhr()) {
                return $response->withJson($comment);
            }
            return $response->withRedirect($request->getUri());
        } else {
            if ($request->isXhr()) {
                return $response->withJson($errors);
            }
            return $response->withRedirect($request->getUri());
        }
    }
}