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
 * @ORM\Table(name="user")
 * @property string $artist
 * @property string $title
 * @property int $id
 * @property string $password
 */
class Dashboard implements InputFilterAwareInterface 
{
    protected $inputFilter;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer");
     */
	  protected $uId;
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
    protected $firstName;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $lastName;
    
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
    public function __get($property) 
    {
        return $this->$property;
    }

    /**
     * Magic setter to save protected properties.
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value) 
    {
        $this->$property = $value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy() 
    {
        return get_object_vars($this);
    }

   public function populate($data = array()) 
    {	
        date_default_timezone_set('Asia/Kolkata');
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        $this->registrationDatenTime = date("Y-m-d H:i:s");
       
		
    }
    
    /**
     * Set password
     *
     * @param  string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
	/*public function __construct() {
        $this->verificationcode = new ArrayCollection();
    }*/
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'email',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
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
				'required' => TRUE,
				'filters' => array(
						array('name' => 'StringTrim'),
				),
				'validators' => array(
					array(
						'name'    => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min'      => 3,
							'max'      => 50,
						),
					),
				),
    		)));
		
			   
			  
            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    } 
}