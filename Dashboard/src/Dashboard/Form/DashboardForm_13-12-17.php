<?php
namespace Dashboard\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class DashboardForm extends Form
{
    public function __construct ($name = NULL)
    {
        parent::__construct('DashboardForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');
        
        $this->add(new Element\Csrf('security'));
        $this->add(array(
    		'name' => 'email',
    		'attributes' => array(
				'type' => 'text',
				'placeholder' => 'Your email',
                                'class' =>'form-control',
    		),
    		
        ));
        $this->add(array(
    		'name' => 'password',
    		'attributes' => array(
				'type' => 'password',
				'placeholder' => 'Enter password',
                                'class' =>'form-control',
    		),
    		
        ));
//        $this->add(array(
//    		'type' => 'checkbox',
//    		'name' => 'remember',
//    		'options' => array(
//				'label' => 'Remember Me?:',
//				'checked_value' => 1,
//    		)
//        ));
        $this->add(array(
    		'name' => 'dashboard',
    		'attributes' => array(
				'type' => 'submit',
				'value' => 'Sign in',
				'class' => 'btn btn-lg btn-primary btn-block',
    		),
    		
        ));
    }
}