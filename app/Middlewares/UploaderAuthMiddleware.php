<?php


namespace Filehosting\Middlewares;
use Filehosting\Auth\UploaderAuth;
use Filehosting\Exceptions\AuthException;
use Slim\Http\{Request, Response};

class UploaderAuthMiddleware
{
    protected $uploaderAuth;

    public function __construct(UploaderAuth $uploaderAuth)
    {
        $this->uploaderAuth=$uploaderAuth;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->uploaderAuth->isAuth($request)) {
            throw new AuthException();
        }
        // убрать write
        $response = $next($request, $response);
        return $response;
    }
}