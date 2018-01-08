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

  $user=$this->_checkIfUserIsLoggedIn();

  $id=$user->Id;
 $em = $this->getEntityManager();
 
        $form = new RegistrationForm($em);

        $form->get('submit')->setValue('Sign Up Now');

        $request = $this->getRequest();

       

        $msg="";

        if ($request->isPost()) {

            

  $responce= $request->getPost();

  if(isset($responce['pid'])){

      $form->get('parent')->setAttributes(["value"=>$responce['pid']]);

      $form->get('node')->setAttributes(["value"=>$responce['ChildNode']]);

  }else{

            $registration = new Registration($em);

            $form->setInputFilter($registration->getInputFilter());

            $form->setData($request->getPost());

            

            if ($form->isValid()) {

                $data = $request->getPost();
               
                // SMS DATA
                $uname = $data['firstName'];
                $passw = $data['password'];                
                $mob = "91".$data['mobileNo'];
               

                $bcrypt = new Bcrypt(array(

                                                'salt' => 'XMG_-2)*|vU@L)vWJceU96,Og[`)9BNW]F.`66fYrls\'uX^=1V',

                                                'cost' => 10,

                                                ));

                                                $salt  =  'gdm';

                                                $data['password'] = md5($salt . $bcrypt->create($data['password']));

                $data["status"]=1;

                $data["refral_Id"]=$user->Id;

//                print_r($data);die;

                $registration->exchangeArray($data);

                $em->persist($registration);
                /* Change Status used when Inserted - Suraj/Navin/Nitin */ 
                $epinarr = $em->getRepository("Registration\Entity\Epin")->findOneBy(array('id'=>$data['epin']));
                if($epinarr){
                    $epinarr->status=1;
                }
                $em->flush();
                
                
                $uID = $registration->user_id;
                $msgSMS = "Congrats, $uname, You have successfully registered with us Your ID - $uID and Password - $passw visite http://www.growdreammaker.com";
                //SMS Intigration
//                echo $msgSMS;
//               die("End");
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
                //echo "finished urlencode - ";
                //print_r($resp);
                $form = new RegistrationForm($em);

                $msg="successfuly inserted";
                return $this->redirect()->toRoute('dashboard/default', array('controller' => 'dashboard','action' => 'downtree' ));

            } else {

                $data = $request->getPost();

//                print_r($data); die();

                if($data['parent'] != "")

                 $form->get('parent')->setAttributes(["value"=>$data['parent']]);

                 if($data['node'] != "")

      $form->get('node')->setAttributes(["value"=>$data['node']]);

//                print_r($form->getMessages());

//                die;

            }

        }

        }

        $this->layout()->setVariable('UserSession', $user);

        return array('form' => $form,"msg"=>$msg,"uid"=>$id);

    }

    

    public function getepinAction() {

        $em= $this->getEntityManager();

        $request = $this->getRequest();

        $data=$request->getPost();

//        print_r($data);

        

        $qb = $em->createQueryBuilder();

        $qb->select("e.id,e.pinId")

                ->from("Registration\Entity\Registration", "u")

                ->innerJoin("Registration\Entity\Epin","e","WITH","e.userId = u.user_id and e.productId=".$data['productId'])

                ->where("e.status = 0 AND u.id=" . $data['uid'] );

        $data = $qb->getQuery()->getArrayResult();

        echo json_encode($data);

        exit;

    }

    

     final private function _checkIfUserIsLoggedIn()

	{

        $em=$this->getEntityManager();

        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

        if ($authService->hasIdentity()) {

           return $user=$authService->getIdentity();

//            print_r($user);die;

            

        } else {

			//$this->flashMessenger()->clearMessagesFromContainer();

            $this->flashMessenger()->addErrorMessage('Session expired or not valid.');

            return $this->redirect()->toRoute('signin');

        }

	}





}

