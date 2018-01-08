<?php
namespace Signin\Entity;

//doctrine
use Doctrine\ORM\Mapping as ORM;
// for input filter validation
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 
/**
 * @ORM\Entity
 * @ORM\Table(name="userrefno")
 */

class Userrefno implements InputFilterAwareInterface
{
protected $inputFilter;
     /**
     * @ORM\Id
     * @ORM\Column(type="integer");
    * @ORM\GeneratedValue(strategy="AUTO")
     */
protected $refNoId;
     /**
     * @ORM\Column(type="integer");
     */
protected $userId;
     /**
     * @ORM\Column
     */   
protected $reftoken;

    /**
     * Populate from an array.
     *
     * @param array $data
     */
    public function exchangeArray($data = array()) 
    {
        $this->refNoId = null;
        $this->userId = $data['u_id'];
        $this->reftoken = $data['token'];
    }
    
  
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
 
            $inputFilter->add(array(
                'name'     => 'user_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
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
            ));
 
            $this->inputFilter = $inputFilter;
        }
 
        return $this->inputFilter;
    }  
}