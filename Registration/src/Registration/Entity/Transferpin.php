<?php

namespace Registration\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\I18n\Validator;

//use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="transferpin")
 */
class Transferpin implements InputFilterAwareInterface {

    protected $em;

    public function __construct() {
    
    }

    protected $inputFilter;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $pinId;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $fromuserid;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $touserid;
     
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

    //private $verificationcode;

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray($data) {
        date_default_timezone_set('Asia/Kolkata');
        $this->pinId = $data['pinId'];
        $this->fromuserid = $data['fromuserid'];
        $this->touserid = $data['touserid'];
        $this->status = 1;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

           
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
     

    /*     * *********************Suraj***************************************** */

}
