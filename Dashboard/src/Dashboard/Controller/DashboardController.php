<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Signin\Form\changepasswordForm; // chage pass 
use Signin\Entity\ChangePass;
use Registration\Form\RegistrationForm;
use Dashboard\Service\Datatableresponse1;


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

    public function getControllerManager($controller) {
        $this->cm = $this->getServiceLocator()->get('ControllerManager')->get($controller);
        return $this->cm;
    }

    public function dashboardAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $uId = $userdata->Id;

        $user = [];
        if (empty($user)) {
            $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
//            print_r($user); die("user data");
        }
        $treedata = [];
        $treedata[] = [$user->id, $user->user_id, $user->globalpostion, $user->firstName, $user->status, $user->lastName, $user->created_at, $user->sponserId];
        // print_r($treedata); die();
//        $treedata[0]['left'] = $this->hasleft($treedata[0][0]); //left
//        $treedata[0]['right'] = $this->hasright($treedata[0][0]); //right
        //$treedata[] = $this->hasleft($treedata[0][0]);
        //$treedata[] = $this->hasright($treedata[0][0]);
        $treedata = $this->findLeftRightCount(0, $treedata[0][0], $treedata);

        unset($userdata->password);  // Removed Password from Session
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(["user" => $user, "treedata" => $treedata]);
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
            // print_r($user); die("user data");
        }
        $treedata = [];
        $treedata[] = [$user->id, $user->user_id, $user->globalpostion, $user->firstName, $user->status, $user->lastName, $user->created_at, $user->sponserId];
        // print_r($treedata); die();
//        $treedata[0]['left'] = $this->hasleft($treedata[0][0]); //left
//        $treedata[0]['right'] = $this->hasright($treedata[0][0]); //right
        $treedata[] = $this->hasleft($treedata[0][0]);
        $treedata[] = $this->hasright($treedata[0][0]);
        $treedata = $this->findLeftRightCount(0, $treedata[0][0], $treedata);

        $treedata[] = $this->hasleft($treedata[1][0]);
        $treedata[] = $this->hasright($treedata[1][0]);
        $treedata = $this->findLeftRightCount(1, $treedata[1][0], $treedata);

        $treedata[] = $this->hasleft($treedata[3][0]);
        $treedata[] = $this->hasright($treedata[3][0]);
        $treedata = $this->findLeftRightCount(3, $treedata[3][0], $treedata);

        $treedata[] = $this->hasleft($treedata[4][0]);
        $treedata[] = $this->hasright($treedata[4][0]);
        $treedata = $this->findLeftRightCount(4, $treedata[4][0], $treedata);

        //right tree
        $treedata[] = $this->hasleft($treedata[2][0]);
        $treedata[] = $this->hasright($treedata[2][0]);
        $treedata = $this->findLeftRightCount(2, $treedata[2][0], $treedata);

        $treedata[] = $this->hasleft($treedata[9][0]);
        $treedata[] = $this->hasright($treedata[9][0]);
        $treedata = $this->findLeftRightCount(9, $treedata[9][0], $treedata);

        $treedata[] = $this->hasleft($treedata[10][0]);
        $treedata[] = $this->hasright($treedata[10][0]);
        $treedata = $this->findLeftRightCount(10, $treedata[10][0], $treedata);

        $treedata[] = $this->hasleft($treedata[5][0]);
        $treedata[] = $this->hasright($treedata[5][0]);
        $treedata = $this->findLeftRightCount(5, $treedata[5][0], $treedata);

        $treedata[] = $this->hasleft($treedata[6][0]);
        $treedata[] = $this->hasright($treedata[6][0]);
        $treedata = $this->findLeftRightCount(6, $treedata[6][0], $treedata);

        $treedata[] = $this->hasleft($treedata[7][0]);
        $treedata[] = $this->hasright($treedata[7][0]);
        $treedata = $this->findLeftRightCount(7, $treedata[7][0], $treedata);

        $treedata[] = $this->hasleft($treedata[8][0]);
        $treedata[] = $this->hasright($treedata[8][0]);
        $treedata = $this->findLeftRightCount(8, $treedata[8][0], $treedata);

        $treedata[] = $this->hasleft($treedata[11][0]);
        $treedata[] = $this->hasright($treedata[11][0]);
        $treedata = $this->findLeftRightCount(11, $treedata[11][0], $treedata);

        $treedata[] = $this->hasleft($treedata[12][0]);
        $treedata[] = $this->hasright($treedata[12][0]);
        $treedata = $this->findLeftRightCount(12, $treedata[12][0], $treedata);

        $treedata[] = $this->hasleft($treedata[13][0]);
        $treedata[] = $this->hasright($treedata[13][0]);
        $treedata = $this->findLeftRightCount(13, $treedata[13][0], $treedata);

        $treedata[] = $this->hasleft($treedata[14][0]);
        $treedata[] = $this->hasright($treedata[14][0]);
        $treedata = $this->findLeftRightCount(14, $treedata[14][0], $treedata);

        $treedata[] = $this->hasleft($treedata[15][0]);
        $treedata[] = $this->hasright($treedata[15][0]);
        $treedata = $this->findLeftRightCount(15, $treedata[15][0], $treedata);


//
//        echo "<pre>";
//        print_r($treedata);
//        echo "</pre>";
//        die;
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(["treedata" => $treedata]);
    }

    public function maintreeAction1() {
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
//        $this->params()->fromPost();   // From POST
//$this->params()->fromQuery();  // From GET
//$this->params()->fromRoute();  // From RouteMatch
//$this->params()->fromHeader(); // From header
//$this->params()->fromFiles();  // From file being uploaded
        
        $userdata = $this->_checkIfUserIsLoggedIn();
        $editid=$this->params()->fromRoute('id',null);
        if($userdata->Id==1){
        $user_id=$editid==null?$userdata->user_id:$editid;
        }
        else{
        $user_id=$userdata->user_id;
        }
        
//       print_r($userdata); die('111');
        $userProfile = $this->showUserProfile($editid);
        $error=[];
        $error1=[];
        $profileError=[];
        $userform = new RegistrationForm($this->em);
        $changepassword = new changepasswordForm();
        $ChangeUserPassword = new changepasswordForm();
        $request=  $this->getRequest();
        
        if($request->isPost()){
            $data=$request->getPost();
            if(isset($data['selfpass'])){
                $this->getControllerManager("\Signin\Controller\Signin");
                $password = $this->cm->bcryptPass($data['otp']);
                         
                $user = $this->em->getRepository('Registration\Entity\Registration')->findOneBy(array('user_id' => $userdata->user_id, 'password' => $password));
            if(!empty($user)){
                $change = new ChangePass();
                $changepassword->setInputFilter($change->getInputFilter());
                $changepassword->setData($request->getPost());
//                $cform->getInputFilter()->get('oldpass')->setRequired(false);
                if ($changepassword->isValid()) {
                    
                if ($data['pass'] == $data['cpass']) {
                    
                    $user->password= $this->cm->bcryptPass($data['pass']);
                    $this->em->flush();
                }
                else{
                    $error['notsame']="Password and Confirm Password Should Be Same";
                }
                
                    }
                else{
                    $error= $changepassword->getMessages();
                }
//                    die();
            }
            else{
                $error['notsame']="Old password is wrong";
            }
                        
         
       
            }
            else if(isset($data['userpass'])){
                $this->getControllerManager("\Signin\Controller\Signin");
                $user = $this->em->getRepository('Registration\Entity\Registration')->findOneBy(array('user_id' =>$data['otp']));
            if(!empty($user)){
                $change = new ChangePass();
                $ChangeUserPassword->setInputFilter($change->getInputFilter());
                $ChangeUserPassword->setData($request->getPost());
                if ($ChangeUserPassword->isValid()) {
                    
                if ($data['pass'] == $data['cpass']) { 
                    
                    $user->password= $this->cm->bcryptPass($data['pass']);
                    $this->em->flush();
                }
                else{
                    $error1['notsame']="Password and Confirm Password Should Be Same";
                }
                
                    }
                else{
                    $error1= $ChangeUserPassword->getMessages();
                }
//                    die();
            }
            else{
                $error1['notsame']="user id not exist";
            }
                        
         
       
            }
            else{
                
                $registration = new \Registration\Entity\Registration($em);
                $userform->setInputFilter($registration->getInputFilter());
                $userform->setData($request->getPost());
                $userform->setValidationGroup('firstName','middleName', 'lastName','gender','birth_date', 'mobileNo', 'email');
                if ($userform->isValid()) {
                    $data=$request->getPost();
                    
                    $profile=  $this->em->getRepository("Registration\Entity\Registration")->findOneBy(["user_id"=>$user_id]);
                    $profile->firstName=$data['firstName'];
                    $profile->middleName=$data['middleName'];
                    $profile->lastName=$data['lastName'];
                    $profile->gender=$data['gender'];
                    $profile->birth_date=$data['birth_date'];
                    $profile->mobileNo=$data['mobileNo'];
                    $profile->email=$data['email'];
                    $this->em->flush();
                    $userProfile = $this->showUserProfile($user_id);
                }
                else{
                    $profileError=$userform->getMessages();
                }
                
                
            }
        }
        
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel([
            "userProfile" => $userProfile,
            "userform" => $userform,
            "changepassword" => $changepassword,
            "ChangeUserPassword" => $ChangeUserPassword,
            "error" => $error,
            "error1" => $error1,
            "profileError" => $profileError,
            "ulogin" => $userdata,
            "editid" => $editid,
            ]);
    }

    public function getprofileAction(){
        $request=$this->getRequest();
        $editid=$request->getPost('editid');
        $editid=$editid!=""?$editid:null;
     $userProfile = $this->showUserProfile($editid);
echo json_encode($userProfile);
die;
    }

    public function getpersonaldetailsAction(){
        $request=$this->getRequest();
        $user_id=$request->getPost('user_id');
        $user_id=$user_id!=""?trim($user_id):0;
        
        $em = $this->getEntityManager();
 
        $qb = $em->createQueryBuilder();
        $qb->select("p")
                ->from("\Dashboard\Entity\PersonalEntity", "p")
                ->leftJoin("Registration\Entity\Registration", "r", "WITH", "p.uId = r.id")
                ->where("r.user_id = '" . $user_id . "'");
        $datas = $qb->getQuery()->getArrayResult();
         
echo json_encode($datas);
die;
    }

    /*
      public function personalAction() {
      $userdata = $this->_checkIfUserIsLoggedIn();
      //       print_r($userdata); die('111');
      $this->layout()->setVariable('UserSession', $userdata);
      return new ViewModel();
      }
     */
    
     
    public function personalAction() {
        $user = $this->_checkIfUserIsLoggedIn();
        $uId = $user->Id;
        $user_id = $user->user_id;
        $em = $this->getEntityManager();
//     print_r($userEn); die;
        $this->layout()->setVariable('UserSession', $userdata);
        $form = new \Dashboard\Form\PersonalForm($em);
        if($uId>1){
        $userEn = $em->getRepository('\Dashboard\Entity\PersonalEntity')->findOneBy(array('uId' => $uId));
        if($userEn){
        $form->bind($userEn);
        }
        }
        $request = $this->getRequest();
        $msg = "";
        if ($request->isPost()) {
            $data = $request->getPost();
            $user1 = $em->getRepository('\Registration\Entity\Registration')->findOneBy(array('user_id' => $request->getPost('user_id')));

            $uIdForInfo = ($user1) ? $user1->id : 0;
            $responce = $request->getPost();
            $PersonalEntity = new \Dashboard\Entity\PersonalEntity();
            $form->setInputFilter($PersonalEntity->getInputFilter());
            $form->setData($request->getPost());
            //$form->getInputFilter()->get('state')->setRequired(FALSE);
            if ($form->isValid()) {
                $data['uId'] = $uIdForInfo;
                $PersonalEntity->populate($data);
                $userarr = $em->getRepository("\Dashboard\Entity\PersonalEntity")->findOneBy(array('uId' => $uIdForInfo));
                if (!$userarr) {
                    $PersonalEntity->populate($data);
                    $em->persist($PersonalEntity);
                } else {
                    $userarr->populate($data);
                }
                $em->flush();
                $form = new \Dashboard\Form\PersonalForm($em);
                $msg = "success";
            } else {
//                    echo "<pre>"; print_r ($form->getMessages()); echo "</pre>"; die;
            }
        }
        $userdata = $this->_checkIfUserIsLoggedIn();
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(array("form" => $form, "uId" => $uId, "user_id" => $user_id, "msg" => $msg));
    }

    public function userlistserversideAction() {
        $request = $this->getRequest();
        $data = $request->getPost();
        $response = $this->getResponse();
        print_r($data);
        die;
    }

    public function finduserbyuseridAction() {
        $request = $this->getRequest();
        $data = $_GET['term'];
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select("r.user_id")
                ->from("Registration\Entity\Registration", "r")
                ->where("r.user_id LIKE '%" . $data . "%'");
        $datas = $qb->getQuery()->getArrayResult();
        echo json_encode($datas);
        exit;
    }

    public function welcomeAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $uId = $userdata->Id;
        $user = [];
        if (empty($user)) {
            $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $uId));
//            print_r($user); die("user data");
        }
        $welcomeData = [];
        $welcomeData[] = [$user->id, $user->user_id, $user->globalpostion, $user->firstName, $user->middleName, $user->lastName, $user->email, $user->created_at, $user->sponserId, $user->parent, $user->mobileNo, $user->product];
        //$treedata = $this->findLeftRightCount(0, $treedata[0][0], $treedata);
//         print_r($welcomeData);         die();

        unset($userdata->password);  // Removed Password from Session 

        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel(["user" => $user, "welcomeData" => $welcomeData]);
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

    function callme($uId, $lr) {

        if ($lr == 0) {
            $this->tindex++;
            $x = 0;
            if ($uId > 0) {

                $x = $this->hasleft($uId);
                $this->leftChlidDataArr[] = $x;
                $y = $this->hasright($uId);
//            $this->leftChlidDataArr[$y[1]] = $y[1];
                $this->leftChlidDataArr[] = $y;

//                if($this->tindex<9){
                $this->callme($x[0], $lr);
                $this->callme($y[0], $lr);
//                }
            }
        } else if ($lr == 1) {
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
                $this->callme($x[0], $lr);
                $this->callme($y[0], $lr);
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
//            return [$user1->id, $user1->user_id, $user1->globalpostion, $user1->firstName, $user1->status, $user1->created_at, $user1->sponserId];
            return [$user1->id, $user1->user_id, $user1->globalpostion, $user1->firstName, $user1->status, $user1->lastName, $user1->created_at, $user1->sponserId];
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
            return [$user1->id, $user1->user_id, $user1->globalpostion, $user1->firstName, $user1->status, $user1->lastName, $user1->created_at, $user1->sponserId];
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

    public function findLeftRightCount($ls, $uId, $treedata) {
        $this->leftChlidDataArr = array();
        $this->rightChlidDataArr = array();
        $leftNode = $this->hasleft($uId);
        $rightNode = $this->hasright($uId);
        $this->leftChlidDataArr[] = $leftNode;
        $this->rightChlidDataArr[] = $rightNode;
        $this->callme($leftNode[0], 0);
        $this->callme($rightNode[0], 1);
        $userAtLeft = array_filter($this->leftChlidDataArr);
        $userAtRight = array_filter($this->rightChlidDataArr);
        $treedata[$ls]['left'] = count($userAtLeft);
        $treedata[$ls]['right'] = count($userAtRight);
        return $treedata;
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

    public function showUserProfile($user_id=null) {
        $userdata = $this->_checkIfUserIsLoggedIn();
        
        if($userdata->Id==1){
        $user_id=$user_id==null?$userdata->user_id:$user_id;
        }
        else{
        $user_id=$userdata->user_id;
        }
        $qb = $this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.user_id = '$user_id'");
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

    public function basevalueAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $form = new \Dashboard\Form\bvvalueForm();
        
        $request =  $this->getRequest();
        if($request->isPost()){
            $basevalue = new \Dashboard\Entity\Basevalue();
                $form->setInputFilter($basevalue->getInputFilter());
                $form->setData($request->getPost());
//                $cform->getInputFilter()->get('oldpass')->setRequired(false);
                if ($form->isValid()) {
                    $data=$request->getPost();
                    
                    $data['addedBy']=$userdata->Id;
                    $data['status']=1;
                    
                    $basevalue->ExchangeArray($data);
                    
                    $this->em->persist($basevalue);
                    $this->em->flush();
                    $form = new \Dashboard\Form\bvvalueForm();
                }
                
        }
        return new ViewModel(["form"=>$form]);
    }

    public function basevalueserversideAction() {
        $table = [
            ['tb' => 'Dashboard\Entity\Basevalue', 'alise' => 'p'],
//            ['tb' => '\Owner\Entity\Mycalendar', 'alise' => 'c', 'on' => "c.caldate BETWEEN sd.s_start AND sd.s_end", 'join' => 'inner'],
        ];

        $columns = array(
            array('db' => "p.id", 'dt' => 1),
            array('db' => "p.bvrate", 'dt' => 2),
            array('db' => "p.appliedFrom", 'dt' => 3),
            array('db' => "p.created_at", 'dt' => 4),
//            array('db' => "ifnull(date_format(a.aer_in_time, '%H:%i'),'No SignIn')", 'dt' => 2, 'as' => 'intime', "order" => "a.aer_in_time"),
        );

        $where = "";
//$where = [$wherestring, "groupby" => "a.aer_id"];
        //echo $where;      
        $datatableobjec = new Datatableresponse1($this->getEntityManager());
        $result = $datatableobjec->complex($_GET, $table, $columns, $where);
        $start = $_REQUEST['start'];
        $start++;

        foreach ($result['data'] as &$res) {
            $res[0] = (string) $start;
            $start++;
        }
        echo json_encode($result);
        exit;
    }

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
