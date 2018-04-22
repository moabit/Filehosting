<?php


namespace Filehosting\Auth;

use Dflydev\FigCookies\{SetCookie, FigResponseCookies};
use Filehosting\Exceptions\AuthException;
use Filehosting\Models\File;
use Slim\Http\{Request, Response};

class UploaderAuth
{
    public function isAuth(Request $request): bool
    {
        $id=intval($request->getAttribute('routeInfo')[2]['id']);
        $file=File::find($id);
        if (in_array($file->uploader_token, $request->getCookieParams() )){
            return true;
        } else {
            return false;
        }
    }

    public function setUploaderToken(string $token, Response $response, int $fileId): \Psr\Http\Message\ResponseInterface
    {
        return FigResponseCookies::set($response, SetCookie::create($fileId)->withValue($token)->withExpires(strtotime('60 days')));
    }

    public function deleteUploaderToken (int $fileId, Response $response):\Psr\Http\Message\ResponseInterface
    {
        return FigResponseCookies::remove($response, $fileId);
    }
}