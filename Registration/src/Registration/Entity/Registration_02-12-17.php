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
 * @ORM\Table(name="users")
 */
class Registration implements InputFilterAwareInterface {

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
    protected $user_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string")
     */
    protected $middleName;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastName;

    /**
     * @ORM\Column(type="integer")
     */
    protected $gender;

    /**
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $mobileNo;

    /**
     * @ORM\Column(type="string")
     */
    protected $birth_date;

    /**
     * @ORM\Column(type="string")
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer")
     */
    protected $parentId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $node;

    /**
     * @ORM\Column(type="string")
     */
    protected $epin;

    /**
     * @ORM\Column(type="integer")
     */
    protected $product;

    /**
     * @ORM\Column(type="integer")
     */
    protected $refral_Id;

    /**
     * @ORM\Column(type="string")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="string")
     */
    protected $edited_at;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(type="integer")
     */
    protected $globalpostion;

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
        $this->user_id = "GDM" . $this->randomString(6);
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->middleName = $data['middleName'];
        $this->gender = $data['gender'];
        $this->birth_date = $data['birth_date'];
        $this->parent = $data['parent'];
        $this->parentId = $data['parentId'];
        $this->node = $data['node'];
        $this->product = $data['product'];
        $this->epin = $data['epin'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->mobileNo = $data['mobileNo'];
        $this->refral_Id = $data['refral_Id'];
        $this->created_at = date("Y-m-d H:i:s");
        $this->edited_at = date("Y-m-d H:i:s");
        $this->globalpostion = $this->getGlobalPosition($data['parent'],$data['node']);
        $this->status = isset($data['status']) ? $data['status'] : 0;
    }

    public function randomString($length = 6) {
        $characters = '1234567890';
        $charactersLength = strlen($characters);
        $randomString = substr(str_shuffle($characters), rand(0, 3), $length);

        $qb = $this->em->createQueryBuilder();
        $qry = $qb->select('u')
                ->from('Registration\Entity\Registration', 'u')
                ->where("u.user_id ='" . $randomString . "'")
                ->getQuery();
        $refuser = $qry->getArrayResult();
        if (isset($refuser[0]['user_id']))
            $this->randomString($length);

        return $randomString;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'epin',
                        'required' => true,
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'parent',
                        'required' => true,
                        'validators' => array(
                            array('name' => 'Callback',
                                'options' => array(
                                    'messages' => array(\Zend\Validator\Callback::INVALID_VALUE => 'Opps! Not exist...',),
                                    'callback' => function($value, $context = array()) {
                                        $isValid = $this->checkParent($value);
//                            $isValid = $value > 0;
                                        return $isValid;
                                    },),),),
            )));

            $inputFilter->add($factory->createInput(
                            array('name' => 'node',
                                'required' => true,
                                'validators' => array(
                                    array('name' => 'Callback',
                                        'options' => array(
                                            'messages' => array(\Zend\Validator\Callback::INVALID_VALUE => 'Oops! Already exist...',),
                                            'callback' => function($value, $context = array()) {
                                                $isValid = $this->checkNode($context);
//                            $isValid = $value > 0;
                                                return $isValid;
                                            },),),),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'firstName',
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
                                    'max' => 100,
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

            $inputFilter->add($factory->createInput(array(
                        'name' => 'middleName',
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
                                    'max' => 100,
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

            $inputFilter->add($factory->createInput(array(
                        'name' => 'lastName',
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
                                    'max' => 100,
                                ),
                            ),
                            array(
                                'name' => 'Alpha',
                                'options' => array(
                                    'allowWhiteSpace' => FALSE,
                                ),
                            ),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'email',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 100,
                                ),
                            ),
                            array(
                                'name' => 'EmailAddress',
                                'options' => array(
                                    'useMxCheck' => FALSE
                                ),
                            ),
                        ),
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'password',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 5,
                                    'max' => 15,
                                ),
                            ),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'mobileNo',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 10,
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


            $this->inputFilter = $inputFilter;
        }
//            die('in getInputFilter2');

        return $this->inputFilter;
    }

    public function checkNode($param) {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.parent='" . $param['parent'] . "'")
                ->andWhere("u.node =" . $param['node']);
        $data = $qb->getQuery()->getArrayResult();

        return (empty($data)) ? TRUE : FALSE;
    }

    public function checkParent($parent) {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.user_id='" . $parent . "'");
        $data = $qb->getQuery()->getArrayResult();

        return (empty($data)) ? FALSE : TRUE;
    }
    
    public function getGlobalPosition($parent,$node) {
        $em = $this->em;
        $qb = $em->createQueryBuilder();
        $qb->select("u.globalpostion")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.user_id='" . $parent . "'");
        $data = $qb->getQuery()->getArrayResult();
        $globalpostionNext = (intval($data[0]['globalpostion'])*2)+$node; 
        return $globalpostionNext;
    }

}
