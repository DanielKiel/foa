<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 08:08
 */

namespace Dion\Foa\Repositories;


class SearchEngine
{
    private $search;

    private $pagination = [
        'per_page' => 25,
        'page_name' => 'page',
        'page' => null
    ];

    private $operators = [
        '=', '!=', 'LIKE', 'NOT LIKE', '>', '>=', '<', '<='
    ];

    public function performSearch()
    {
        if ($this->pagination !== false && is_array($this->pagination)) {
            return $this->returnPagination();
        }

        return $this->returnCollection();
    }

    public function setBaseQuery($baseQuery)
    {
        $this->search = $baseQuery;

        return $this;
    }

    public function setQuery($query = '', $field = 'data')
    {
        $this->search = $this->search->where($field, 'LIKE', '%' . $query . '%');

        return $this;
    }

    public function setFilters(array $filters = [])
    {
        foreach ($filters as $filter) {
            $this->transformFilter($filter);
        }


        return $this;
    }

    public function setPagination($pagination = ['per_page' => 25, 'page_name' => 'page', 'page' => null])
    {
        $this->pagination = $pagination;

        return $this;
    }

    private function returnPagination()
    {
        return $this->search->paginate(
            (int) array_get($this->pagination, 'per_page',25),
            $columns = ['*'],
            array_get($this->pagination, 'page_name','page'),
            array_get($this->pagination, 'page',null)
        );
    }

    private function returnCollection()
    {
        return $this->search->get();
    }

    private function transformFilter(string $filter)
    {
        //preg_match_all('/\({1}(.*){1}\)$/U',$filter, $matches);
        preg_match('~'. implode('|', $this->operators) . '~', $filter, $matches);

        $match = array_first($matches, function() {return true;}, false);

        if ($match !== false) {
            $params = explode($match, $filter);

            $leftOperand = trim(array_shift($params));

            $rightOperand = null;

            if (!empty($params)) {
                $rightOperand = trim(array_shift($params));
            }

            if ($match === 'LIKE' || $match === 'NOT LIKE') {
                $rightOperand = '%' . $rightOperand . '%';
            }

            $this->search = $this->search->where('data->' . $leftOperand, $match, $rightOperand);
        }
    }
}