<?php
namespace Signin\Form;
use Zend\Form\Form;

class changepasswordForm extends Form
{
    public function __construct ($name = null)
    {
            parent::__construct('changepassword');

       $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');
         
        $this->add(array(
    		'name' => 'otp',
            'type' => 'text',
    		'attributes' => array(
				'placeholder' => 'Enter Otp',
                                'class' =>'form-control',
    		),
    		
        ));
//         
//        $this->add(array(
//    		'name' => 'oldpass',
//            'type' => 'Password',
//    		'attributes' => array(
//				'placeholder' => 'Old Password',
//                                'class' =>'form-control',
//    		),
//    		
//        ));
       
         
        $this->add(array(
    		'name' => 'pass',
            'type' => 'Password',
    		'attributes' => array(
				'placeholder' => 'New Password',
                                'class' =>'form-control',
    		),
    		
        ));
       
        
         $this->add(array(
    		'name' => 'cpass',
            'type' => 'Password',
    		'attributes' => array(
				'placeholder' => 'Confirm Password',
                                'class' =>'form-control',
    		),
    		
        ));
        
        
        
        
        $this->add(array(
    		'name' => 'submit',
    		'attributes' => array(
				'type' => 'submit',
				'value' => 'CHANGE PASSWORD',
//				'class' => 'btn btn-lg btn-primary btn-block',
    		),
    		
        ));
        
        
        
        
    }
}