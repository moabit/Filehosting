<?php


namespace Filehosting\Helpers;


class SearchViewParams
{
    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var int
     */
    protected $totalPages;
    /**
     * @var int|mixed
     */
    protected $currentPage;
    /**
     * Pager constructor.
     * @param array $params
     * @param $studentQuantity
     * @param int $limit
     */
    protected $files;

    protected $filesFound;

    public function __construct(array $params, $files, $filesFound)
    {
        $this->params = $params;
        $this->files=$files;
        if ($filesFound) {
            $this->filesFound=intval($filesFound);
            $this->totalPages = ceil($this->filesFound / 1);
        } else {
            $this->filesFound=null;
        }
       $this->currentPage = isset($params['page']) ? $params['page'] : 1;
    }

    /**
     * @param $page
     * @return string
     */
    public function getPageLink($page): string
    {
        return '?' . $this->generateURL(['page' => $page]);
    }
    /**
     * @param $params
     * @return string
     */
    private function generateURL($params): string
    {
        return http_build_query(array_replace($this->params, $params));
    }
    /**
     * @return float
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }
    /**
     * @return int|mixed
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }
    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->params['search'];
    }

    public function isSearchSuccessful ():bool
    {
        if ($this->files) {
            return true;
        }
        return false;
    }

    public function getParams () :array
    {
        return $this->params;
    }

    public function getFiles () :\Illuminate\Support\Collection
    {
        return $this->files;
    }

    public function getFilesFound () :int
    {
        return $this->filesFound;
    }


}