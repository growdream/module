<?php
namespace Payment\Entity;

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
 * @ORM\Table(name="payment")
 * @property string $artist
 * @property string $title
 * @property int $id
 * @property string $password
 */
class Payment implements InputFilterAwareInterface 
{
    protected $inputFilter;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer");
     */
	  protected $id;
    /**
     * @ORM\Column(type="string")
     */
    protected $user_id;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $uId;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $lhs_count;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $rhs_count;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $lhs_bv;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $rhs_bv;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $pay_least_bv;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $tot_bv;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $created_at;
    
    
     /**
     * @ORM\Column(type="float")
     */
    protected $amounttopay;
    
     /**
     * @ORM\Column(type="integer")
     */
    protected $loss_on_capping;
    
     /**
     * @ORM\Column(type="float")
     */
    protected $repurchase;
        
     /**
     * @ORM\Column(type="float")
     */
    protected $admincharges;
            
     /**
     * @ORM\Column(type="float")
     */
    protected $tds;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $status;
    /**
     * @ORM\Column(type="integer")
     */
    protected $actualPayment;
    
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

   public function exchangeArray(\Doctrine\ORM\EntityManager $em =NUll, $data = array()) 
    {	
        $qb = $em->createQueryBuilder();
        $qb->select("p.tot_bv")
                ->from("Payment\Entity\Payment", "p")
                ->where("p.uId = ".$data['uId'])
                ->orderBy('p.id','DESC')
                ->setMaxResults(1);
        $lsttotbv = $qb->getQuery()->getArrayResult();
        $last_tot_bv = 0;
        if($lsttotbv){
        $last_tot_bv = $lsttotbv[0]['tot_bv']+$data['amounttopay'];  
        }else{
        $last_tot_bv =  $data['amounttopay'];              
        }
        date_default_timezone_set('Asia/Kolkata');
        $this->uId = $data['uId'];
        $this->user_id = $data['user_id'];
        $this->lhs_count = $data['lhs_count'];
        $this->rhs_count = $data['rhs_count'];
        $this->lhs_bv = $data['lhs_bv'];
        $this->rhs_bv = $data['rhs_bv'];
        $this->pay_least_bv = $data['pay_least_bv'];
        $this->tot_bv = $last_tot_bv; //// paid 
        $this->amounttopay = $data['amounttopay'];
        $this->loss_on_capping = $data['lapsoncap'];
        $this->repurchase = $data['repurchase'];
        $this->admincharges = $data['admincharges'];
        $this->tds = $data['tds'];
        $this->actualPayment = $data['actualPayment'];
        $this->status = 1;  
        $this->created_at = date("Y-m-d H:i:s");
    }
    
	/*public function __construct() {
        $this->verificationcode = new ArrayCollection();
    }*/
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        
    }

}