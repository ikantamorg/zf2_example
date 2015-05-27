<?php

namespace Catalog\Controller\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper as ViewHelper;
use Zend\Console\Console;

class CategoryControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $catalogService = new \Catalog\Model\CatalogService();

        $indexController = new \Catalog\Controller\IndexController($catalogService);

        $helper = new ViewHelper\Url();
        $router = Console::isConsole() ? 'HttpRouter' : 'Router';
        $helper->setRouter($serviceLocator->getServiceLocator()->get($router));

        $match = $serviceLocator->getServiceLocator()->get('application')
                ->getMvcEvent()
                ->getRouteMatch()
        ;

        if ($match instanceof RouteMatch) {
            $helper->setRouteMatch($match);
        }

        $catalogService->setCatalogFilterForm($serviceLocator->getServiceLocator()->get('CatalogFilterForm'));
        $catalogService->setGeoip($serviceLocator->getServiceLocator()->get('geoip'));
        $catalogService->setSearchAdapter($serviceLocator->getServiceLocator()->get('SearchService'));
        $catalogService->setUrlHelper($helper);

        $paginator = new \Catalog\Model\Paginator();
        $paginator->setUrlHelper($helper);

        $catalogService->setPaginator($paginator);

        return $indexController;
    }
}
