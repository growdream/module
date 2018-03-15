<?php

namespace Registration\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Registration\Entity\Registration;
use Registration\Form\RegistrationForm;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\JsonModel;

class RegistrationController extends AbstractActionController {

    public function __construct() {
//        error_reporting(0);
    }

    /**
     * @var Doctrine\ORM\EntityManager
     */

    /**
     * Entity manager instance
     *           
     * @var Doctrine\ORM\EntityManager
     */
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

    protected $sUid;
    protected $bcrypt;

    public function registrationAction() {
//        die("registration");
        
        $user = $this->_checkIfUserIsLoggedIn();
        $id = $user->Id;
        $em = $this->getEntityManager();
        $form = new RegistrationForm($em);
        $form->get('submit')->setValue('Sign Up Now');
        $request = $this->getRequest();
        $msg = "";
        if ($request->isPost()) {
            $responce = $request->getPost();
            if (isset($responce['pid'])) {
                $form->get('parent')->setAttributes(["value" => $responce['pid']]);
                $form->get('node')->setAttributes(["value" => $responce['ChildNode']]);
            } else {
                $registration = new Registration($em);
                $form->setInputFilter($registration->getInputFilter());
                $form->setData($request->getPost());
                if ($form->isValid()) {
                    $data = $request->getPost();
                  
                    $parentUser = $em->getRepository("Registration\Entity\Registration")->findOneBy(array('user_id' => $data['parent']));
                    $data['parentId'] = $parentUser->id;
                    // SMS DATA
                    $uname = $data['firstName'];
                    $passw = $data['password'];
                    $mob = "91" . $data['mobileNo'];
                    $bcrypt = new Bcrypt(array(
                        'salt' => 'XMG_-2)*|vU@L)vWJceU96,Og[`)9BNW]F.`66fYrls\'uX^=1V',
                        'cost' => 10,
                    ));
                    $salt = 'gdm';
                    $data['password'] = md5($salt . $bcrypt->create($data['password']));
                    /* Start Status change 02-12-2017 */
                    $qbS = $em->createQueryBuilder();
                    $qbS->select("e.id,e.baseValue")
                            ->from("Registration\Entity\Product", "e")
                            ->where("e.id = " . $data['product']);
                    $dataS = $qbS->getQuery()->getArrayResult();
//                    print_r($dataS);
                    $baseV = $dataS[0]['baseValue'];
                    if ($baseV == 0) {
                        $data["status"] = 2;
                    } else {
                        $data["status"] = 1;
                    }
//                    echo $data["status"];
//                    die($baseV);
                    /* End Status change 02-12-2017 */
//                    $data["status"] = 1;
                    $data["refral_Id"] = $user->Id;
                    //$data["bvrate"] = 100;
                    $bvdata = $this->getbvValue();
                    $data["bvrate"] = $bvdata['bvrate'];
//                print_r($data);die;
                    $registration->exchangeArray($data);
                    
                    
                    $em->persist($registration);
                    /* Change Status used when Inserted - Suraj/Navin/Nitin */
                    $epinarr = $em->getRepository("Registration\Entity\Epin")->findOneBy(array('id' => $data['epin']));
                    if ($epinarr) {
                        $epinarr->status = 1;
                       // echo $epinarr->productId; die("Suraj");
                        if($epinarr->productId == 22){
                            
                           $dpinarr = $em->getRepository("Registration\Entity\Directpin")->findOneBy(array('pinId' => $epinarr->pinId));  
                           $dpinarr->status = 1;
                        }
                    }
                    $em->flush();
                    
                    /* =====  CODE FOR DIRECT PIN start 
                     * nitin and suraj 
                     * Date: 19 Feb 2018 [ Shiv Jayanti : Jai Bhavani.. Jai Shivaji.. ]                      
                     *                      
                     */
                    if(($data['sponserId'])!=""){
                    if($this->isDirectPinExists($data['sponserId'])){
                    $this->findSponsorOfUsers($data['sponserId']);
                    }
                    //die;
                    
                    }
                    /* ===== CODE FOR DIRECT PIN  end */
                    
                    $uID = $registration->user_id;
//                    $msgSMS = "Congrats, $uname, You have successfully registered with us Your ID - $uID and Password - $passw visite http://www.growdreammaker.com";
//                    //SMS Intigration
////                echo $msgSMS;
////               die("End");
//                    $sendmsg = urlencode($msgSMS);
//                    //cURL resource initialization
//                    $curl = curl_init();
//                    curl_setopt_array($curl, array(
//                        CURLOPT_RETURNTRANSFER => 1,
//                        CURLOPT_URL => "http://nimbusit.net/api.php?username=T4bhandarinavin&password=925422&sender=grodrm&sendto=$mob&message=$sendmsg",
//                    ));
//                    /////send Request and save Response
//                    $resp = curl_exec($curl);
//                    //////close req. to clear up resources
//                    curl_close($curl);
//                    /////echo "finished urlencode - ";
//                    //////print_r($resp);

                    $form = new RegistrationForm($em);

                    $msg = "successfuly inserted";
                 //   return $this->redirect()->toRoute('dashboard/default', array('controller' => 'dashboard', 'action' => 'maintree'));
                     header("location: /dashboard/dashboard/maintree/".$responce['pid']);
                } else {
                    $data = $request->getPost();
//                print_r($data); die();
                    if ($data['parent'] != "")
                        $form->get('parent')->setAttributes(["value" => $data['parent']]);
                    if ($data['node'] != "")
                        $form->get('node')->setAttributes(["value" => $data['node']]);
//                print_r($form->getMessages());
//                die;
                }
            }
        }
        $this->layout()->setVariable('UserSession', $user);
        return array('form' => $form, "msg" => $msg, "uid" => $id);
    }

    public function getsponcernameAction() {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $data = $request->getPost();
        $qb = $em->createQueryBuilder();
        $qb->select("concat(u.firstName,' ',u.lastName) uname")
                ->from("Registration\Entity\Registration", "u")
                ->where("u.user_id='" . $data['sponserId'] . "'");
        $data = $qb->getQuery()->getArrayResult();
        echo json_encode($data);
        exit;
    }

    public function getbvValue() {
        date_default_timezone_set('Asia/Kolkata');
        $today = date("Y-m-d");
        $this->getEntityManager();
        $qb = $this->em->createQueryBuilder()
                ->add("select", "bv")
                ->from("\Dashboard\Entity\Basevalue", "bv")
                ->where("bv.appliedFrom<='$today'")
                ->orderBy("bv.id", "DESC")
                ->setMaxResults(1)
        ;
        $result = $qb->getQuery()->getArrayResult();
        return $result[0];
    }

    public function getepinAction() {
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        $data = $request->getPost();
//        print_r($data);
        $qb = $em->createQueryBuilder();
        $qb->select("e.id,e.pinId")
                ->from("Registration\Entity\Registration", "u")
                ->innerJoin("Registration\Entity\Epin", "e", "WITH", "e.userId = u.user_id and e.productId=" . $data['productId'])
                ->where("e.status = 0 AND u.id=" . $data['uid']);
        $data = $qb->getQuery()->getArrayResult();
        echo json_encode($data);
        exit;
    }

    public function changepassAction() {
        $em = $this->getEntityManager();
        $userdata = $this->_checkIfUserIsLoggedIn();
//        die("change Pass");
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel([
            "userdata" => $userdata,
        ]);
    }

    final private function _checkIfUserIsLoggedIn() {
        $em = $this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
            return $user = $authService->getIdentity();
//            print_r($user);die;
        } else {
            //$this->flashMessenger()->clearMessagesFromContainer();
            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
            return $this->redirect()->toRoute('signin');
        }
    }

    public function findSponsorOfUsers($sponsor) {
        $em = $this->getEntityManager();
//        $sponsor = "gdm978403";
        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('user_id' => $sponsor));
        $UserGP = $user->globalpostion;

        $qb = $em->createQueryBuilder();
        $qb->select("u.globalpostion")
                ->from("Registration\Entity\Registration", "u")
                // ->leftJoin("Registration\Entity\Registration", "uu", "WITH", "uu.sponserId = u.sponserId")
                ->where("u.sponserId='" . $sponsor . "'");
        $GpArray = $qb->getQuery()->getArrayResult();
        $GpArray = array_column($GpArray, 'globalpostion');
        
        $leftSideUser = 0;
        $rightSideUser = 0;
        //$UserGP = 25;
        //$GpArray = [201, 414];
        foreach ($GpArray as $lastGP) {
            for ($i = 0; $i < 64; $i++) {
                $defth = $i;
                $x = pow(2, $defth);
                $stratNo = $x * $UserGP;
                $endNo = ($x * $UserGP + $x - 1);
//                if ($lastGP >= $stratNo && $lastGP <= $endNo) {
//                    if ($lastGP < (($x * $UserGP ) + ($x / 2))) {
//                        $leftSideUser = $lastGP;
//                    }
//                    if ($lastGP >= (($x * $UserGP ) + ($x / 2))) {
//                        $rightSideUser = $lastGP;
//                    }
//                }
                
                /* TEMP CODE NITIN 3 March 2018 */
                if ($lastGP == $stratNo) {                     
                        $leftSideUser = $lastGP;
                    }
                    if ($lastGP == $endNo) {
                        $rightSideUser = $lastGP;
                    }
                /* TEMP CODE NITIN 3 March 2018  ENDS */
                if ($lastGP <= $endNo) {
                    break;
                }
               // die("last");
                    
            }
        }
        if($leftSideUser>0 && $rightSideUser>0){
            $data['userId']=$sponsor;
            $this->directpin($data);
            return 1;
        }else{
            return 0;
        }
//        echo "left : $leftSideUser";
//        echo "<br> Right : $rightSideUser";
//        die;
    }
    
    public function isDirectPinExists($sponsor){
          $em = $this->getEntityManager();
//        $sponsor = "gdm978403";
        $DirectpinArr = $em->getRepository('Registration\Entity\Directpin')->findOneBy(array('userId' => $sponsor));
        return (count($DirectpinArr)>0)?0:1; // 0= Already Exists Not TO proceed, 1= Not Exists  Allow to Proceed
        
    }

     public function directpin($data) {
//        die("Product registration");
        $user = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        
        $msg = "";

            $Dpin = new \Registration\Entity\Directpin($em);
                $pinUser = $em->getRepository("Registration\Entity\Registration")->findOneBy(array('user_id' => $data['userId']));
                    $data['uId'] = $pinUser->id;
                    $data['pinId'] = $this->randomString(6);    
                    $Dpin->exchangeArray($data);
                    $em->persist($Dpin);
                    
                    /*  add into dream_pin table */
                      $Epin = new \Registration\Entity\Epin($em);
                      $data['status'] = 0;
                      $data['productId'] = 20;
                    $Epin->exchangeArray($data);
                    $em->persist($Epin);
                    /* add into dream pin table ends*/
                    
                $em->flush();
                $msg = "1";
    }

    public function randomString($length = 6) {
        $em = $this->getEntityManager();

        $characters = '1234567890'
//                ."abcdefghijklmnopqrstuvwxyz"
                . "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        ;
        $charactersLength = strlen($characters);
        $randomString = substr(str_shuffle($characters), rand(0, 26), $length);

        $qb = $em->createQueryBuilder();
        $qry = $qb->select('e.pinId')
                ->from('Registration\Entity\Directpin', 'e')
                ->where("e.pinId ='" . $randomString . "'")
                ->getQuery();
        $epin = $qry->getArrayResult();
        if (count($epin) > 0) {
            $this->randomString($length);
        } else {
            return "dpin_".$randomString;
        }
    }
    
}
