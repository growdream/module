<?php

namespace Dashboard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

//use Zend\I18n\Validator;
//use Doctrine\Common\Collections\ArrayCollection;
/**
 * A music user.
 *
 * @ORM\Entity
 * @ORM\Table(name="personal")
 *  
 */
class PersonalEntity implements InputFilterAwareInterface {

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer");
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $uId;
    /**
     * @ORM\Column(type="string")
     */
    protected $fatherName;
    /**
     * @ORM\Column(type="string")
     */
    protected $maritalStatus;
     /**
     * @ORM\Column(type="string")
     */
    protected $address;
     /**
     * @ORM\Column(type="string")
     */
    protected $city;
     /**
     * @ORM\Column(type="integer")
     */
    protected $postalCode;
    /**
     * @ORM\Column(type="string")
     */
    protected $state;
    /**
     * @ORM\Column(type="string")
     */
    protected $bankName;
    /**
     * @ORM\Column(type="string")
     */
    protected $accountNo;
    /**
     * @ORM\Column(type="string")
     */
    protected $accountType;
    /**
     * @ORM\Column(type="string")
     */
    protected $ifsc;
    /**
     * @ORM\Column(type="string")
     */
    protected $branchName;
    /**
     * @ORM\Column(type="string")
     */
    protected $bankCity;
    /**
     * @ORM\Column(type="string")
     */
    protected $accountHolder;
    /**
     * @ORM\Column(type="string")
     */
    protected $panCard;
    /**
     * @ORM\Column(type="string")
     */
    protected $panName;
    /**
     * @ORM\Column(type="string")
     */
    protected $nomineeName;
    /**
     * @ORM\Column(type="string")
     */
    protected $relation; 
   
    
    
    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property) {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value) {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function populate($data = array()) {
        
        date_default_timezone_set('Asia/Kolkata');
        $this->accountHolder = $data['accountHolder'];
        $this->accountNo = $data['accountNo'];
        $this->accountType = $data['accountType'];
        $this->address = $data['address'];
        $this->bankCity = $data['bankCity'];
        $this->branchName = $data['branchName'];
        $this->bankName = $data['bankName'];
        $this->city = $data['city'];
        $this->fatherName = $data['fatherName'];
        $this->ifsc = $data['ifsc'];
        $this->maritalStatus = $data['maritalStatus'];
        $this->panCard = $data['panCard'];
        $this->panName = $data['panName'];
        $this->postalCode = $data['postalCode'];
        $this->relation = $data['relation'];
        $this->nomineeName = $data['nomineeName'];
        $this->state = $data['state'];
        $this->uId = $data['uId'];
        //print_r($data);
        
    }
 

    /* public function __construct() {
      $this->verificationcode = new ArrayCollection();
      } */

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'uId',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'accountHolder',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'accountNo',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'accountType',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'address',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'bankCity',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'bankName',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'city',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'fatherName',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'ifsc',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'maritalStatus',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'panCard',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'panName',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'postalCode',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'relation',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'nomineeName',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'state',
                        'required' => FALSE,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'user_id',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    "message" => "Invalid input"
                                ),
                            ), 
                        ),
            )));
           

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
