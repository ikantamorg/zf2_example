<?php

namespace Catalog\Model;

abstract class AbstractService implements AbstractServiceInterface
{
    protected $_params;

    protected function _setParams($type, $params)
    {
        $this->_params[$type] = $params;

        return $this;
    }

    protected function _getParams($type, $key)
    {
        if (!isset($this->_params[$type])) {
            return array();
        }

        if ($key === null) {
            return $this->_params[$type];
        }

        return isset($this->_params[$type][$key]) ? $this->_params[$type][$key] : null;
    }

    public function setQueryParams($queryParams)
    {
        $this->_setParams('query', $queryParams);

        return $this;
    }

    public function setPostParams($postParams)
    {
        $this->_setParams('post', $postParams);

        return $this;
    }

    public function setRouteParams($routeParams)
    {
        $this->_setParams('route', $routeParams);

        return $this;
    }

    public function getQueryParams($key = null)
    {
        return $this->_getParams('query', $key);
    }

    public function getPostParams($key = null)
    {
        return $this->_getParams('post', $key);
    }

    public function getRouteParams($key = null)
    {
        return $this->_getParams('route', $key);
    }
}
