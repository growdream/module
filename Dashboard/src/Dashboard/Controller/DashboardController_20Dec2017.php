<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController {

    public function __construct() {
        error_reporting(1);
    }

    protected $em;
    protected $treedata;
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

    public function dashboardAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//       print_r($userdata); die('111');
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel();
    }

    public function downtreeAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $uId = $userdata->Id;
        if ($request->isPost()) {
            $uId = $request->getPost("id");
        }
//       print_r($userdata); die('111');
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $treedata = [];
        $treedata[] = [$user->id, $user->user_id, $user->globalpostion, $user->firstName, $user->status];
        $treedata[] = $this->hasleft($treedata[0][0]); //left
        $treedata[] = $this->hasright($treedata[0][0]); //right

        $treedata[] = $this->hasleft($treedata[1][0]);
        $treedata[] = $this->hasright($treedata[1][0]);

        $treedata[] = $this->hasleft($treedata[3][0]);
        $treedata[] = $this->hasright($treedata[3][0]);

        $treedata[] = $this->hasleft($treedata[4][0]);
        $treedata[] = $this->hasright($treedata[4][0]);

        //right tree
        $treedata[] = $this->hasleft($treedata[2][0]);
        $treedata[] = $this->hasright($treedata[2][0]);

        $treedata[] = $this->hasleft($treedata[9][0]);
        $treedata[] = $this->hasright($treedata[9][0]);

        $treedata[] = $this->hasleft($treedata[10][0]);
        $treedata[] = $this->hasright($treedata[10][0]);


//        print_r($treedata);die;

        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(["treedata" => $treedata]);
    }

    /*
      public function maintreeAction() {
      $userdata = $this->_checkIfUserIsLoggedIn();
      $em = $this->getEntityManager();
      $request = $this->getRequest();
      $uId = $userdata->Id;
      $user=[];
      if ($request->isPost()) {
      $postData=$request->getPost();
      if(isset($postData['id']))
      {
      $uId = $postData['id'];
      }
      else if(isset($postData['backId'])){
      $parent = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $postData['backId']));
      $uId = $parent->parentId;
      }
      else if(isset($postData['searchUserId'])){
      $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('user_id' => $postData['searchUserId']));
      }

      }
      //       print_r($userdata); die('111');
      if(empty($user)){
      $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
      }
      $treedata = [];
      $treedata[] = [$user->id, $user->user_id, $user->globalpostion, $user->firstName, $user->status];
      $treedata[] = $this->hasleft($treedata[0][0]); //left
      $treedata[] = $this->hasright($treedata[0][0]); //right

      $treedata[] = $this->hasleft($treedata[1][0]);
      $treedata[] = $this->hasright($treedata[1][0]);

      $treedata[] = $this->hasleft($treedata[3][0]);
      $treedata[] = $this->hasright($treedata[3][0]);

      $treedata[] = $this->hasleft($treedata[4][0]);
      $treedata[] = $this->hasright($treedata[4][0]);

      //right tree
      $treedata[] = $this->hasleft($treedata[2][0]);
      $treedata[] = $this->hasright($treedata[2][0]);

      $treedata[] = $this->hasleft($treedata[9][0]);
      $treedata[] = $this->hasright($treedata[9][0]);

      $treedata[] = $this->hasleft($treedata[10][0]);
      $treedata[] = $this->hasright($treedata[10][0]);


      //        print_r($treedata);die;

      return new ViewModel(["treedata" => $treedata]);
      }
     */


    /*     * *** By Navin START*** */

    public function maintreeAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $uId = $userdata->Id;
        $user = [];
        if ($request->isPost()) {
            $postData = $request->getPost();
            if (isset($postData['id'])) {
                $uId = $postData['id'];
            } else if (isset($postData['backId'])) {
                $parent = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $postData['backId']));
                $uId = $parent->parentId;
            } else if (isset($postData['searchUserId'])) {
                $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('user_id' => $postData['searchUserId']));
            }
        }
//       print_r($userdata); die('111');
        if (empty($user)) {
            $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        }
        $treedata = [];
        $treedata[] = [$user->id, $user->user_id, $user->globalpostion, $user->firstName, $user->status];
        $treedata[] = $this->hasleft($treedata[0][0]); //left
        $treedata[] = $this->hasright($treedata[0][0]); //right

        $treedata[] = $this->hasleft($treedata[1][0]);
        $treedata[] = $this->hasright($treedata[1][0]);

        $treedata[] = $this->hasleft($treedata[3][0]);
        $treedata[] = $this->hasright($treedata[3][0]);

        $treedata[] = $this->hasleft($treedata[4][0]);
        $treedata[] = $this->hasright($treedata[4][0]);

        //right tree
        $treedata[] = $this->hasleft($treedata[2][0]);
        $treedata[] = $this->hasright($treedata[2][0]);

        $treedata[] = $this->hasleft($treedata[9][0]);
        $treedata[] = $this->hasright($treedata[9][0]);

        $treedata[] = $this->hasleft($treedata[10][0]);
        $treedata[] = $this->hasright($treedata[10][0]);


//        print_r($treedata);die;
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(["treedata" => $treedata]);
    }

    /*     * *** By Navin END*** */
    /*   Display User Profile */

    public function profileAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//       print_r($userdata); die('111');
        $userProfile = $this->showUserProfile();
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(["userProfile" => $userProfile]);
    }
/*
    public function personalAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//       print_r($userdata); die('111');
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel();
    }
*/
  /*  
        public function personalAction() {
        $user = $this->_checkIfUserIsLoggedIn();
        $uId = $user->Id;
        $em = $this->getEntityManager();
        $this->layout()->setVariable('UserSession', $userdata);
        $form = new \Dashboard\Form\PersonalForm($em);
        $request = $this->getRequest();
        $msg = "";
        if ($request->isPost()) {
            $data = $request->getPost();
            $data['uId']=$uIdForInfo;
        $user1 = $em->getRepository('\Registration\Entity\Registration')->findOneBy(array('user_id' => $request->isPost('user_id')));
       echo "asdasda";print_r($data); die;
        $uIdForInfo=      $user1->id;       
            $responce = $request->getPost();
            $PersonalEntity = new \Dashboard\Entity\PersonalEntity();
                $form->setInputFilter($PersonalEntity->getInputFilter());
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    
                    $PersonalEntity->populate($data);
                    $userarr =  $em->getRepository("\Dashboard\Entity\PersonalEntity")->findOneBy(array('uId' => $uIdForInfo));
                    if(!$userarr){
                    $PersonalEntity->populate($data);
                        $em->persist($PersonalEntity);
                    }else{
                    $userarr->populate($data);                        
                    }
                    $em->flush();
                    $form = new \Dashboard\Form\PersonalForm($em);
                    $msg="success";
                }else{
//                    echo "<pre>"; print_r ($form->getMessages()); echo "</pre>"; die;
                }
        }
         $userdata = $this->_checkIfUserIsLoggedIn();
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(array("form"=>$form,"uId"=>$uId,"msg"=>$msg));
    }
    
    public function userlistserversideAction(){
        $request = $this->getRequest();
        $data= $request->getPost();
        $response = $this->getResponse();
        print_r($data); die;
        
    }
    */
    
        public function personalAction() {
        $user = $this->_checkIfUserIsLoggedIn();
        $uId = $user->Id;
        $user_id = $user->user_id;
        $em = $this->getEntityManager();
        $this->layout()->setVariable('UserSession', $userdata);
        $form = new \Dashboard\Form\PersonalForm($em);
        $request = $this->getRequest();
        $msg = "";
        if ($request->isPost()) {
            $data = $request->getPost();
        $user1 = $em->getRepository('\Registration\Entity\Registration')->findOneBy(array('user_id' => $request->getPost('user_id')));
        
        $uIdForInfo=    ($user1)?$user1->id:0;       
            $responce = $request->getPost();
            $PersonalEntity = new \Dashboard\Entity\PersonalEntity();
                $form->setInputFilter($PersonalEntity->getInputFilter());
                $form->setData($request->getPost());
                //$form->getInputFilter()->get('state')->setRequired(FALSE);
                if ($form->isValid()) {
                    $data['uId']=$uIdForInfo;                    
                    $PersonalEntity->populate($data);
                    $userarr =  $em->getRepository("\Dashboard\Entity\PersonalEntity")->findOneBy(array('uId' => $uIdForInfo));
                    if(!$userarr){
                    $PersonalEntity->populate($data);
                        $em->persist($PersonalEntity);
                    }else{
                    $userarr->populate($data);                        
                    }
                    $em->flush();
                    $form = new \Dashboard\Form\PersonalForm($em);
                    $msg="success";
                }else{
//                    echo "<pre>"; print_r ($form->getMessages()); echo "</pre>"; die;
                }
        }
         $userdata = $this->_checkIfUserIsLoggedIn();
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(array("form"=>$form,"uId"=>$uId,"user_id"=>$user_id,"msg"=>$msg));
    }
    
    public function userlistserversideAction(){
        $request = $this->getRequest();
        $data= $request->getPost();
        $response = $this->getResponse();
        print_r($data); die;
        
    }
    
    public function finduserbyuseridAction(){
        $request = $this->getRequest();
        $data= $_GET['term'];
         $em = $this->getEntityManager();
          
          $qb = $em->createQueryBuilder();
                 $qb->select("r.user_id")
                ->from("Registration\Entity\Registration", "r")
                ->where("r.user_id LIKE '%" . $data . "%'");
        $datas = $qb->getQuery()->getArrayResult();
        echo   json_encode($datas);
        exit;
    }
    public function welcomeAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel();
    }

    public function testAction() {
//        $this->treedata=[];
//        $this->treedata[]=1;
        $this->callme(2);
//        print_r($this->treedata);
//        die;
//        print_r($this->treedata);
//        $this->haschild(2);
//        $gp=3;
//        $start=3;
//        $count=$start+14;
//        
//        $treeData=array();
//        for($i=0;$i<4;$i++){
//            $k=pow(2,$i);
//            $index=0;
//         for($j=1;$j<=$k;$j++){
//           echo ", ". $index;
//           $index++;
//        }   
//        echo "<br>";
////        $index++;
//        }
//        
////        for($i=$start;$i<=$count;$i++){
////            $x=  $this->hasglobalposition($i);
////            $treeData[$index] =  $this->hasglobalposition($x);
////            $index++;
////        }
        ////        }
        ksort($this->treedata);
        echo "<pre>";
        print_r($this->treedata);
        echo "</pre>";
        die;
        die;
    }

    public function globaltreeAction() {

        $gp = 2;
        $start = 3;
        $count = $start + 14;

        $treeData = array();
        for ($i = 0; $i < 4; $i++) {
            $k = pow(2, $i);
            if ($i == 0) {
                $temp = $gp;
            } else {
                $temp = pow(2, $temp);
            }
            $index = 0;
            for ($j = 1; $j <= $k; $j++) {
                echo ",new$k " . " [" . ($temp + $index) . "]";
                $index++;
            }
            $temp = $k;
            echo "<br>";
//        $index++;
        }
        die("end");
    }

    function callme($uId) {
        $this->tindex++;

        $x = 0;
        if ($uId > 0) {

            $x = $this->hasleft($uId);
            $this->treedata[$x[1]] = $x[0];
            $y = $this->hasright($uId);
            $this->treedata[$y[1]] = $y[0];

//                if($this->tindex<9){
            $this->callme($x[0]);
            $this->callme($y[0]);
//                }
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

    function hasglobalposition($position) {

        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('globalpostion' => $position));

        if ($user) {
            return $user->globalpostion;
        } else {
            return 0;
        }
    }

    function haschild($uId) {
        $em = $this->getEntityManager();
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
        $qb = $this->em->createQueryBuilder();
        $qb->select("r.id")
                ->from("Registration\Entity\Registration", "r")
                ->where("r.parent = '" . $user->user_id . "'");
        $dataLeft = $qb->getQuery()->getArrayResult();
        echo "<pre>";
        print_r($dataLeft);
        echo "</pre>";
        $array = array_column($dataLeft, 'id');
        echo "<pre>";
        print_r($array);
        echo "</pre>";
        die;

        if ($user1) {

            return [];
        } else {
            return 0;
        }
    }

    public function showUserProfile() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $qb = $this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.user_id = '$userdata->user_id'");
        /* ->andWhere("u.node =".$param['node']); */
        $userProfile = $qb->getQuery()->getArrayResult();
        unset($userProfile[0]['password']);
        return $userProfile;
    }

    /*
      final private function _checkIfUserIsLoggedIn() {
      $em = $this->getEntityManager();
      $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
      if ($authService->hasIdentity()) {
      $user = $authService->getIdentity();
      $this->layout()->setVariable('UserSession', $user);
      return $user;
      } else {
      $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
      return $this->redirect()->toRoute('signin');
      }
      }
     */

    final private function _checkIfUserIsLoggedIn() {
        $em = $this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
            $user = $authService->getIdentity();
            $this->layout()->setVariable('UserSession', $user);
            return $user;
        } else {
            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
            return $this->redirect()->toRoute('signin');
        }
    }

}
