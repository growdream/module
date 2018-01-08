<?php

namespace Registration\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class RegistrationForm extends Form {

    public $em;

    public function __construct($em = NULL) {
        $this->em = $em;
        parent::__construct('RegistrationForm');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');

        //  $this->add(new Element\Csrf('security'));
        $this->add(array(
            'name' => 'refral_Id',
            'attributes' => array(
                'type' => 'hidden',
                "value" => '1'
            ),
        ));
        $this->add(array(
            'name' => 'firstName',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'First Name',
            ),
                /* 'options' => array(
                  'label' => 'Name:',
                  ), */
        ));
        $this->add(array(
            'name' => 'lastName',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Last Name',
            ),
                /* 'options' => array(
                  'label' => 'Name:',
                  ), */
        ));
        $this->add(array(
            'name' => 'middleName',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Middle Name',
            ),
                /* 'options' => array(
                  'label' => 'Name:',
                  ), */
        ));

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Email Address',
            ),
                /* 	'options' => array(
                  'label' => 'Password:',
                  ), */
        ));

//        $this->add(array(
//    		'name' => 'birth_datesss',
//    		'attributes' => array(
//				'type' => 'text',
//				'class'=>'form-control border-input',
//				'id'=>'bday',
//				'placeholder' => 'Birth Date',
//    		),
//
//        ));

        $this->add(array(
            'name' => 'parent',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Parent',
            ),
//    		'options' => array(
//				'label' => 'Parent',
//    		),
        ));

        $node = new Element\Select('node');
        $node->setEmptyOption("Side");
        $node->setValueOptions(
                ["0" => "Left", "1" => "Right"]
        );
        $node->setAttributes(array(
            'id' => 'node',
            'class' => 'form-control',
        ));
        $node->setDisableInArrayValidator(TRUE);
        $this->add($node);

        $product = new Element\Select('product');
        $product->setEmptyOption("Product");
        $product->setValueOptions(
                $this->product()
        );
        $product->setAttributes(array(
            'id' => 'product',
            'class' => 'form-control',
        ));
        $product->setDisableInArrayValidator(TRUE);
        $this->add($product);

        
        $epin = new Element\Select('epin');
        $epin->setEmptyOption("epin");
        $epin->setAttributes(array(
            'id' => 'epin',
            'class' => 'form-control',
        ));
        $epin->setDisableInArrayValidator(TRUE);
        $this->add($epin);

//        $this->add(
//                array(
//                    'name' => 'epin',
//                    'type' => 'Select',
//                    'attributes' => array(
//                        'class' => 'form-control border-input',
//                        'placeholder' => 'E-pin',
//                        'id'=>'epin'
//                    ),
////    		'options' => array(
////				'label' => 'Key',
////    		),
//        ));

        $gender = new Element\Select('gender');
        $gender->setEmptyOption("Select Gender");
        $gender->setValueOptions(
                $this->getGender()
        );
        $gender->setAttributes(array(
            'id' => 'gender',
            'class' => 'form-control',
        ));
        $gender->setDisableInArrayValidator(TRUE);
        $this->add($gender);

        $this->add(array(
            'name' => 'birth_date',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'id' => 'bday',
                'placeholder' => 'Birth Date',
            ),
//    		'options' => array(
//				'label' => 'Birth Date:',
//    		),
        ));
        $this->add(array(
            'name' => 'mobileNo',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Mobile Number',
            ),
                /* 'options' => array(
                  'label' => 'Repeat Password:',
                  ), */
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'class' => 'form-control border-input',
                'placeholder' => 'Enter Password',
            ),
                /* 'options' => array(
                  'label' => 'Repeat Password:',
                  ), */
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class' => 'btn btn-info btn-fill btn-wd',
            ),
                /* 'options' => array(
                  'label' => 'Save'
                  ), */
        ));
    }

    public function getGender() {
        $selectData = [];
        $selectData['0'] = "Female";
        $selectData['1'] = "Male";

        return $selectData;
    }

    public function product() {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("p.id,p.productName")
                ->from("Registration\Entity\Product", "p")
                ->where("p.status=1");
        $data = $qb->getQuery()->getArrayResult();

        for ($i = 0; $i < count($data); $i++) {
            $selectData[$data[$i]['id']] = $data[$i]['productName'];
        }
        return $selectData;
    }

}
