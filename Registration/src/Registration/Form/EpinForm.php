<?php

namespace Registration\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class EpinForm extends Form {
public $em;
    public function __construct($em = NULL) {
        $this->em = $em;
        parent::__construct('MpinForm');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');

        
        
        $this->add(array(
            'name' => 'userId',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Enter UserId',
            ),
                /* 'options' => array(
                  'label' => 'Name:',
                  ), */
        ));
        
       $productId = new Element\Select('productId');
       $productId->setEmptyOption("Product");
       $productId->setValueOptions(
             $this->product()
         );
       $productId->setAttributes(array(
         'id'  => 'productId',
         'class' => 'form-control',
        ));
       $productId->setDisableInArrayValidator(TRUE);
       $this->add($productId);

        $this->add(array(
            'name' => 'count',
            'attributes' => array(
                'type' => 'number',
                'class' => 'form-control border-input',
                'placeholder' => 'count',
            ),
                /* 	'options' => array(
                  'label' => 'Password:',
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
    
        public function product()
    {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("p.id,p.productName,p.baseValue")
                ->from("Registration\Entity\Product", "p")
                ->where("p.status=1");
        $data = $qb->getQuery()->getArrayResult();
        
       for($i=0;$i<count($data);$i++){
           $selectData[$data[$i]['id']] = $data[$i]['productName']." [".$data[$i]['baseValue']." BV]";
       }
        return $selectData;
    }
}
