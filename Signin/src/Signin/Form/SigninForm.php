<?php
namespace Signin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class SigninForm extends Form
{
    public function __construct ($name = NULL)
    {
        parent::__construct('SignForm');
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');
        
        $this->add(new Element\Csrf('security'));
        $this->add(array(
    		'name' => 'user_id',
    		'attributes' => array(
				'type' => 'text',
				'placeholder' => 'Username',
    		),
    		
        ));
        $this->add(array(
    		'name' => 'password',
    		'attributes' => array(
				'type' => 'password',
				'placeholder' => 'Enter password',
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
    		'name' => 'signin',
    		'attributes' => array(
				'type' => 'submit',
				'value' => 'Sign in',
    		),
    		
        ));
    }
}