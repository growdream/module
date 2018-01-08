<?php

namespace Dashboard\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class bvvalueForm extends Form {

    public function __construct($name = NULL) {
        parent::__construct('bvvalueForm');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');


        $this->add(array(
            'name' => 'bvrate',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Bv Rate',
                'class' => 'form-control',
                'id' => 'bvrate',
            ),
        ));
        $this->add(array(
            'name' => 'appliedFrom',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Apply Date',
                'class' => 'form-control',
                'id' => 'appliedFrom',
            ),
        ));



        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'submit',
                'class' => 'btn btn-lg btn-primary btn-block',
            ),
        ));
    }

}
