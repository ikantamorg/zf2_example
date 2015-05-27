<?php

namespace Catalog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Catalog\Model\CatalogServiceInterface;

class IndexController extends AbstractActionController
{
    /**
     * Catalog service instance.
     *
     * @var CatalogServiceInterface
     */
    protected $_catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->_catalogService = $catalogService;
    }

    public function indexAction()
    {
        $this->_catalogService->setQueryParams($this->params()->fromQuery());
        $this->_catalogService->setRouteParams($this->params()->fromRoute());

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel($this->_catalogService->getJsonParams());
        }

        return new ViewModel($this->_catalogService->getParams());
    }
}
