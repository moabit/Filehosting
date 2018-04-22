<?php

namespace Filehosting\Controllers;

use Slim\Http\{
    Request, Response
};
use Slim\Views\Twig;

/**
 * Class Controller
 * @package Filehosting\Controllers
 */
abstract class Controller
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * Controller constructor.
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Shows a page handled by controller
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    abstract public function index(Request $request, Response $response, array $args = []): Response;
}