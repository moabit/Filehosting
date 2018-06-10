<?php


namespace Filehosting\Middlewares;

use Filehosting\Auth\UploaderAuth;
use Filehosting\Exceptions\AuthException;
use Slim\Http\{
    Request, Response
};

/**
 * Class UploaderAuthMiddleware
 * @package Filehosting\Middlewares
 */
class UploaderAuthMiddleware
{
    /**
     * @var UploaderAuth
     */
    protected $uploaderAuth;

    /**
     * UploaderAuthMiddleware constructor.
     * @param UploaderAuth $uploaderAuth
     */
    public function __construct(UploaderAuth $uploaderAuth)
    {
        $this->uploaderAuth = $uploaderAuth;
    }

    /**
     * Checks if a user is the file uploader
     * If not doesn't allow to delete a file
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws AuthException
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->uploaderAuth->isAuth($request)) {
            throw new AuthException();
        }
        $response = $next($request, $response);
        return $response;
    }
}