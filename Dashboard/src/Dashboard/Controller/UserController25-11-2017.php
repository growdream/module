<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session;
use Zend\Session\Container;

class UserController extends AbstractActionController {

    protected $em;
    public $userdata;
 protected $leftChlidDataArr;
 protected $rightChlidDataArr;
    protected $tindex = 0;
    protected $levelL = 0;
    protected $levelR = 0;
    protected $tree = [];
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

    public function alluserAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
        $leftNode=$this->hasleft($userdata->Id);
        $rightNode=$this->hasright($userdata->Id);
        $this->leftChlidDataArr[]=$leftNode;
        $this->rightChlidDataArr[]=$rightNode;
         $this->callme($leftNode[0],0);
        $this->callme($rightNode[0],1);
       $userAtLeft= array_filter($this->leftChlidDataArr);
       $userAtRight= array_filter($this->rightChlidDataArr);
//      echo "<pre>"; print_r ($userAtLeft); echo "</pre>"; die;
 
//        foreach ($leftArr as $key => $value) {
//            
//    echo " ;  ".$value; 
//}
//        die; 
//        $userAtLeft = $this->showUsersLeft();
//        $userAtRight = $this->showUsersRight();
        
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel([
            "userdata" => $userdata,
            "userAtRight" => $userAtRight,
            "userAtLeft" => $userAtLeft,
        ]);
    }

    /* Created by Suraj 09-11 */

    public function showUsersLeft() {
        //$userdata = $this->_checkIfUserIsLoggedIn();
        $qb=$this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration","u")
                ->where("u.node= 0 ");
                /*->andWhere("u.node =".$param['node']);*/
       $dataLeft=$qb->getQuery()->getArrayResult();
        return $dataLeft;
    }
    public function showUsersRight() {
        $qb=$this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration","u")
                ->where("u.node= 1 ");
       $dataLeft=$qb->getQuery()->getArrayResult();
        return $dataLeft;
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

    
    /*   FIND USER'S LEFT AND RIGHT TREE SEPARATELY */
    
    function callme($uId,$lr) {
       
        if($lr==0){
        $this->tindex++;

        $x = 0;
        if ($uId > 0) {

            $x = $this->hasleft($uId);
            $this->leftChlidDataArr[] = $x;
            $y = $this->hasright($uId);
//            $this->leftChlidDataArr[$y[1]] = $y[1];
            $this->leftChlidDataArr[] = $y;

//                if($this->tindex<9){
            $this->callme($x[0],$lr);
            $this->callme($y[0],$lr);
//                }
        }
        }else if($lr==1){
        $this->tindex++;

        $x = 0;
        if ($uId > 0) {

            $x = $this->hasleft($uId);
//            $this->rightChlidDataArr[$x[1]] = $x[1];
            $this->rightChlidDataArr[] = $x;
            $y = $this->hasright($uId);
//            $this->rightChlidDataArr[$y[1]] = $y[1];
            $this->rightChlidDataArr[] = $y;

//                if($this->tindex<9){
            $this->callme($x[0],$lr);
            $this->callme($y[0],$lr);
//                }
        }
        }
    }

    function hasleft($uId) {
        if ($uId == 0) {
            return 0;
        }
        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id, "node" => 0));
        if ($user1) {
            //echo "<br> LevalL - ".++$this->levalL;
            //echo " $user->firstName Left - $user1->firstName";
            return [$user1->id, $user1->user_id, $user1->globalpostion, $user1->firstName, $user1->status];
        } else {
            return 0;
        }
    }

    function hasright($uId) {
        if ($uId == 0) {
            return 0;
        }
        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id, "node" => 1));
        if ($user1) {
            //echo "<br> LevalR - ".++$this->levalR;
            //echo " $user->firstName right - $user1->firstName";
            return [$user1->id, $user1->user_id, $user1->globalpostion, $user1->firstName, $user1->status];
        } else {
            return 0;
        }
    }

    
    /*   FIND USER'S LEFT AND RIGHT TREE SEPARATELY  END*/
            
            
}
