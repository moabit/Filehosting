<?php


namespace Filehosting\Helpers;


/**
 * Class SearchViewParams
 * @package Filehosting\Helpers
 */
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
     * @var int
     */
    protected $currentPage;
    /**
     * Pager constructor.
     * @param array $params
     * @param $studentQuantity
     * @param int $limit
     */
    protected $files;

    /**
     * @var null
     */
    protected $filesFound;

    /**
     * SearchViewParams constructor.
     *
     * @param array $params
     * @param $files
     * @param $filesFound
     */
    public function __construct(array $params, $files, $filesFound)
    {
        $this->params = $params;
        $this->files = $files;
        if ($filesFound) {
            $this->filesFound = intval($filesFound);
            $this->totalPages = ceil($this->filesFound / 1);
        } else {
            $this->filesFound = null;
        }
        $this->currentPage = isset($params['page']) ? $params['page'] : 1;
    }

    /**
     * Generates pagination link
     *
     * @param $page
     * @return string
     */
    public function getPageLink($page): string
    {
        return '?' . $this->generateURL(['page' => $page]);
    }

    /**
     * Generates link
     *
     * @param $params
     * @return string
     */
    private function generateURL($params): string
    {
        return http_build_query(array_replace($this->params, $params));
    }

    /**
     * Returns total number of pages
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Returns number of current page
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Returns search params
     *
     * @return mixed
     */
    public function getSearch()
    {
        return $this->params['search'];
    }

    /**
     * Returns true if files were found
     *
     * @return bool
     */
    public function isSearchSuccessful(): bool
    {
        if ($this->files) {
            return true;
        }
        return false;
    }

    /**
     * Returns an array of search params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Returns collection of found files
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFiles(): \Illuminate\Support\Collection
    {
        return $this->files;
    }

    /**
     * Returns quantity of found files
     *
     * @return int
     */
    public function getFilesFound(): int
    {
        return $this->filesFound;
    }
}