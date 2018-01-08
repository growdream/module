<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    public function __construct() {
        error_reporting(1);
    }

    protected $em;
    protected $treedata = 0;
    protected $tindex = 0;
    protected $levelL = 0;
    protected $levelR = 0;

    protected $tree=[
        "0-0"=>[],
        "0-1"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],
        "0-0"=>[],

    ];
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

    public function dashboardAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//       print_r($userdata); die('111');
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel();
    }

    public function downtreeAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//       print_r($userdata); die('111');
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel();
    }

    public function testAction() {
        $this->treedata=[];
        $this->treedata[]=1;
        $this->callme(1);
        
        print_r($this->treedata);
//        $this->haschild(3);
        die;
    }
    public function treetestAction() {
        $this->treedata=[];
        $this->treedata[]=1;
        $this->callme(1);
        
        print_r($this->treedata);
//        $this->haschild(3);
        die;
    }
function callme($uId){ 
    $x=0; 
//              if($uId>0){
                  echo $uId ." => ";
              echo  $x=  $this->hasleft($uId);  
              $this->treedata[(++$this->tindex)]= "index->".$this->tindex."\t [parent->$uId L=> child-> $x ]";
              echo " - ".  $y=  $this->hasright($uId);
              $this->treedata[(++$this->tindex)]= "index->".$this->tindex."\t [parent->$uId R=> child-> $y ]";
              
              echo "<br> ";
                if($this->tindex<7){
                $this->callme($x);               
                $this->callme($y);
                }
//              }  
}
    function haschild($uId) {
        $em = $this->getEntityManager();
         $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));       
         $user1 = $em->getRepository('Registration\Entity\Registration')->findBy(array('parent' => $user->user_id));    
         if($user1){
//        echo "<br> LevalL - ".++$this->levalL;
             $nodeL = 0;
             $nodeR = 0;
//             if(isset($user1[0]['node']==1)){
//                $nodeR = $user->user_id;
//             }
//             if(isset($user1[0]['node']==0)){
//                $nodeL = $user->user_id;
//             }
      //  echo " $user->firstName Left - $user1->firstName";
      //  print_r($user1);
         return $user1->id;}else{return 0; }
    }
    function hasleft($uId) {
        if($uId==0){
            return 0;
        }
        $em = $this->getEntityManager();
         $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));       
         $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id,"node"=>0));    
         if($user1){
        //echo "<br> LevalL - ".++$this->levalL;
        //echo " $user->firstName Left - $user1->firstName";
         return $user1->id;}else{return 0; }
    }

    function hasright($uId) {
         
               $em = $this->getEntityManager();
         $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));       
         $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id,"node"=>1));       
        if($user1){
         //echo "<br> LevalR - ".++$this->levalR;
        //echo " $user->firstName right - $user1->firstName";
         return $user1->id;}else{return 0; }
    }

    final private function _checkIfUserIsLoggedIn() {
        $em = $this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();

            return $user;
        } else {
            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
            return $this->redirect()->toRoute('signin');
        }
    }

}
