<?php


namespace Filehosting\Middlewares;
use Filehosting\Auth\UploaderAuth;
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
            return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write('Page not found, FAIL');
        }
        // убрать write
        $response = $next($request, $response);
        return $response;
    }
}