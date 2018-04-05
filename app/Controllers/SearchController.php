<?php

namespace Filehosting\Controllers;

use Slim\Http\{
    Request, Response
};

use Filehosting\Models\File;
use Filehosting\Helpers\SearchViewParams;


class SearchController extends Controller
{
    /**
     * SearchController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container)
    {
        parent::__construct($container);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function index(Request $request, Response $response, array $args = []):Response
    {
        $params = $request->getQueryParams();
        if ($params['match'] == null) {
            return $response->withRedirect('/');
        }

        $currentPage = isset($params['page']) ? intval($params['page']) : 1;
        $limit = 15; // search results on a single page
        $offset = ($currentPage - 1) * $limit;
        $search=$this->container['sphinxSearch']->search($params['match'], $offset, $limit);
        $files=$this->getSearchedFiles($search);

        $viewParams= new SearchViewParams($params, $files);

        return $this->container['twig']->render($response, 'search.twig',['viewParams'=> $viewParams]);
    }

    /**
     * @param array $searchResults
     * @return bool
     */
    private function getSearchedFiles(array $searchResults)
    {
        if (empty($searchResults)) {
            return false;
        }
        return File::whereIn('id', $searchResults)->get();
    }




}