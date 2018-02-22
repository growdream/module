<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session;
use Zend\Session\Container;

use Dashboard\Service\Datatableresponse1;
class ProductController extends AbstractActionController {

    protected $em;
    public $userdata;

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

    public function allproductAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
        $allProduct = $this->showAllProduct();
//        print_r($allProduct); die();
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel([
            "userdata" => $userdata,
            "dataProduct" => $allProduct,
        ]);
    }
public function allproductserversideAction() {
        $table = [
            ['tb' => 'Registration\Entity\Product', 'alise' => 'u'], 
        ];

        $columns = array(
            array('db' => "u.id", 'dt' => 1),
            array('db' => "u.productCatId", 'dt' => 2),
            array('db' => "u.productName", 'dt' => 3),
            array('db' => "u.description", 'dt' => 4),
            array('db' => "u.mrp", 'dt' => 5),
            array('db' => "u.price", 'dt' => 6),
            array('db' => "u.baseValue", 'dt' => 7),
            array('db' => "u.status", 'dt' => 8),
//            array('db' => "ifnull(date_format(a.aer_in_time, '%H:%i'),'No SignIn')", 'dt' => 2, 'as' => 'intime', "order" => "a.aer_in_time"),
        );

        $where ="";
        if($_GET['status']!=""){
        $where = "u.status = ".$_GET['status'];
        }
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

    
    
    public function allepinAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
        $this->layout()->setVariable('UserSession', $userdata);
        $allPin = $this->showPins();
        return new ViewModel([
            "userdata" => $allPin,
        ]);
    }
    public function changeproductstatusAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
         $request = $this->getRequest();
         $data = $request->getPost(); 
         $statusto = $data['statusto'];
         $statusof = $data['statusof'];
                 
                 
           $em = $this->getEntityManager();
          $qb = $em->createQueryBuilder();
          try{
         $q = $qb->update('\Registration\Entity\Product', 'p')
                            ->set('p.status', "'$statusto'")                            
                            ->where("p.id = :statusof")
                            ->setParameter('statusof', $statusof)
                            ->getQuery();
                    $p = $q->execute();
                    echo "success";
          }
          catch(Exception $e){
              echo "Fail";
          }
                    die;
    }

    public function userepinAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
        $this->layout()->setVariable('UserSession', $userdata);
        $allPin = $this->showUserPins();
        return new ViewModel([
            "userdata" => $allPin,
        ]);
    }

    /* Created by Suraj 09-11 */

    public function showAllProduct() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $qb = $this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\Product", "u");
//                ->where("u.node= 1 ");
        $dataProduct = $qb->getQuery()->getArrayResult();
        $this->layout()->setVariable('UserSession', $userdata);
        return $dataProduct;
    }

    public function showUsersLeft() {
        //$userdata = $this->_checkIfUserIsLoggedIn();
        $qb = $this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\RegistrationProduct", "u")
                ->where("u.node= 0 ");
        /* ->andWhere("u.node =".$param['node']); */
        $dataLeft = $qb->getQuery()->getArrayResult();
        return $dataLeft;
    }

    public function showUsersRight() {
        $qb = $this->em->createQueryBuilder();
        $qb->select("u")
                ->from("Registration\Entity\RegistrationProduct", "u")
                ->where("u.node= 1 ");
        $dataLeft = $qb->getQuery()->getArrayResult();
        return $dataLeft;
    }

    /*
      public function showUserPins() {
      $user = $this->_checkIfUserIsLoggedIn();
      $id = $user->user_id;
      $qb = $this->em->createQueryBuilder();
      $qb->select("u.userId, u.pinId, p.productName, p.baseValue")
      ->from("Registration\Entity\Epin", "u")
      ->innerJoin("Registration\Entity\Product", "p", "WITH", "u.productId = p.id")
      ->where("u.userId = '$id'");
      $dataPin = $qb->getQuery()->getArrayResult();
      return $dataPin;
      }
     */

    public function showUserPins() {
        $user = $this->_checkIfUserIsLoggedIn();
        $id = $user->user_id;
        $qb = $this->em->createQueryBuilder();
        $qb->select("r.firstName, r.lastName, u.status, u.userId, u.pinId, p.productName, p.baseValue")
                ->from("Registration\Entity\Epin", "u")
                ->leftJoin("Registration\Entity\Registration", "r", "WITH", "u.uId = r.id")
                ->leftJoin("Registration\Entity\Product", "p", "WITH", "u.productId = p.id")
                ->where("u.userId = '$id'");
        $dataPin = $qb->getQuery()->getArrayResult();
        return $dataPin;
    }

    public function showPins() {
        $user = $this->_checkIfUserIsLoggedIn();
        $id = $user->user_id;
        $qb = $this->em->createQueryBuilder();
        $qb->select("r.firstName, r.lastName, u.status, u.userId, u.pinId, p.productName, p.baseValue")
                ->from("Registration\Entity\Epin", "u")
                ->leftJoin("Registration\Entity\Registration", "r", "WITH", "u.uId = r.id")
                ->leftJoin("Registration\Entity\Product", "p", "WITH", "u.productId = p.id");
        $dataPin = $qb->getQuery()->getArrayResult();
        return $dataPin;
    }

    /*     * *****BY NITIN JAMDHADE 17 DEC 2017  ****** */

    public function transferpinAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        $uId = $userdata->Id;
        $user_id = $userdata->user_id;
        $msg = "";
        $this->layout()->setVariable('UserSession', $userdata);
        $allUnusedPin = $this->showUnusedPins($user_id);
        $em = $this->getEntityManager();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $to_user_id = isset($data['to_user_id']) ? $data['to_user_id'] : 0;
            $to_Arr = $this->getuserinfobyuerid($to_user_id);
            if (count($to_Arr) > 0) {
                $to_uId = $to_Arr->id;
                $qb = $em->createQueryBuilder();
                foreach ($data['epin'] as $value) {
                    $q = $qb->update('\Registration\Entity\Epin', 'ep')
                            ->set('ep.userId', "'$to_user_id'")
                            ->set('ep.uId', "$to_uId")
                            ->where("ep.id = :epinid")
                            ->setParameter('epinid', $value)
                            ->getQuery();
                    $p = $q->execute();

                    $trasnferdata['fromuserid'] = $user_id;
                    $trasnferdata['touserid'] = $to_user_id;
                    $trasnferdata['pinId'] = $value;

                    $transferentity = new \Registration\Entity\Transferpin();
                    $transferentity->exchangeArray($trasnferdata);
                    $em->persist($transferentity);
                    $em->flush();
                }
                $msg = "1";
            } else {
                $msg = "0";
            }
        }

        return new ViewModel([
            "userdata" => $allUnusedPin,
            "uId" => $uId,
            "user_id" => $user_id,
            "msg" => $msg
        ]);
    }

    public function showUnusedPins($user_id) {
        $user = $this->_checkIfUserIsLoggedIn();
        $id = $user_id;
        $qb = $this->em->createQueryBuilder();
        $qb->select(" ep.id as epinId,ep.status, ep.userId, ep.pinId, p.productName, p.baseValue")
                ->from("Registration\Entity\Epin", "ep")
                ->leftJoin("Registration\Entity\Product", "p", "WITH", "ep.productId = p.id")
                ->where("ep.status=0", " ep.userId = '" . $id . "'");
        $dataPin = $qb->getQuery()->getArrayResult();
        return $dataPin;
    }

    public function finduserbyuseridAction() {
        $user = $this->_checkIfUserIsLoggedIn();
        $uId = $user->Id;
        $user_id = $user->user_id;

        $request = $this->getRequest();
        $data = $_GET['term'];
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        if ($uId == 1) {
            $qb->select("r.user_id")
                    ->from("Registration\Entity\Registration", "r")
                    ->where("r.user_id LIKE '%" . $data . "%'")
                    ->andwhere("r.user_id != '" . $user_id . "'")
            ;
        } else {
            $qb->select("r.user_id")
                    ->from("Registration\Entity\Registration", "r")
                    ->where("r.user_id LIKE '%" . $data . "%'")
                    ->andwhere("r.user_id != '" . $user_id . "'")
            ;
        }
        $datas = $qb->getQuery()->getArrayResult();
        echo json_encode(array_column($datas, 'user_id'));

        exit;
    }

    public function getuserinfobyuerid($user_id) {
        $em = $this->getEntityManager();
        $user1 = $em->getRepository('\Registration\Entity\Registration')->findOneBy(array('user_id' => $user_id));
        return $user1;
    }

    /*     * *****BY NITIN JAMDHADE 17 DEC 2017 END ****** */

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
