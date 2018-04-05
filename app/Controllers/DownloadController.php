<?php

namespace Filehosting\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Filehosting\Models\{
    File, Comment
};

/**
 * Class DownloadController
 * @package Filehosting\Controllers
 */
class DownloadController extends Controller
{
    /**
     * DownloadController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container)
    {
        parent::__construct($container);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function index(Request $request, Response $response, array $args = []): Response
    {
        $csrfNameKey = $this->container['csrf']->getTokenNameKey();
        $csrfValueKey = $this->container['csrf']->getTokenValueKey();
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);
        $fileId = intval($args['id']);
        $file = File::find($fileId);
        if (!$file) {
            return $response->withStatus(404);
        }
        $comments = $file->getSortedComments();
        $this->container['uploaderAuth']->checkUploaderToken($file, $request);
        //var_dump($request->getAttribute('routeInfo')[2])['id'];
        $link = $request->getUri();
        //  var_dump($this->container['uploaderAuth']->isAuth($request));

        return $this->container['twig']->render($response, 'download.twig', ['csrfNameKey' => $csrfNameKey,
            'csrfValueKey' => $csrfValueKey,
            'csrfName' => $csrfName,
            'csrfValue' => $csrfValue,
            'file' => $file,
            'link' => $link,
            'comments' => $comments
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function forceFileDownload(Request $request, Response $response, array $args = []): Response
    {
        $file = File::find(intval($args['id']));
        $filePath = $this->container['fileSystem']->getAbsolutePathToFile($file);
        try {
            $this->container['db']->getConnection()->getPDO()->beginTransaction();
            $file->download_counter++;
            if (!(in_array('mod_xsendfile', apache_get_modules()))) {
                $response = $response->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Disposition', 'attachment;filename="' . $file->original_name . '"')
                    ->write($filePath);
            } else {
                $response = $response->withHeader('X-SendFile', $filePath)
                    ->withHeader('Content-Type', 'application/octet-stream')
                    ->withHeader('Content-Disposition', 'attachment;filename="' . $file->original_name . '"');
            }
        } catch (\Exception $e) {
            $this->container['db']->getConnection()->getPDO()->rollback();
            throw new $e;
        }
        $file->save();
        $this->container['db']->getConnection()->getPDO()->commit();
        return $response;
    }

    public function deleteFile(Request $request, Response $response, array $args = []): Response
    {
        $file = File::find(intval($args['id']));
        $filePath = $this->container['fileSystem']->getAbsolutePathToFile($file);
        try {
            $this->container['db']->getConnection()->getPDO()->beginTransaction();
            unlink($filePath);
            File::destroy($file->id);
            //еще куку убрать
        } catch (\Exception $e) {
            $this->container['db']->getConnection()->getPDO()->rollback();
            throw new $e;
        }
        $this->container['db']->getConnection()->getPDO()->commit();
        return $response->write('удолил');
    }

    public function addComment(Request $request, Response $response, array $args = [])
    {

        $file = File::find(intval($args['id']));
        // Parses POST-vars
        $commentAuthor = $request->getParam('author');
        $commentText = $request->getParam('comment');
        $parentId = intval($request->getParam('parentId'));
        $comment = new Comment ();
        $comment->fill(['file_id' => $file->id, 'text' => $commentText, 'author' => $commentAuthor, 'parent_id' => $parentId]);
        if ($comment->parent_id == null) {
            $comment->makeRoot();
        } else {
            $comment->makeChild();
        }
        $comment->save();
        if ($request->isXhr()) {
            return $response->withJson($comment);
        }
        return $response->withRedirect($request->getUri());
    }
}