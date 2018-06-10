<?php

namespace Filehosting\Controllers;

use Slim\Http\{
    Request, Response
};
use Slim\Views\Twig;
use Filehosting\Models\File;
use Filehosting\Helpers\{
    SearchViewParams, SphinxSearch
};


/**
 * Class SearchController
 * @package Filehosting\Controllers
 */
class SearchController extends Controller
{
    /**
     * @var SphinxSearch
     */
    protected $sphinxSearch;

    /**
     * SearchController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(Twig $twig, SphinxSearch $sphinxSearch)
    {
        parent::__construct($twig);
        $this->sphinxSearch = $sphinxSearch;
    }

    /**
     * Shows search page; if search string is empty redirects to the main page
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function index(Request $request, Response $response, array $args = []): Response
    {
        $params = $request->getQueryParams();
        if ($params['match'] == null) {
            return $response->withRedirect('/');
        }
        $currentPage = isset($params['page']) ? intval($params['page']) : 1;
        $limit = 10; // search results on a single page
        $offset = ($currentPage - 1) * $limit;
        $search = $this->sphinxSearch->search(strval($params['match']), $offset, $limit);
        $files = $this->getSearchedFiles($search);
        $filesFound = $this->sphinxSearch->countSearchResults($params['match']);
        $viewParams = new SearchViewParams($params, $files, $filesFound);
        return $this->twig->render($response, 'search.twig', ['viewParams' => $viewParams]);
    }

    /**
     * Returns search results
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