<?php

namespace Catalog\Model;

use Zend\Paginator\Paginator as ZendPaginator;

class Paginator
{
    protected $_dataSource;
    protected $_queryParams;
    protected $_routeParams;
    protected $_urlHelper;
    protected $_pa;

    public function setDataSource($dataSource)
    {
        $this->_dataSource = $dataSource;

        return $this;
    }

    public function setQueryParams($queryParams)
    {
        $this->_queryParams = $queryParams;

        return $this;
    }

    public function setRouteParams($routeParams)
    {
        $this->_routeParams = $routeParams;

        return $this;
    }

    public function setUrlHelper($urlHelper)
    {
        $this->_urlHelper = $urlHelper;

        return $this;
    }

    public function paginate()
    {
        $pa       = $this->_initPaginator($this->_dataSource);
        $products = $pa->getCurrentItems()->getArrayCopy();

        return $products;
    }

    protected function _initPaginator($collection)
    {
        $limit     = (isset($this->_queryParams['view']) && $this->_queryParams['view'] == 'map') ? 1000000 : 9;
        $paginator = new ZendPaginator($collection);
        $paginator->setCurrentPageNumber(isset($this->_routeParams['page']) ? $this->_routeParams['page'] : 1);
        $paginator->setDefaultItemCountPerPage($limit);
        $paginator->setPageRange(10);

        $this->_pa = $paginator;

        return $paginator;
    }

    public function getControl()
    {
        return $this->_preparePaginator($this->_pa, $this->_queryParams, $this->_urlHelper);
    }

    protected function _preparePaginator($pa, $params, $urlHelper)
    {
        $urlParams               = $this->_getUrlParams($params);
        $p                       = (array) $pa->getPages('Sliding');
        $paginator['page_count'] = $p['pageCount'] > 1;

        if (isset($p['previous'])) {
            $paginator['previous']     = $p['previous'];
            $paginator['previous_url'] = $urlHelper('catalog/list', array('page' => $p['previous']), array('query' => $urlParams));
        }

        $paginator['current'] = $p['current'];

        if (isset($p['next'])) {
            $paginator['next']     = $p['next'];
            $paginator['next_url'] = $urlHelper('catalog/list', array('page' => $p['next']), array('query' => $urlParams));
        }

        foreach ($p['pagesInRange'] as $page) {
            if ($page === $p['current']) {
                $paginator['pages'][] = array('page' => $page);
            } else {
                $paginator['pages'][] = array('page' => $page, 'url' => $urlHelper('catalog/list', array('page' => $page), array('query' => $urlParams)));
            }
        }

        return $paginator;
    }

    protected function _getUrlParams($params)
    {
        return array(
            'query'                 => isset($params['query']) ? $params['query'] : null,
            'address'               => isset($params['address']) ? $params['address'] : null,
            'product_type'          => isset($params['product_type']) ? $params['product_type'] : null,
            'start_date'            => isset($params['start_date']) ? $params['start_date'] : null,
            'min_price'             => isset($params['min_price']) ? $params['min_price'] : null,
            'max_price'             => isset($params['max_price']) ? $params['max_price'] : null,
            'min_distance'          => isset($params['min_distance']) ? $params['min_distance'] : null,
            'max_distance'          => isset($params['max_distance']) ? $params['max_distance'] : null,
            'min_expected_audience' => isset($params['min_expected_audience']) ? $params['min_expected_audience'] : null,
            'max_expected_audience' => isset($params['max_expected_audience']) ? $params['max_expected_audience'] : null,
        );
    }
}
