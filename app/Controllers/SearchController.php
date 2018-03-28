<?php

namespace Filehosting\Controllers;

use Slim\Http\{
    Request, Response
};

use Filehosting\Models\File;
use Illuminate\Support\Facades\DB;


class SearchController extends Controller
{
    public function __construct(\Slim\Container $container)
    {
        parent::__construct($container);
    }

    public function index(Request $request, Response $response, array $args = []):Response
    {
        $currentPage = isset($userData['page']) ? $userData['page'] : 1;
        $files=File::offset(10)->limit(5)->orderBy('download_counter');

       $test= $this->container['search']->select('*')->from ('index_files');

        return $response->write(var_dump ($test));


        $csrfNameKey = $this->container['csrf']->getTokenNameKey();
        $csrfValueKey = $this->container['csrf']->getTokenValueKey();
        $csrfName = $request->getAttribute($csrfNameKey);
        $csrfValue = $request->getAttribute($csrfValueKey);
      //  return $this->container['twig']->render($response, 'search.twig', ['csrfNameKey' => $csrfNameKey,
     //       'csrfValueKey' => $csrfValueKey,
     //       'csrfName' => $csrfName,
     //       'csrfValue' => $csrfValue,
     //       'files'=>$files]);

    }

    public function search (Request $request, Response $response, array $args = []): Response
    {

           var_dump($test);
    }
}