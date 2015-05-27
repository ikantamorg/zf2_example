<?php

namespace Catalog\Model;

interface CatalogServiceInterface extends AbstractServiceInterface
{
    public function getJsonParams();

    public function getParams();
}
