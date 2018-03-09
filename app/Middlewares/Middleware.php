<?php


namespace Filehosting\Middlewares;


abstract class Middleware
{
    protected $container;

    public function __construct (\Slim\Container $container)
    {
        $this->container=$container;
    }
}