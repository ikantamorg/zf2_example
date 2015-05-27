<?php

namespace Catalog\Model;

interface AbstractServiceInterface
{
    public function setQueryParams($queryParams);

    public function setPostParams($postParams);

    public function setRouteParams($routeParams);

    public function getQueryParams($key = null);

    public function getPostParams($key = null);

    public function getRouteParams($key = null);
}
