<?php

namespace Dashboard\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class PersonalForm extends Form {

    public function __construct($name = NULL) {
        parent::__construct('personalform');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-signin');


        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'User Id',
                'class' => 'form-control',
                'id' => 'user_id',
            ),
        ));
        $this->add(array(
            'name' => 'fatherName',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Father/Husband Name',
                'class' => 'form-control',
                'id' => 'fatherName',
            ),
        ));
//        $this->add(array(
//            'name' => 'maritalStatus',
//            'attributes' => array(
//                'type' => 'text',
//                'placeholder' => 'Marital Status',
//                'class' => 'form-control',
//                'id' => 'maritalStatus',
//            ),
//        ));
        
        $maritalStatus = new Element\Select('maritalStatus');
        $maritalStatus->setAttributes(array('class' => 'form-control'));
        $maritalStatus->setAttributes(array('id' => 'maritalStatus'));
        $maritalStatus->setEmptyOption("Select Marital status");
        $maritalStatus->setValueOptions(
                array('0'=>'Un-Married','1'=>'Married',"2"=>"Others")
//                $this->getOptionsForSelectMarrital()
        );
        $maritalStatus->setDisableInArrayValidator(true);
        $this->add($maritalStatus);
        
        $accountType = new Element\Select('accountType');
        $accountType->setAttributes(array('class' => 'form-control'));
        $accountType->setAttributes(array('id' => 'accountType'));
        $accountType->setEmptyOption("Select Account Type");
        $accountType->setValueOptions(
                array('Saving'=>'Saving','Current'=>'Current') 
        );
        $accountType->setDisableInArrayValidator(true);
        $this->add($accountType);

        
        $this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Address',
                'class' => 'form-control',
                'id' => 'address',
            ),
        ));
        $this->add(array(
            'name' => 'city',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'City',
                'class' => 'form-control',
                'id' => 'city',
            ),
        ));
        $this->add(array(
            'name' => 'postalCode',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Postal Code',
                'class' => 'form-control',
                'id' => 'postalCode',
            ),
        ));

//        $this->add(array(
//            'name' => 'state',
//            'attributes' => array(
//                'type' => 'text',
//                'placeholder' => 'State',
//                'class' => 'form-control',
//                'id' => 'state',
//            ),
//        ));
//        
        $state = new Element\Select('state');
        $state->setAttributes(array('class' => 'form-control'));
        $state->setAttributes(array('id' => 'state'));
        $state->setEmptyOption("Select State");
        $state->setValueOptions(
//                array('Mahindra'=>'Mahindra','Toyota'=>'Toyota')
                $this->getOptionsForSelectState()
        );
        $state->setDisableInArrayValidator(true);
        $this->add($state);



        $this->add(array(
            'name' => 'bankName',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Bank Name',
                'class' => 'form-control',
                'id' => 'bankName',
            ),
        ));
        $this->add(array(
            'name' => 'accountNo',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Account No',
                'class' => 'form-control',
                'id' => 'accountNo',
            ),
        ));
        $this->add(array(
            'name' => 'branchName',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Branch Name',
                'class' => 'form-control',
                'id' => 'branchName',
            ),
        ));
         
        $this->add(array(
            'name' => 'ifsc',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'IFSC',
                'class' => 'form-control',
                'id' => 'ifsc',
            ),
        ));
        $this->add(array(
            'name' => 'bankCity',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Bank City',
                'class' => 'form-control',
                'id' => 'bankCity',
            ),
        ));
        $this->add(array(
            'name' => 'accountHolder',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Account Holder',
                'class' => 'form-control',
                'id' => 'accountHolder',
            ),
        ));
        $this->add(array(
            'name' => 'panCard',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Pan Card',
                'class' => 'form-control',
                'id' => 'panCard',
            ),
        ));
        $this->add(array(
            'name' => 'panName',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Pan Name',
                'class' => 'form-control',
                'id' => 'panName',
            ),
        ));
        $this->add(array(
            'name' => 'nomineeName',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Nominee Name',
                'class' => 'form-control',
                'id' => 'nomineeName',
            ),
        ));
//        $this->add(array(
//            'name' => 'relation',
//            'attributes' => array(
//                'type' => 'text',
//                'placeholder' => 'Relation with Nominee',
//                'class' => 'form-control',
//                'id' => 'relation',
//            ),
//        ));
        $relation = new Element\Select('relation');
        $relation->setAttributes(array('class' => 'form-control'));
        $relation->setAttributes(array('id' => 'state'));
        $relation->setEmptyOption("Select Relation");
        $relation->setValueOptions(
//                array('Mahindra'=>'Mahindra','Toyota'=>'Toyota')
                $this->getOptionsForSelectRelation()
        );
        $relation->setDisableInArrayValidator(true);
        $this->add($relation);

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Sign in',
                'class' => 'btn btn-lg btn-primary btn-block',
            ),
        ));
    }

    

   public function getOptionsForSelectState() {        

        $selectData["ASSAM"] = "ASSAM";
                            $selectData["ANDRAPRADESH"] = "ANDRAPRADESH";
                            $selectData["BIHAR"] = "BIHAR";
                            $selectData["CHANDIGARH"] = "CHANDIGARH";
                            $selectData["CHATTISGARH"] = "CHATTISGARH";
                            $selectData["DAMAN-DEU"] = "DAMAN-DEU";
                            $selectData["DELHI"] = "DELHI";
                            $selectData["GUJARAT"] = "GUJARAT";
                            $selectData["HARYANA"] = "HARYANA";
                            $selectData["HIMACHAL"] = "HIMACHAL";
                            $selectData["JAMMU AND KASHMIR"] = "JAMMU AND KASHMIR";
                            $selectData["JHARKHAND"] = "JHARKHAND";
                            $selectData["KARNATAKA"] = "KARNATAKA";
                            $selectData["KERALA"] = "KERALA";
                            $selectData["MADHYA PRADESH"] = "MADHYA PRADESH";
                            $selectData["MAHARASHTRA"] = "MAHARASHTRA";
                            $selectData["ORISSA"] = "ORISSA";
                            $selectData["PUNJAB"] = "PUNJAB";
                            $selectData["PONDICHERRY"] = "PONDICHERRY";
                            $selectData["RAJASTAN"] = "RAJASTAN";
                            $selectData["TELANGANA"] = "TELANGANA";
                            $selectData["TAMILNADU"] = "TAMILNADU";
                            $selectData["UTTAR PRADESH"] = "UTTAR PRADESH";
                            $selectData["UTTARAKHAND"] = "UTTARAKHAND";
                            $selectData["WEST BENGAL"] = "WEST BENGAL";
        return $selectData;
    }
   public function getOptionsForSelectRelation() {        
 
                            $selectData["BROTHER"] = "BROTHER";
                            $selectData["BROTHER-IN-LAW"] = "BROTHER-IN-LAW";
                            $selectData["COUSIN"] = "COUSIN";
                            $selectData["DAUGHTER"] = "DAUGHTER";
                            $selectData["FATHER"] = "FATHER";
                            $selectData["GRANDDAUGHTER"] = "GRANDDAUGHTER";
                            $selectData["GRANDSON"] = "GRANDSON";
                            $selectData["HUSBAND"] = "HUSBAND";
                            $selectData["MOTHER"] = "MOTHER";
                            $selectData["NEPHEW"] = "NEPHEW";
                            $selectData["NIECE"] = "NIECE";
                            $selectData["OTHER"] = "OTHER";
                            $selectData["PARENT-IN-LAW"] = "PARENT-IN-LAW";
                            $selectData["PROPERITOR"] = "PROPERITOR";
                            $selectData["SISTER"] = "SISTER";
                            $selectData["SISTER-IN-LAW"] = "SISTER-IN-LAW";
                            $selectData["SON"] = "SON";
                            $selectData["WIFE"] = "WIFE";  
        return $selectData;
    }
         
    
}
