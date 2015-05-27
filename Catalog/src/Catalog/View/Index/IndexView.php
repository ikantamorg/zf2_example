<?php

namespace Catalog\View\Index;

use Zend\View\Model\ViewModel;

class IndexView extends ViewModel
{
    protected $_catalogFilterForm;
    protected $_userPosition;
    protected $_view;
    protected $_prices = array();

    public function __construct($catalogFilterForm, $userPosition, $view, $prices)
    {
        $this->_catalogFilterForm = $catalogFilterForm;
        $this->_userPosition      = $userPosition;
        $this->_view              = $view;
        $this->_prices            = $prices;

        $variables = array(
            'form'          => $this->_catalogFilterForm,
            'user_position' => $this->_userPosition,
            'view'          => $this->_view,
            'prices'        => $this->_prices,
        );

        parent::__construct($variables);
    }
}
