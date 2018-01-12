<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\User;

class IndexController extends AbstractActionController {

    protected $em;

    /**
     * Returns an instance of the Doctrine entity manager loaded from the service 
     * locator
     * 
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }


    public function indexAction() {
               
            $em = $this->getEntityManager();
            
//            $user=new User();
//            $user->exchangeArray(['name'=>'navin']);
//            $em->persist($user);
//            $em->flush();
//            echo $user->__get("id")." name=".$user->__get("name");
//        $success = $em->getRepository('Registration\Entity\Registration')->find(1);
//        print_r($success);
//        die("hello dreammaker");
        $this->layout()->setVariable('indexNo', 1);
        
        
    }
    public function aboutusAction() {               
//            $em = $this->getEntityManager();  
        $this->layout()->setVariable('indexNo', 2);          
    }
    public function productAction() {           
//            $em = $this->getEntityManager(); 
        $this->layout()->setVariable('indexNo', 4);   
        
    }
    public function contactusAction() {           
//            $em = $this->getEntityManager();       
//        return array('indexNo' => 6);
        $this->layout()->setVariable('indexNo', 6);
    }

    public function legalAction() { 
        $this->layout()->setVariable('indexNo', 7);
}
}
