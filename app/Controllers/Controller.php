<?php

namespace Filehosting\Controllers;
use Slim\Http\{
    Request, Response
};

abstract class Controller
{
    protected $container;

    public function __construct (\Slim\Container $container)
    {
        $this->container=$container;
    }
    abstract public function index (Request $request, Response $response, array $args = []):Response;
}