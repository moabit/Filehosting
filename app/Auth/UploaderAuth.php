<?php


namespace Filehosting\Auth;

use Dflydev\FigCookies\{
    SetCookie,
    FigResponseCookies
};
use Filehosting\Models\File;

class UploaderAuth
{
    protected $container;

    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }

    public function isAuth($request): bool
    {
        $id=$request->getAttribute('routeInfo')[2]['id'];
        if (in_array($id, $_SESSION['uploader'])){
            return true;
        } else {
            return false;
        }
    }

    public function checkUploaderToken(File $file, \Slim\Http\Request $request)
    {
        if ($file->uploader_token !== $request->getCookieParam($file->id)) {
            return false;
        } else {
            $_SESSION['uploader']=[$file->id];
        }
    }

    public function setUploaderToken(int $fileId, string $token, \Slim\Http\Response $response): \Psr\Http\Message\ResponseInterface
    {
        return FigResponseCookies::set($response, SetCookie::create($fileId)->withValue($token)->withExpires(strtotime('60 days')));
    }
}