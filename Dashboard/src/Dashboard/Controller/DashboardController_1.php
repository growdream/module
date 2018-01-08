<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    public function __construct() {
        error_reporting(1);
    }

    protected $em;
    protected $treeArrs;
    protected $treeArrsIndex;
    protected $levelL = 0;
    protected $levelR = 0;

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
        $this->treeArrsIndex = 0;
        $this->treeArrs[$this->treeArrsIndex] = 1;
        $this->callme(1);
//        $this->haschild(3);
        print_r($this->treeArrs);
        echo "<br>";
        $l=0;
        for ($i = 0; $i < 6; $i++) {
            $k = pow(2, $i);
            for ($j = 1; $j <= $k; $j++) {
                echo " ". $this->treeArrs[$l];
                $l++;
            }
            echo "<br>";
        }

        die;
    }

    function callme($uId) {
        $x = 0;
        if($uId>0){
                  echo $uId ." => ";
              echo  $x=  $this->hasleft($uId);  
              echo " - ".  $y=  $this->hasright($uId);
              echo "<br> ";
            //echo "<br> $uId";
//           $x = $this->hasleft($uId);
//             $this->treeArrsIndex++;
//            $this->treeArrs[$this->treeArrsIndex] = $x;
//           $y = $this->hasright($uId);
//             $this->treeArrsIndex++;
//            $this->treeArrs[$this->treeArrsIndex] = $y;


            $this->callme($x); 
//            echo " level ". $this->$levelL++;
            $this->callme($y);
//            echo "<br> level ". $this->$levelL++;
        }
    }

    function haschild($uId) {
        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $user1 = $em->getRepository('Registration\Entity\Registration')->findBy(array('parent' => $user->user_id));
        if ($user1) {
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
            return $user1->id;
        } else {
            return 0;
        }
    }

    function hasleft($uId) {
        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id, "node" => 0));
        if ($user1) {
            //echo "<br> LevalL - ".++$this->levalL;
            //echo " $user->firstName Left - $user1->firstName";

          
            return $user1->id;
        } else {
           
            return 0;
        }
    }

    function hasright($uId) {

        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $user1 = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('parent' => $user->user_id, "node" => 1));
        if ($user1) {
 
            return $user1->id;
        } else {
          
            return 0;
        }
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
