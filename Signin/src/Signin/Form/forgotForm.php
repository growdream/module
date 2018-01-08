<?php
namespace Signin\Form;
use Zend\Form\Form;

class forgotForm extends Form
{
    public function __construct($name = null) {
        parent::__construct('forgotForm');
        
        
        
        
         $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');
        
        $this->add(array(
    		'name' => 'user_id',
    		'attributes' => array(
				'type' => 'text',
				'placeholder' => 'userId',
                                'class' =>'form-control',
    		),
    		
        ));
       
        $this->add(array(
    		'name' => 'Submit',
    		'attributes' => array(
				'type' => 'submit',
				'value' => 'RESET PASSWORD',
//				'class' => 'btn btn-lg btn-primary btn-block',
    		),
    		
        ));
        
        
        
  
}

}