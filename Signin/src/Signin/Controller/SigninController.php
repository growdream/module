<?php

namespace Signin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Signin\Entity\Signin;
use Signin\Form\SigninForm;

use Signin\Form\forgotForm; // forgot pass form display email
use Signin\Entity\Userrefno; // for forgot Change password

use Signin\Form\changepasswordForm; // chage pass 
use Signin\Entity\ChangePass; 

use Zend\Crypt\Password\Bcrypt;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session;
use Zend\Session\Container;


class SigninController extends AbstractActionController
{
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
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    } 

        
    protected $fname;
    protected $lname;
    protected $bcrypt;
    protected $identity;
    
    public function signinAction()
    {
  //  $this->checkUrlPath();
        
        $form = new SigninForm();
        $form->get('signin')->setValue('Sign In');
        $request = $this->getRequest();
        $em=$this->getEntityManager();
       
        if ($request->isPost())
            {
            
                $signin = new Signin();
                $form->setInputFilter($signin->getInputFilter());
                $form->setData($request->getPost());
                if ($form->isValid())
                    {
//                    echo "hi";die;
                        $data=$form->getData();
                        $user = new Signin;
//                        $user->setPassword($data['password']);
                        $data=$form->getData();
                        $user_id=$data['user_id'];
                        $bcrypt = new Bcrypt(array(
                                                'salt' => 'XMG_-2)*|vU@L)vWJceU96,Og[`)9BNW]F.`66fYrls\'uX^=1V',
                                                'cost' => 10,
                                                ));
                                                $data['salt']  =  'gdm';
                        $password = md5($data['salt'] . $bcrypt->create($data['password']));
                         
                        $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('user_id' => $user_id, 'password' => $password));
//                        print_r($user); die;
                        if ($user !== null)
                        {
//                            $status=$user->status;
//                            if($status==3)
//                            {
//                                $this->flashMessenger()->addErrorMessage('Your account disabled by admin.');
//                                return array('form' => $form,'flashMessages' => $this->flashMessenger()->getErrorMessages());
//                                exit;
//                            }
                          
                          // If you used another name for the authentication service, change it here
                            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                            $adapter = $authService->getAdapter();
                            $adapter->setIdentityValue($data['user_id']);
                            $adapter->setCredentialValue($data['password']);
                            $authResult = $authService->authenticate();
                            if($authResult->isValid()) {
                                
                                $identity = $authResult->getIdentity();
                                $authService->getStorage()->write($identity);
//                            print_r($identity); die("2");    
                            return $this->redirect()->toRoute('dashboard', array('action' => 'dashboard' ));
                            }
                            else
                            {
                                $this->flashMessenger()->addErrorMessage('Invalid User ID or password.');
                                return array('form' => $form,'flashMessages' => $this->flashMessenger()->getErrorMessages());
                            }
                                //        return $this->redirect()->toRoute('home');
                        }
                         else
                        {
                            $this->flashMessenger()->addErrorMessage('Invalid User ID or password.');
                            return array('form' => $form,'flashMessages' => $this->flashMessenger()->getErrorMessages());
                        }
                   }
//                   else{
//                       print_r($form->getMessages());
//                       die;
//                   }
            } 
            
        return array('form' => $form,'flashMessages' => $this->flashMessenger()->getCurrentErrorMessages());
    }
    
    
        /**
     * Log out action
     *
     * The method destroys session for a logged user
     *
     * @return redirect to specific action
     */
    public function logoutAction()
    {
        $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }
        return $this->redirect()->toRoute('signin');
    }
    
     
     // for forgot password
    public function forgotAction() {
    
        $form = new forgotForm();
        $request = $this->getRequest();
      
        if ($request->isPost()) {
            $entForgot = new Userrefno();
            $form->setInputFilter($entForgot->getInputFilter());
            $form->setData($request->getPost());
             
            if ($form->isValid()) {
                $qb = $this->getEntityManager()->createQueryBuilder();
                $user_id=$request->getPost('user_id');
                $qry = $qb->select('u.id uId,u.mobileNo')->from('Registration\Entity\Registration', 'u')->where("u.user_id ='$user_id'")->getQuery();
                $userid = $qry->execute();

                if (isset($userid[0]['uId'])) {
                    $otp=$this->randomString(4);
                    $data = ['u_id' => $userid[0]['uId'],
                        'token' => $otp];
                    $entForgot->exchangeArray($data);
                    $this->getEntityManager()->persist($entForgot);
                    $this->getEntityManager()->flush();
                    /*
                     * send $otp by message
                     */
                     
                $mob="91".$userid[0]['mobileNo'];
                $msgSMS = "$otp is your OTP for change password.";

                $sendmsg = urlencode($msgSMS);

                //cURL resource initialization
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_RETURNTRANSFER => 1,
                  CURLOPT_URL =>"http://nimbusit.net/api.php?username=T4bhandarinavin&password=925422&sender=grodrm&sendto=$mob&message=$sendmsg",
                ));
                //send Request and save Response
                $resp = curl_exec($curl);
                //close req. to clear up resources
                curl_close($curl);
                     
                     
//                    $hosturl=$_SERVER['HTTP_HOST'];
//                    $forgotpasswordlink=$hosturl."/signin/passwordreset/";
//                    $mailLink = "<a href='".$forgotpasswordlink. md5($data['u_id'] . "cabsaas") . "-" . $data['token']."'>Password Reset</a>";
//                    $emailidVal = $request->getPost()->email;
                    return $this->redirect()->toRoute('signin', array('action' => 'passwordreset','key'=> $user_id));
                   // return new ViewModel(array("form" => $form, "successEmail" => "Send", "mailLink" => $mailLink,"mailid" => $request->getPost('user_id')));
                } else {
                    return new ViewModel(array("form" => $form, "errEmail" => "Wrong User Id"));
                }
            }
        }
        
        return new ViewModel(array("form" => $form));
    }
    
    // for forgot password
    public function passwordresetAction() {
        $user_id = $this->params()->fromRoute('key', 0);
        $cform = new changepasswordForm();
 if($user_id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qry = $qb->select('u.id uId,u.mobileNo')->from('Registration\Entity\Registration', 'u')->where("u.user_id ='$user_id'")->getQuery();
        $userid = $qry->execute();
    }    
        if (!empty($userid)) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $otpdata=$request->getPost('otp');
        $qb1 = $this->getEntityManager()->createQueryBuilder();
        $qry = $qb1->select('r.refNoId,r.userId')
                ->from('Signin\Entity\Userrefno', 'r')
                ->where("r.reftoken ='$otpdata' and r.userId=".$userid[0]['uId'])
                ->orderBy('r.refNoId', 'DESC')
                ->setMaxResults(1)
                ->getQuery();
        $refuser = $qry->execute();
        if(!empty($refuser)){
                $change = new ChangePass();
                $cform->setInputFilter($change->getInputFilter());
                $cform->setData($request->getPost());
//                $cform->getInputFilter()->get('oldpass')->setRequired(false);
                if ($cform->isValid()) {
                    //echo "valid";die;
                    if ($request->getPost()->pass == $request->getPost()->cpass) {
                        $qb = $this->getEntityManager()->createQueryBuilder();
                        $q = $qb->update('Registration\Entity\Registration', 'u')
                                ->set('u.password', '?1')
                                ->where('u.id = ?2')
                                ->setParameter(1, $this->bcryptPass($request->getPost()->pass))
                                ->setParameter(2, $refuser[0]['userId'])
                                ->getQuery();
                        $update = $q->execute();
                        if ($update) {
                            $qb2 = $this->getEntityManager()->createQueryBuilder();
                            $qry = $qb2->delete('Signin\Entity\Userrefno', 'r')
                                    ->where('r.refNoId = ?1')
                                    ->setParameter(1, $refuser[0]['refNoId'])
                                    ->getQuery();
                            $delete = $qry->execute();
                        }
                        return new ViewModel(array("success" => "changed"));
                    } else {
                        return new ViewModel(array("form" => $cform, "uid" => $user_id, "errCpass" => "notsame"));
                    }
                }
        }else {
            return new ViewModel(array("form" => $cform, "uid" => $user_id, "errOtp" => "InvalidOTP"));
        }
            }
            return new ViewModel(array("form" => $cform, "uid" => $user_id));
        } 
        
        else {
            return new ViewModel(array("iderr" => "error"));
        }
    }
    
     // encrypt password
     public function bcryptPass($str) {
        $bcrypt = new Bcrypt(array(
            'salt' => 'XMG_-2)*|vU@L)vWJceU96,Og[`)9BNW]F.`66fYrls\'uX^=1V',
            'cost' => 10,
        ));
        $salt = 'gdm';
        $password = md5($salt . $bcrypt->create($str));
        return $password;
    }

     

    public function randomString($length = 10) {
        $characters = '123456789'
                . 'abcdefghijklmnpqrstuvwxyz'
                . 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = substr(str_shuffle($characters), rand(0, 25), $length);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qry = $qb->select('r.refNoId')->from('Signin\Entity\Userrefno', 'r')->where("r.reftoken ='" . $randomString . "'")->getQuery();
        $refuser = $qry->execute();
        if (isset($refuser[0]['refNoId']))
            randomString($length);

        return $randomString;
    }

       final private function _checkIfUserIsLoggedIn()
	{
        $em=$this->getEntityManager();
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        if ($authService->hasIdentity()) {
            $user=$authService->getIdentity();
//            print_r($user);die;
            $userId=$authService->getIdentity()->uId;
           $this->layout()->setVariable('UserSession', $userdata);
            
        } else {
			//$this->flashMessenger()->clearMessagesFromContainer();
            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');
            return $this->redirect()->toRoute('signin');
        }
	}
     
    
    
    
}

