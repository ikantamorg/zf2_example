<?php

namespace Catalog\Form;

use Zend\Form\Form;

class CatalogFilter extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('id', 'catalog-filter-form');
        $this->setAttribute('class', 'std-form toggle-block');

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'query',
            'options'    => array(
                'label' => 'Enter your search',
            ),
            'attributes' => array(
                'id'           => 'query',
                'class'        => 'form-control',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Select',
            'name'       => 'product_type',
            'options'    => array(
                'label'         => 'Select category',
                'value_options' => $this->_getProductTypes(),
                'empty_option'  => '-- Ad spot categories --',
            ),
            'attributes' => array(
                'id'          => 'product_type',
                'data-custom' => 'select',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'start_date',
            'options'    => array(
                'label'            => 'Start Date<i class="fa fa-angle-down pull-right"></i>',
                'label_options'    => array(
                    'disable_html_escape' => true,
                ),
                'label_attributes' => array(
                    'class' => 'accordion-title',
                ),
            ),
            'attributes' => array(
                'id'           => 'start_date',
                'class'        => 'form-control',
                'placeholder'  => 'Select date',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'min_price',
            'options'    => array(
                'label'            => 'Cost range<i class="fa fa-angle-down pull-right"></i>',
                'label_options'    => array(
                    'disable_html_escape' => true,
                ),
                'label_attributes' => array(
                    'class' => 'accordion-title',
                ),
            ),
            'attributes' => array(
                'id'           => 'min_price',
                'class'        => 'form-control',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'max_price',
            'attributes' => array(
                'id'           => 'max_price',
                'class'        => 'form-control',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'location',
            'options'    => array(
                'label'            => 'Location<i class="fa fa-angle-down pull-right"></i>',
                'label_options'    => array(
                    'disable_html_escape' => true,
                ),
                'label_attributes' => array(
                    'class' => 'accordion-title',
                ),
            ),
            'attributes' => array(
                'id'           => 'location',
                'class'        => 'form-control',
                'placeholder'  => 'Enter location',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'min_distance',
            'options'    => array(
                'label'            => 'Distance from you <span class="text-muted">(mi)</span><i class="fa fa-angle-down pull-right"></i>',
                'label_options'    => array(
                    'disable_html_escape' => true,
                ),
                'label_attributes' => array(
                    'class' => 'accordion-title',
                ),
            ),
            'attributes' => array(
                'id'           => 'min_distance',
                'class'        => 'form-control',
                'value'        => 0,
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'max_distance',
            'attributes' => array(
                'id'           => 'max_distance',
                'class'        => 'form-control',
                'value'        => 20000,
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'min_expected_audience',
            'options'    => array(
                'label'            => 'Expected Audience <span class="text-muted">(people qty)</span><i class="fa fa-angle-down pull-right"></i>',
                'label_options'    => array(
                    'disable_html_escape' => true,
                ),
                'label_attributes' => array(
                    'class' => 'accordion-title',
                ),
            ),
            'attributes' => array(
                'id'           => 'min_expected_audience',
                'class'        => 'form-control',
                'autocomplete' => 'off',
            ),
        ));

        $this->add(array(
            'type'       => 'Text',
            'name'       => 'max_expected_audience',
            'attributes' => array(
                'id'           => 'max_expected_audience',
                'class'        => 'form-control',
                'autocomplete' => 'off',
            ),
        ));
    }

    protected function _getProductTypes()
    {
        return \Product\Model\Product\Product::getProductTypes();
    }
}
