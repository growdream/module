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
 * @ORM\Table(name="dream_pin")
 */
class Epin implements InputFilterAwareInterface {

    protected $em;

    public function __construct($em = null) {
        $this->em = $em;
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
    protected $userId;
    
   

    /**
     * @ORM\Column(type="string")
     */
    protected $productId;

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
        $this->userId = $data['userId'];
        $this->productId = $data['productId'];
        $this->status = isset($data['status']) ? $data['status'] : 0;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            /*             * ****************************START****************************** */
            $inputFilter->add($factory->createInput(array(
                        'name' => 'userId',
                        'required' => true,
                         'validators' => array(
                            array('name' => 'Callback',
                                'options' => array(
                                    'messages' => array(\Zend\Validator\Callback::INVALID_VALUE => 'Opps! Not exist...',),
                                    'callback' => function($value, $context = array()) {
                                        $isValid = $this->checkUserId($value);
//                            $isValid = $value > 0;
                                        return $isValid;
                                    },),),),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'productId',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 200,
                                ),
                            ),
                        /**                         * XAMPP ERROR Zend\I18n\Filter component requires the intl PHP extension ** */
                        /* array(
                          'name' => 'Alpha',
                          'options' => array(
                          'allowWhiteSpace' => FALSE,
                          ),
                          ), */
                        ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                        'name' => 'count',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 0,
                                    'max' => 10,
                                ),
                            ),
                            array(
                                'name' => 'Digits',
                                'options' => array(
                                    'allowWhiteSpace' => FALSE,
                                ),
                            ),
                        ),
            )));
          
            /*             * ****************************END****************************** */
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
    
    public function checkUserId($uid) {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.user_id='" . $uid . "'");
        $data = $qb->getQuery()->getArrayResult();

        return (empty($data)) ? FALSE : TRUE;
    }

    /*     * *********************Suraj***************************************** */

}
