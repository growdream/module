<?php

namespace Registration\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ProductForm extends Form {
public $em;
    public function __construct($em = NULL) {
        $this->em = $em;
        parent::__construct('ProductForm');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');

        
       $productCatId = new Element\Select('productCatId');
       $productCatId->setEmptyOption("Product Category");
       $productCatId->setValueOptions(
             $this->productCatId()
         );
       $productCatId->setAttributes(array(
         'id'  => '$productCatId',
         'class' => 'form-control',
        ));
       $productCatId->setDisableInArrayValidator(TRUE);
       $this->add($productCatId);
        
        $this->add(array(
            'name' => 'productName',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Enter Product Name',
            ),
                /* 'options' => array(
                  'label' => 'Name:',
                  ), */
        ));
        $this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'description',
            ),
                /* 'options' => array(
                  'label' => 'Name:',
                  ), */
        ));

        $this->add(array(
            'name' => 'mrp',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'MRP',
            ),
                /* 	'options' => array(
                  'label' => 'Password:',
                  ), */
        ));

        $this->add(array(
            'name' => 'price',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'Price',
            ),
//    		'options' => array(
//				'label' => 'Parent',
//    		),
        ));
        $this->add(array(
            'name' => 'baseValue',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control border-input',
                'placeholder' => 'base Value',
            ),
//    		'options' => array(
//				'label' => 'Parent',
//    		),
        ));
        
//
//        $baseValue = new Element\Select('baseValue');
//        $baseValue->setEmptyOption("baseValue");
//        $baseValue->setValueOptions(
//                [
//                    "1" => "BV 1",
//                    "2" => "BV 2",
//                    "3" => "BV 3",
//                    "4" => "BV 4",
//                    "5" => "BV 5",
//                    "6" => "BV 6",
//                    "7" => "BV 7",
//                    "8" => "BV 8",
//                    "9" => "BV 9",
//                    "10" => "BV 10",
//                    "11" => "BV 11",
//                    "12" => "BV 12",
//                    "13" => "BV 13",
//                    "14" => "BV 14",
//                    "15" => "BV 15",
//                    "16" => "BV 16",
//                    "17" => "BV 17",
//                    "18" => "BV 18",
//                    "19" => "BV 19",
//                    "20" => "BV 20",
//                    "21" => "BV 21",
//                    "22" => "BV 22",
//                    "23" => "BV 23",
//                    "24" => "BV 24",
//                    "25" => "BV 25",
//                    "26" => "BV 26",
//                    "27" => "BV 27",
//                    "28" => "BV 28",
//                    "29" => "BV 29",
//                    "30" => "BV 30"
//                ]
//        );
//        $baseValue->setAttributes(array(
//            'id' => 'baseValue',
//            'class' => 'form-control',
//        ));
//        $baseValue->setDisableInArrayValidator(TRUE);
//        $this->add($baseValue);

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
    
         public function productCatId()
    {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("pc.id,pc.productCatgory")
                ->from("Registration\Entity\Productcategory", "pc")
                ->where("pc.status=1");
        $data = $qb->getQuery()->getArrayResult();
        
       for($i=0;$i<count($data);$i++){
           $selectData[$data[$i]['id']]=$data[$i]['productCatgory'];
       }
        return $selectData;
    }

}
