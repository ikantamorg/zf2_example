<?php

namespace Catalog\Model;

use Zend\Paginator\Paginator;

class CatalogService extends AbstractService implements CatalogServiceInterface
{
    protected $_catalogFilterForm;
    protected $_geoip;

    /**
     * @var \Search\Model\ProductSearch
     */
    protected $_searchAdapter;
    protected $_urlHelper;

    /**
     * Catalog paginator instance.
     *
     * @var \Catalog\Model\Paginator
     */
    protected $_paginator;

    public function getJsonParams()
    {
        $defaultFilters = \Search\Model\ProductSearch::getDefaultFilters();

        $this->_searchAdapter
                ->setPosition($this->_getPosition())
                ->init()
                ->prepareFilters($defaultFilters)
                ->prepareFilters($this->getQueryParams());

        $this->_paginator
                ->setDataSource($this->_searchAdapter)
                ->setQueryParams($this->getQueryParams())
                ->setRouteParams($this->getRouteParams());

        $result = $this->_paginator->paginate();
        $nores  = false;

        if ($this->getQueryParams('view') != 'map') {
            //filters priority
            $filters = array(
                'query'                 => 0,
                'product_type'          => 1,
                'start_date'            => 2,
                'min_price'             => 3,
                'max_price'             => 4,
                'location'              => 5,
                'min_distance'          => 6,
                'max_distance'          => 7,
                'min_expected_audience' => 8,
                'max_expected_audience' => 9,
            );

            $activeFilters = array();
            $data          = $this->getQueryParams();

            //get applied filters
            foreach ($data as $filter => $value) {
                if (!empty($value) && isset($filters[$filter])) {
                    $activeFilters[$filters[$filter]] = $value;
                }
            }

            //if no results found remove filters one-by-one
            while (count($result) < 1 && count($activeFilters) > 0) {
                $nores = true;

                array_pop($activeFilters);

                $nf = array();
                foreach ($activeFilters as $key => $value) {
                    $nf[array_search($key, $filters)] = $value;
                }

                $this->_searchAdapter
                        ->clear()
                        ->setPosition($this->_getPosition())
                        ->init()
                        ->prepareFilters($defaultFilters)
                        ->prepareFilters($nf);

                $this->_paginator
                        ->setDataSource($this->_searchAdapter)
                        ->setQueryParams($this->getQueryParams())
                        ->setRouteParams($this->getRouteParams());

                $result = $this->_paginator->paginate();
            }

            if ($nores) {
                $result = array_slice($result, 0, 3);
            }
        }

        return array(
            'products'         => $result,
            'paginator'        => $this->_paginator->getControl(),
            'show_suggestions' => $nores,
        );
    }

    public function getParams()
    {
        $form          = $this->getCatalogFilterForm();
        $searchAdapter = $this->getSearchAdapter()->setPosition($this->_getPosition())->init();
        $view          = ($this->getQueryParams('view') == 'map') ? 'map' : 'grid';

        $pa     = $this->_initPaginator($searchAdapter);
        $pa->getCurrentItems()->getArrayCopy(); //init facets
        $facets = $searchAdapter->getFacets();

        $minPrice = isset($facets['price']['min']) ? $facets['price']['min'] : 0;
        $maxPrice = isset($facets['price']['max']) ? $facets['price']['max'] : 0;

        $minAudience = isset($facets['audience']['min']) ? (int) (floor($facets['audience']['min'] / 1000) * 1000) : 0;
        $maxAudience = isset($facets['audience']['max']) ? (int) (ceil($facets['audience']['max'] / 1000) * 1000) : 0;

        $form->setAttribute('action', $this->_urlHelper->__invoke('catalog/list', array(), array('query' => array('view' => $view))));
        $form->setAttribute('method', 'get');

        $form->get('min_price')->setAttribute('value', (int) (floor($minPrice / 10) * 10));
        $form->get('max_price')->setAttribute('value', (int) (ceil($maxPrice / 10) * 10));

        $form->get('min_expected_audience')->setAttribute('value', $minAudience);
        $form->get('max_expected_audience')->setAttribute('value', $maxAudience);

        $form->populateValues($this->getQueryParams());

        $selectedProductId = $this->getQueryParams('id');
        $product           = new \Product\Model\Product\Product();
        $product->load($selectedProductId);

        if ($product->getId()) {
            $selectedProductId = $product->getId();
        } else {
            $selectedProductId = null;
        }

        return array(
            'form'              => $form,
            'user_position'     => $this->_getPosition(),
            'view'              => $view,
            'prices'            => array($minPrice, $maxPrice),
            'audience'          => array($minAudience, $maxAudience),
            'selectedProductId' => $selectedProductId,
        );
    }

    public function getPaginator()
    {
        return $this->_paginator;
    }

    public function setPaginator($paginator)
    {
        $this->_paginator = $paginator;

        return $this;
    }

    public function getUrlHelper()
    {
        return $this->_urlHelper;
    }

    public function setUrlHelper($urlHelper)
    {
        $this->_urlHelper = $urlHelper;

        return $this;
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getCatalogFilterForm()
    {
        return $this->_catalogFilterForm;
    }

    public function setCatalogFilterForm($catalogFilterForm)
    {
        $this->_catalogFilterForm = $catalogFilterForm;

        return $this;
    }

    public function getGeoip()
    {
        return $this->_geoip;
    }

    public function setGeoip($geoip)
    {
        $this->_geoip = $geoip;

        return $this;
    }

    public function getSearchAdapter()
    {
        return $this->_searchAdapter;
    }

    public function setSearchAdapter($searchAdapter)
    {
        $this->_searchAdapter = $searchAdapter;

        return $this;
    }

    protected function _getPosition($type = null)
    {
        $lat = isset($this->_queryParams['latitude']) ? $this->_queryParams['latitude'] : false;
        $lon = isset($this->_queryParams['longitude']) ? $this->_queryParams['longitude'] : false;

        if ($lat && $lon) {
            $position = array('lat' => $lat, 'lon' => $lon);
        } else {
            $position = $this->getGeoip()->getData();
        }

        if ($type === null) {
            return $position;
        } else {
            return $position[$type];
        }
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

    protected function _initPaginator($collection)
    {
        $limit     = ($this->getQueryParams('view') == 'map') ? 1000000 : 9;
        $paginator = new Paginator($collection);
        $paginator->setCurrentPageNumber($this->getRouteParams('page') ? $this->getRouteParams('page') : 1);
        $paginator->setDefaultItemCountPerPage($limit);
        $paginator->setPageRange(10);

        return $paginator;
    }
}
