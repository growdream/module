<?php

namespace Dashboard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
/**
 * @ORM\Entity
 * @ORM\Table(name="base_value")
 *  
 */
class Basevalue implements InputFilterAwareInterface {

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
    protected $bvrate;
    /**
     * @ORM\Column(type="string")
     */
    protected $appliedFrom;
    /**
     * @ORM\Column(type="integer")
     */
    protected $addedBy;
     /**
     * @ORM\Column(type="string")
     */
    protected $created_at;
    /**
     * @ORM\Column(type="integer")
     */
    protected $status; 
   
    
    
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

    public function ExchangeArray($data = array()) {
        
        date_default_timezone_set('Asia/Kolkata');
        $this->bvrate = $data['bvrate'];
        $this->appliedFrom = $data['appliedFrom'];
        $this->addedBy = $data['addedBy'];
        $this->created_at = date("Y-m-d H:i:s");
        $this->status = $data['status'];
        
    }
 
    
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'bvrate',
                        'required' => true,
                        'validators' => array(
                            
                            array(
                                'name' => 'Digits',
                                'options' => array(
                                    'allowWhiteSpace' => FALSE,
                                ),
                            ),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'appliedFrom',
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
