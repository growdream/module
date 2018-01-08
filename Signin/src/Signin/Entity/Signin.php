<?php
namespace Signin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
   */

class Signin implements InputFilterAwareInterface 
{
    protected $inputFilter;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer");
     */
	  public $Id;
  
    /**
     * @ORM\Column(type="string")
     */
    public $firstName;
    /**
     * @ORM\Column(type="string")
     */
    public $lastName;
    /**
     * @ORM\Column(type="string")
     */
    public $email;

    /**
     * @ORM\Column(type="string")
     */
    public $user_id;
    
    /**
     * @ORM\Column(type="string")
     */
    public $password;


    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function getArrayCopy() 
    {
        return get_object_vars($this);
    }

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
                'name'     => 'user_id',
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