<?php


namespace Filehosting\Middlewares;


class UploaderAuthMiddleware extends Middleware
{
    public function __construct(\Slim\Container $container)
    {
        parent::__construct($container);
    }
    public function __invoke($request, $response, $next)
    {
        if (!$this->container['uploaderAuth']->isAuth($request)) {
            return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write('Page not found, FAIL');
        }
        $response = $next($request, $response);
        return $response;
    }
}