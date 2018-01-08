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
 * @ORM\Table(name="productcategory")
 */
class Productcategory implements InputFilterAwareInterface {

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
    protected $productCatgory;
    
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
        $this->user_id = "GDM";
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->middleName = $data['middleName'];
        $this->gender = $data['gender'];
        $this->birth_date = $data['birth_date'];
        $this->parent = $data['parent'];
        $this->node = $data['node'];
        $this->product = $data['product'];
        $this->epin = $data['epin'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->mobileNo = $data['mobileNo'];
        $this->refral_Id = $data['refral_Id'];
        $this->created_at = date("Y-m-d H:i:s");
        $this->edited_at = date("Y-m-d H:i:s");
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
                        'name' => 'productCatgory',
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
                                    'max' => 150,
                                ),
                            ),
                            /**                             * XAMPP ERROR Zend\I18n\Filter component requires the intl PHP extension ** */
                            array(
                                'name' => 'Alpha',
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
}
