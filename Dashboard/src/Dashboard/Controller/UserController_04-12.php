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
         $this->callme($leftNode['id'],0);
        $this->callme($rightNode['id'],1);
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
            $this->callme($x['id'],$lr);
            $this->callme($y['id'],$lr);
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
            $this->callme($x['id'],$lr);
            $this->callme($y['id'],$lr);
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
//        $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id, "node" => 0));
        
         $qb=$this->em->createQueryBuilder();
        $qb->select("u.id,"
                . "u.user_id,"
                . "u.firstName,"
                . "u.lastName,"
                . "u.node,"
                . "u.globalpostion,"
                . "u.status,"
                . "u.product,"
                . "u.epin,"
                . "u.refral_Id,"
                . "u.gender,"
                . "u.parent,"
                . "p.productName,"
                . "p.baseValue," 
                . "p.status as pStatus," 
                . "p.price" 
                )
                ->from("Registration\Entity\Registration","u")
//                ->leftJoin("Registration\Entity\Epin","e","WITH","u.id = e.uId")
                ->leftJoin("Registration\Entity\Product","p","WITH","u.product = p.id")
                ->where("u.parent = '$user->user_id'", " u.node = 0"); 
       $user1=$qb->getQuery()->getArrayResult(); 
      
        if ($user1) {
             $user1 =  $user1[0];
            //echo "<br> LevalL - ".++$this->levalL;
            //echo " $user->firstName Left - $user1->firstName";
            return ['id'=>$user1['id'], 'user_id'=>$user1['user_id'], 'globalpostion'=>$user1['globalpostion'], 'firstName'=>$user1['firstName'], 'status'=>$user1['status'],'lastName'=>$user1['lastName'],'gender'=>$user1['gender'],'epin'=>$user1['epin'],'refral_Id'=>$user1['refral_Id'],'productName'=>$user1['productName'],'baseValue'=>$user1['baseValue'],'price'=>$user1['price'],'pStatus'=>$user1['pStatus'],];
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
//        $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id, "node" => 1));
        
         $qb=$this->em->createQueryBuilder();
        $qb->select("u.id,"
                . "u.user_id,"
                . "u.firstName,"
                . "u.lastName,"
                . "u.node,"
                . "u.globalpostion,"
                . "u.status,"
                . "u.product,"
                . "u.epin,"
                . "u.refral_Id,"
                . "u.gender,"
                . "u.parent,"
                . "p.productName,"
                . "p.baseValue," 
                . "p.status as pStatus," 
                . "p.price" 
                )
                ->from("Registration\Entity\Registration","u")
//                ->leftJoin("Registration\Entity\Epin","e","WITH","u.id = e.uId")
                ->leftJoin("Registration\Entity\Product","p","WITH","u.product = p.id")
                ->where("u.parent = '$user->user_id'", " u.node = 1"); 
       $user1=$qb->getQuery()->getArrayResult(); 
      
        if ($user1) {
             $user1 =  $user1[0];
            //echo "<br> LevalL - ".++$this->levalL;
            //echo " $user->firstName Left - $user1->firstName";
            return ['id'=>$user1['id'], 'user_id'=>$user1['user_id'], 'globalpostion'=>$user1['globalpostion'], 'firstName'=>$user1['firstName'], 'status'=>$user1['status'],'lastName'=>$user1['lastName'],'gender'=>$user1['gender'],'epin'=>$user1['epin'],'refral_Id'=>$user1['refral_Id'],'productName'=>$user1['productName'],'baseValue'=>$user1['baseValue'],'price'=>$user1['price'],'pStatus'=>$user1['pStatus'],];
        } else {
            return 0;
        }
    }
    /*   FIND USER'S LEFT AND RIGHT TREE SEPARATELY  END*/
}
