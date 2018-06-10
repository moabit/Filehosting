<?php


namespace Filehosting\Auth;

use Dflydev\FigCookies\{
    SetCookie, FigResponseCookies
};
use Filehosting\Exceptions\AuthException;
use Filehosting\Models\File;
use Slim\Http\{
    Request, Response
};

/**
 * Class UploaderAuth
 * @package Filehosting\Auth
 */
class UploaderAuth
{
    /**
     * @param Request $request
     * @return bool
     */
    public function isAuth(Request $request): bool
    {
        $id = intval($request->getAttribute('routeInfo')[2]['id']);
        $file = File::find($id);
        if (in_array($file->uploader_token, $request->getCookieParams())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $token
     * @param Response $response
     * @param int $fileId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setUploaderToken(string $token, Response $response, int $fileId): \Psr\Http\Message\ResponseInterface
    {
        return FigResponseCookies::set($response, SetCookie::create($fileId)->withValue($token)->withExpires(strtotime('60 days')));
    }

    /**
     * @param int $fileId
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteUploaderToken(int $fileId, Response $response): \Psr\Http\Message\ResponseInterface
    {
        return FigResponseCookies::remove($response, $fileId);
    }
}