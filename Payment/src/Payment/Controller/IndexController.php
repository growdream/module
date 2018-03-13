<?php

namespace Payment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dashboard\Service\Datatableresponse1;
use Dashboard\Service\sms;

class IndexController extends AbstractActionController {

    public function __construct() {
        error_reporting(1);
    }

    protected $em;
    protected $treedata;
    public $userdata;
    protected $leftChlidDataArr1;
    protected $rightChlidDataArr1;
    protected $tindex = 0;
    protected $levelL1 = 0;
    protected $levelR1 = 0;
    protected $tree1 = [];
    protected $leftChlidDataArr;
    protected $rightChlidDataArr;
    protected $levelL = 0;
    protected $levelR = 0;
    protected $tree = [];
    const MinAmountToPay = 1200;

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

     public function indexAction() {

        $userdata = $this->_checkIfUserIsLoggedIn();
        $leftNode = $this->hasleft($userdata->Id);
        $rightNode = $this->hasright($userdata->Id);
        $this->leftChlidDataArr[] = $leftNode;
        $this->rightChlidDataArr[] = $rightNode;
        $this->callme($leftNode['id'], 0);
        $this->callme($rightNode['id'], 1);
        $userAtLeft1 = array_filter($this->leftChlidDataArr);
        $userAtRight1 = array_filter($this->rightChlidDataArr);
        $datas[] = array(
            'id' => $userdata->Id,
            'user_id' => $userdata->user_id,
            'globalpostion' => '',
            'firstName' => $userdata->firstName,
            'status' => '',
            'lastName' => $userdata->lastName,
            'gender' => '',
            'epin' => 0,
            'refral_Id' => 0,
            'productName' => '',
            'baseValue' => 0,
            'price' => 0,
            'pStatus' => 0,
        );
        array_push($datas, array_merge($userAtLeft1, $userAtRight1));
        $arrm = array();
        $arrm [] = $datas[0];
        foreach ($datas as $key1 => $id1) {

            foreach ($id1 as $key => $id) {
                if (is_array($id))
                    $arrm [] = $id;
            }
        }


        $payarr = array();
        /* =======  */
        $em = $this->getEntityManager();

//          $qb = $em->createQueryBuilder();
//                 $qb->select("r.id,r.user_id,r.firstName,r.lastName")
//                ->from("Registration\Entity\Registration", "r")
//                ;
//        $datas = $qb->getQuery()->getArrayResult();
//        echo "<pre>"; print_r ($datas); echo "</pre>"; die;
        $PayCount = 0;
        $first = 0;
        foreach ($arrm as $key => $id) {

            unset($this->rightChlidDataArr);
            unset($this->leftChlidDataArr);

            $lhs_count = 0;
            $rhs_count = 0;
            $lhs_bv = 0;
            $rhs_bv = 0;
            $tot_bv = 0;
            $actualPayment = 0;
            $pay_least_bv=0;
            $qb = $em->createQueryBuilder();
            $qb->select("p")
                    ->from("Payment\Entity\Payment", "p")
                    ->where("p.uId = " . $id['id'])
                    ->orderBy('p.id', 'DESC')
                    ->setMaxResults(1);
            $PaymentArr = $qb->getQuery()->getArrayResult();
            if ($PaymentArr) {
                $tot_bv = $PaymentArr[0]['tot_bv'];
                $actualPayment = $PaymentArr[0]['actualPayment'];
                $lhs_count = $PaymentArr[0]['lhs_count'];
                $rhs_count = $PaymentArr[0]['rhs_count'];
                $lhs_bv = $PaymentArr[0]['lhs_bv'];
                $rhs_bv = $PaymentArr[0]['lhs_bv'];
                $pay_least_bv = $PaymentArr[0]['pay_least_bv'];
            }

            $leftNode = $this->hasleft($id['id']);
            $rightNode = $this->hasright($id['id']);
            $this->leftChlidDataArr[] = $leftNode;
            $this->rightChlidDataArr[] = $rightNode;
            $this->callme($leftNode['id'], 0);
            $this->callme($rightNode['id'], 1);
            $userAtLeft = array_filter($this->leftChlidDataArr);
            $userAtRight = array_filter($this->rightChlidDataArr);

            $totalPriceL = 0;
            $totalBvL = 0;
            $totalPriceR = 0;
            $totalBvR = 0;
            $lscountL = 0;
            $lscountR = 0;
            $totalbiznessL = 0;
            $totalbiznessR = 0;
            foreach ($userAtLeft as $key => $value) {
                // $totalPriceL += $value['price'];
                if ($value['baseValue']) {
                    $totalBvL += $value['baseValue'];
                    $totalPriceL += $value['price'];
                    $totalbiznessL += $value['bizness'];
                    ++$lscountL;
                }
            }
            foreach ($userAtRight as $key => $value) {
                // $totalPriceR += $value['price'];
                if ($value['baseValue']) {
                    $totalBvR += $value['baseValue'];
                    $totalPriceR += $value['price'];
                    $totalbiznessR += $value['bizness'];
                    ++$lscountR;
                }
            }
//       echo $id['id']." = "; 
//       echo "bvl : $totalBvL, total price: $totalPriceL , count: $lscountL :==: bvl : $totalBvR, total price: $totalPriceR , count: $lscountR <br>";
//            if ($lscountR != 0 && $lscountL != 0 && $totalPriceR != 0 && $totalPriceL != 0 && $lscountR != $lscountL) {
            $willpay = ($totalPriceR > $totalPriceL) ? $totalPriceL : $totalPriceR;

            /* ======== My Info============  */
            if ($first == 0) {
                $myInfo['lscountL'] = $lscountL;
                $myInfo['lscountR'] = $lscountR;
                $myInfo['totalcountLR'] = $lscountL + $lscountR;
                $myInfo['totalbiznessR'] = $totalbiznessR;
                $myInfo['totalbiznessL'] = $totalbiznessL;
                $myInfo['id'] = $id['id'];
                $myInfo['user_id'] = $id['user_id'];
                $myInfo['fullName'] = $id['firstName'] . " " . $id['lastName'];
                $myInfo['totalBvL'] = $totalBvL;
                $myInfo['totalPriceL'] = $totalPriceL;
//                $myInfo['lscountL'] = $lscountL;
                $myInfo['totalBvR'] = $totalBvR;
                $myInfo['totalPriceR'] = $totalPriceR;
//                $myInfo['lscountR'] = $lscountR;
                $willpay = ($totalPriceR > $totalPriceL) ? $totalPriceL : $totalPriceR;
                $bvpadd = ($totalBvR > $totalBvL) ? $totalBvL : $totalBvR;

                 $paddi = intVal(($bvpadd / 100)) + (($bvpadd > 50) ? 1 : 0);
           $willDeductForPadding1 = ($willpay) - (($paddi * 10) * $id['bvrate']) + $paddi;
           
            $pay_least_bv_paddi = intVal(($pay_least_bv / 100)) + (($pay_least_bv > 50) ? 1 : 0);
           $willDeductForPadding = $willDeductForPadding1 - ($actualPayment - (($pay_least_bv_paddi * 10) * $id['bvrate']) + $pay_least_bv_paddi); 

                $myInfo['willPay'] = $willDeductForPadding;
                $myInfo['actualPayment'] = $willpay;
                $first++;
            }
            /* ======== My Info============  */

            $bvpadd = ($totalBvR > $totalBvL) ? $totalBvL : $totalBvR;

           $paddi = intVal(($bvpadd / 100)) + (($bvpadd > 50) ? 1 : 0);
           $willDeductForPadding1 = ($willpay) - (($paddi * 10) * $id['bvrate']) + $paddi;
           
            $pay_least_bv_paddi = intVal(($pay_least_bv / 100)) + (($pay_least_bv > 50) ? 1 : 0);
           $willDeductForPadding = $willDeductForPadding1 - ($actualPayment - (($pay_least_bv_paddi * 10) * $id['bvrate']) + $pay_least_bv_paddi); 

           //echo "<br> - ".$id['firstName'] . " " . $id['lastName']." => ".  $willDeductForPadding." =>".(($paddi * 10) * $id['bvrate'])."=>".$willpay."->".$actualPayment;
             if (($willDeductForPadding >= self::MinAmountToPay ) && $id['productId']!=12 ) {

                
                $payarr[$PayCount]['lscountL'] = $lscountL;
                $payarr[$PayCount]['lscountR'] = $lscountR;
                $payarr[$PayCount]['totalcountLR'] = $lscountL + $lscountR;
                $payarr[$PayCount]['totalbiznessR'] = $totalbiznessR;
                $payarr[$PayCount]['totalbiznessL'] = $totalbiznessL;
              
                if ($lscountR != 0 && $lscountL != 0 && $totalPriceR != 0 && $totalPriceL != 0 && ($lscountR + $lscountL) >= 3) {
                     
                    $payarr[$PayCount]['id'] = $id['id'];
                    $payarr[$PayCount]['user_id'] = $id['user_id'];
                    $payarr[$PayCount]['fullName'] = $id['firstName'] . " " . $id['lastName'];
                    $payarr[$PayCount]['totalBvL'] = $totalBvL;
                    $payarr[$PayCount]['totalPriceL'] = $totalPriceL;
//                $payarr[$PayCount]['lscountL'] = $lscountL;
                    $payarr[$PayCount]['totalBvR'] = $totalBvR;
                    $payarr[$PayCount]['totalPriceR'] = $totalPriceR;
//                $payarr[$PayCount]['lscountR'] = $lscountR;
                    // $willpay = ($totalPriceR > $totalPriceL) ? $totalPriceL : $totalPriceR;


                    $payarr[$PayCount]['willPay'] = $willDeductForPadding;
                    $payarr[$PayCount]['actualPayment'] = $willpay;

                    $PayCount++;
                }
            }
        }
 
        /* =======  */
  
        $this->layout()->setVariable('UserSession', $userdata);
        return new ViewModel([
            "userdata" => $userdata,
            "userAtRight" => $userAtRight1,
            "userAtLeft" => $userAtLeft1,
            "payarr" => $payarr,
            "myInfo" => $myInfo,
        ]);
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

    public function makepaymentAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $request = $this->getRequest();
        $success = 0;
        if ($request->isPost()) {
            $data = $request->getPost();
//            echo "<pre>"; print_r ($data); echo "</pre>"; die;
            $mydetails = $this->showPersonalProfile($data['user_id']);
            $data['user_id'] = $mydetails->user_id;
            $data['uId'] = $mydetails->id;
         
            $paymentEntity = new \Payment\Entity\Payment();
            $paymentEntity->exchangeArray($this->em, $data);
            $this->em->persist($paymentEntity);
            $this->em->flush();
            $success = 1;
            echo "<script>self.close();</script>";
//             return $this->redirect()->toUrl('/payment/payment/index');
        }
        echo $success;
        die;
    }

    public function paymentreleasedAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        $request = $this->getRequest();
      return new ViewModel([
            "data" => "Payment",
        ]);
       
    }
    
    public function paymentreleasedatserverAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
        
       $uId = $userdata->Id;
        $request = $this->getRequest();
          $table = [
            ['tb' => 'Payment\Entity\Payment', 'alise' => 'p'],
            ['tb' => '\Registration\Entity\Registration', 'alise' => 'r', 'on' => "r.id = p.uId", 'join' => 'inner'],
        ];

        $columns = array(
            array('db' => "p.id", 'dt' => 1),
            array('db' => "r.user_id", 'dt' => 2),
            array('db' => "concat(r.firstName,' ', r.lastName)", 'dt' => 3, "as" => "name"),
            array('db' => "p.lhs_count", 'dt' => 4),
            array('db' => "p.rhs_count", 'dt' => 5),
            array('db' => "p.lhs_bv", 'dt' => 6),
            array('db' => "p.rhs_bv", 'dt' => 7),
            array('db' => "p.amounttopay", 'dt' => 8),
            array('db' => "p.tot_bv", 'dt' => 9),
            array('db' => "p.created_at", 'dt' => 10),
            array('db' => "p.status", 'dt' => 11),
           
        );
$where ="";
if($uId>1){
        $where = " r.id = $uId ";
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
    
    
    
     public function paymentAlertMsgsAction() {
//("STOP ! karun ghay");
//die("paymentAlertMsgs");
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
        $leftNode = $this->hasleft($userdata->Id);
        $rightNode = $this->hasright($userdata->Id);
        $this->leftChlidDataArr[] = $leftNode;
        $this->rightChlidDataArr[] = $rightNode;
        $this->callme($leftNode['id'], 0);
        $this->callme($rightNode['id'], 1);
        $userAtLeft1 = array_filter($this->leftChlidDataArr);
        $userAtRight1 = array_filter($this->rightChlidDataArr);
        $datas[] = array(
            'id' => $userdata->Id,
            'user_id' => $userdata->user_id,
            'globalpostion' => '',
            'firstName' => $userdata->firstName,
            'status' => '',
            'lastName' => $userdata->lastName,
            'gender' => '',
            'epin' => 0,
            'refral_Id' => 0,
            'productName' => '',
            'baseValue' => 0,
            'price' => 0,
            'pStatus' => 0,
        ); 
        array_push($datas, array_merge($userAtLeft1, $userAtRight1));
        $arrm = array();
         $arrm [] = $datas[0]; 
        foreach ($datas as $key1 => $id1) {

            foreach ($id1 as $key => $id) {
                if (is_array($id))
                    $arrm [] = $id;
            }
        }
        $arrm [] = $datas[0];


        $payarr = array();
        /* =======  */
        $em = $this->getEntityManager();

//          $qb = $em->createQueryBuilder();
//                 $qb->select("r.id,r.user_id,r.firstName,r.lastName")
//                ->from("Registration\Entity\Registration", "r")
//                ;
//        $datas = $qb->getQuery()->getArrayResult();
//        echo "<pre>"; print_r ($datas); echo "</pre>"; die;
        $PayCount = 0;

        foreach ($arrm as $key => $id) {

            unset($this->rightChlidDataArr);
            unset($this->leftChlidDataArr);

            $lhs_count = 0;
            $rhs_count = 0;
            $lhs_bv = 0;
            $rhs_bv = 0;
            $tot_bv = 0;
            $actualPayment = 0;
            $pay_least_bv = 0;
            $qb = $em->createQueryBuilder();
            $qb->select("p")
                    ->from("Payment\Entity\Payment", "p")
                    ->where("p.uId = " . $id['id'])
                    ->orderBy('p.id', 'DESC')
                    ->setMaxResults(1);
            $PaymentArr = $qb->getQuery()->getArrayResult();
            if ($PaymentArr) {
                $tot_bv = $PaymentArr[0]['tot_bv'];
                $actualPayment = $PaymentArr[0]['actualPayment'];
                $lhs_count = $PaymentArr[0]['lhs_count'];
                $rhs_count = $PaymentArr[0]['rhs_count'];
                $lhs_bv = $PaymentArr[0]['lhs_bv'];
                $rhs_bv = $PaymentArr[0]['lhs_bv'];
                $pay_least_bv = $PaymentArr[0]['pay_least_bv'];
            }

            $leftNode = $this->hasleft($id['id']);
            $rightNode = $this->hasright($id['id']);
            $this->leftChlidDataArr[] = $leftNode;
            $this->rightChlidDataArr[] = $rightNode;
            $this->callme($leftNode['id'], 0);
            $this->callme($rightNode['id'], 1);
            $userAtLeft = array_filter($this->leftChlidDataArr);
            $userAtRight = array_filter($this->rightChlidDataArr);

            $totalPriceL = 0;
            $totalBvL = 0;
            $totalPriceR = 0;
            $totalBvR = 0;
            $lscountL = 0;
            $lscountR = 0;
            $totalbiznessL = 0;
            $totalbiznessR = 0;
            foreach ($userAtLeft as $key => $value) {
                // $totalPriceL += $value['price'];
                if ($value['baseValue']) {
                    $totalBvL += $value['baseValue'];
                    $totalPriceL += $value['price'];
                    $totalbiznessL += $value['bizness'];
                    ++$lscountL;
                }
            }
            foreach ($userAtRight as $key => $value) {
                // $totalPriceR += $value['price'];
                if ($value['baseValue']) {
                    $totalBvR += $value['baseValue'];
                    $totalPriceR += $value['price'];
                    $totalbiznessR += $value['bizness'];
                    ++$lscountR;
                }
            }
//       echo $id['id']." <br>"; 
//       echo "bvl : $totalBvL, total price: $totalPriceL , count: $lscountL :==: bvl : $totalBvR, total price: $totalPriceR , count: $lscountR";
//            if ($lscountR != 0 && $lscountL != 0 && $totalPriceR != 0 && $totalPriceL != 0 && $lscountR != $lscountL) {
           $willpay = ($totalPriceR > $totalPriceL) ? $totalPriceL : $totalPriceR;
            $bvpadd = ($totalBvR > $totalBvL) ? $totalBvL : $totalBvR;

            $paddi = intVal(($bvpadd / 100)) + (($bvpadd > 50) ? 1 : 0);
           $willDeductForPadding1 = ($willpay) - (($paddi * 10) * $id['bvrate']) + $paddi;
           
            $pay_least_bv_paddi = intVal(($pay_least_bv / 100)) + (($pay_least_bv > 50) ? 1 : 0);
           $willDeductForPadding = $willDeductForPadding1 - ($actualPayment - (($pay_least_bv_paddi * 10) * $id['bvrate']) + $pay_least_bv_paddi);
           
              if (($willDeductForPadding >= self::MinAmountToPay ) && $id['productId']!=12 ) {

               
                $payarr[$PayCount]['lscountL'] = $lscountL;
                $payarr[$PayCount]['lscountR'] = $lscountR;
                $payarr[$PayCount]['totalcountLR'] = $lscountL + $lscountR;
                $payarr[$PayCount]['totalbiznessR'] = $totalbiznessR;
                $payarr[$PayCount]['totalbiznessL'] = $totalbiznessL;
                
                if ($lscountR != 0 && $lscountL != 0 && $totalPriceR != 0 && $totalPriceL != 0 && ($lscountR + $lscountL) >= 3) {
                    $payarr[$PayCount]['id'] = $id['id'];
                    $payarr[$PayCount]['user_id'] = $id['user_id'];
                    $payarr[$PayCount]['fullName'] = $id['firstName'] . " " . $id['lastName'];
                    $payarr[$PayCount]['firstName'] = $id['firstName'];
                    $payarr[$PayCount]['mobileNo'] = $id['mobileNo'];
                    $payarr[$PayCount]['totalBvL'] = $totalBvL;
                    $payarr[$PayCount]['totalPriceL'] = $totalPriceL;
//                $payarr[$PayCount]['lscountL'] = $lscountL;
                    $payarr[$PayCount]['totalBvR'] = $totalBvR;
                    $payarr[$PayCount]['totalPriceR'] = $totalPriceR;
//                $payarr[$PayCount]['lscountR'] = $lscountR;
                    
                    $payarr[$PayCount]['willPay'] = $willDeductForPadding;
                    $payarr[$PayCount]['actualPayment'] = $willpay;
                     $amounttopay = ($payarr[$PayCount]['willPay'] > 50000) ? 50000 : ($payarr[$PayCount]['willPay']);
                     $lapsoncap = ($payarr[$PayCount]['willPay'] > 50000) ? ($payarr[$PayCount]['willPay'] - 50000) : 0;
                    $payit1 = $amounttopay - Round(($amounttopay * .20), 2); 
                    
                    $payarr[$PayCount]['payit'] = $amounttopay;

                    $PayCount++;
                }
            }
        }

//   echo "<pre>"; print_r ($payarr); echo "</pre>"; die;

        /* =======  */
        $sms=new sms();
$i=0;
        foreach ($payarr as $key => $value) {
//echo "<pre>"; print_r ($payarr); echo "</pre>"; die;
       $mobile="91".$value['mobileNo'];
       $str = "Congrats ".ucwords($value['firstName'])." - ".$value['user_id'].", Your weekly payout generated successfully, with amount Rs ".$value['payit'].", Please check payment, Cutting is applicable." ;
       
       //// UNCLOMMENT FOLLOWING CODE WHEN WANT TO PAYMENT
    //echo "Please uncomment for payment ";
    //Uncomment 2 lines to send msg
        echo "<br><br>".$str;
        $param=["mobile"=>$mobile,"message"=>urlencode($str)];
        $sms->sendSms($param);
    //End Uncomment
        }
        die("ssss");
    }
    
    
    public function invoiceAction() {
         $userdata = $this->_checkIfUserIsLoggedIn();
         $em=$this->em;
         $this->layout()->setVariable('UserSession', $userdata);
          $request = $this->getRequest();
           if ($request->isPost()) {
           $data = $request->getPost();
          $paymentarr = $em->getRepository("\Payment\Entity\Payment")->find($data['id']);
          $user = $em->getRepository('Registration\Entity\Registration')->findOneBy(array('id' => $paymentarr->uId));
          $personalInfo = $em->getRepository('Dashboard\Entity\PersonalEntity')->findOneBy(array('uId' => $paymentarr->uId));
//          echo "<pre>"; print_r ($paymentarr); echo "</pre>"; die;
         return new ViewModel(["user" => $user,"paymentarr"=>$paymentarr,"personalInfo"=>$personalInfo]);
           }else{
               echo "error"; die;
           }
    }

    

    /*   FIND USER'S LEFT AND RIGHT TREE SEPARATELY */

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
                $this->callme($x['id'], $lr);
                $this->callme($y['id'], $lr);
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
                $this->callme($x['id'], $lr);
                $this->callme($y['id'], $lr);
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

        $qb = $this->em->createQueryBuilder();
        $qb->select("u.id,"
                        . "u.user_id,"
                        . "u.firstName,"
                        . "u.lastName,"
                        . "u.mobileNo,"
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
                        . "u.bvrate,"
                        . "p.price as bizness,"
                        . "(u.bvrate*p.baseValue) as price"
                )
                ->from("Registration\Entity\Registration", "u")
//                ->leftJoin("Registration\Entity\Epin","e","WITH","u.id = e.uId")
                ->leftJoin("Registration\Entity\Product", "p", "WITH", "u.product = p.id")
                ->where("u.parent = '$user->user_id'", " u.node = 0");
        $user1 = $qb->getQuery()->getArrayResult();

        if ($user1) {
            $user1 = $user1[0];
//echo "<br> LevalL - ".++$this->levalL;
//echo " $user->firstName Left - $user1->firstName";
            return ['id' => $user1['id'], 'user_id' => $user1['user_id'], 'globalpostion' => $user1['globalpostion'], 'firstName' => $user1['firstName'], 'mobileNo' => $user1['mobileNo'], 'status' => $user1['status'], 'lastName' => $user1['lastName'], 'gender' => $user1['gender'], 'epin' => $user1['epin'], 'refral_Id' => $user1['refral_Id'], 'productId' => $user1['product'],'productName' => $user1['productName'], 'baseValue' => $user1['baseValue'], 'bvrate' => $user1['bvrate'], 'price' => $user1['price'], 'pStatus' => $user1['pStatus'], 'bizness' => $user1['bizness'],];
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

        $qb = $this->em->createQueryBuilder();
        $qb->select("u.id,"
                        . "u.user_id,"
                        . "u.firstName,"
                        . "u.lastName,"
                        . "u.mobileNo,"
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
                        . "u.bvrate,"
                        . "p.price as bizness,"
                        . "(u.bvrate*p.baseValue) as price"
                )
                ->from("Registration\Entity\Registration", "u")
//                ->leftJoin("Registration\Entity\Epin","e","WITH","u.id = e.uId")
                ->leftJoin("Registration\Entity\Product", "p", "WITH", "u.product = p.id")
                ->where("u.parent = '$user->user_id'", " u.node = 1");
        $user1 = $qb->getQuery()->getArrayResult();

        if ($user1) {
            $user1 = $user1[0];
//echo "<br> LevalL - ".++$this->levalL;
//echo " $user->firstName Left - $user1->firstName";
            return ['id' => $user1['id'], 'user_id' => $user1['user_id'], 'globalpostion' => $user1['globalpostion'], 'firstName' => $user1['firstName'], 'mobileNo' => $user1['mobileNo'], 'status' => $user1['status'], 'lastName' => $user1['lastName'], 'gender' => $user1['gender'], 'epin' => $user1['epin'], 'refral_Id' => $user1['refral_Id'], 'productId' => $user1['product'], 'productName' => $user1['productName'], 'baseValue' => $user1['baseValue'], 'bvrate' => $user1['bvrate'], 'price' => $user1['price'], 'pStatus' => $user1['pStatus'], 'bizness' => $user1['bizness'],];
        } else {
            return 0;
        }
    }

    /*   FIND USER'S LEFT AND RIGHT TREE SEPARATELY  END */

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

    public function showPersonalProfile($userid = null) {
        if ($userid == null) {
            $userdata = $this->_checkIfUserIsLoggedIn();

            $userPersonal = $this->em->getRepository('\Registration\Entity\Registration')->findOneBy(array('user_id' => $userdata->user_id));
        } else {
            $userPersonal = $this->em->getRepository('\Registration\Entity\Registration')->findOneBy(array('user_id' => $userid));
        }
        return $userPersonal;
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
