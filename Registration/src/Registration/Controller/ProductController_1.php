<?php

namespace Registration\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//form
use Registration\Form\RegistrationForm;
use Registration\Form\ProductForm;
use Registration\Form\EpinForm;
//entity
use Registration\Entity\Registration;
use Registration\Entity\Product;
use Registration\Entity\Epin;
use Zend\Crypt\Password\Bcrypt;
use Zend\View\Model\JsonModel;

class ProductController extends AbstractActionController {

    public function __construct() {
        error_reporting(0);
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

    /* Product Registration */
    public function productAction() {
//        die("Product registration");
        $user = $this->_checkIfUserIsLoggedIn();
        $em = $this->getEntityManager();
        $request = $this->getRequest();

        $form = new ProductForm($em);

        $msg = "";

        if ($request->isPost()) {

            $product = new Product($em);
            $form->setInputFilter($product->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $data = $form->getData();
                $data['retailerProfit'] = $data['mrp'] - $data['price'];
                $data['status'] = 1;
                $product->exchangeArray($data);
                $em->persist($product);
                $em->flush();
                $form = new ProductForm($em);
                $msg = "successfuly inserted";
            } else {
//                print_r($form->getMessages());
//                die;
            }
        }

        $this->layout()->setVariable('UserSession', $user);
        return array('form' => $form, "msg" => $msg);
    }

    /* E-Pin Registration */ 
    public function epinAction() {
//        die("Product registration");
        $user = $this->_checkIfUserIsLoggedIn();
        
//        echo $user->Id; die("qqq");
        
        $em = $this->getEntityManager();
        $request = $this->getRequest();

        $form = new EpinForm($em);

        $msg = "";

        if ($request->isPost()) {

            $Epin = new Epin($em);
            $form->setInputFilter($Epin->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                for ($i = 1; $i <= $data['count']; $i++) {
                    $data['pinId'] = $this->randomString(6);
 //                   $data['uId'] = $this->user->Id;
                    
                    $Epin = new Epin($em);
                    $Epin->exchangeArray($data);
                    $em->persist($Epin);
                }
                $em->flush();
                $form = new EpinForm($em);
                $msg = "successfuly inserted";
            } else {
//                print_r($form->getMessages());
//                die;
            }
        }
        $this->layout()->setVariable('UserSession', $user);
        return array('form' => $form, "msg" => $msg);
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
                ->from('Registration\Entity\Epin', 'e')
                ->where("e.pinId ='" . $randomString . "'")
                ->getQuery();
        $epin = $qry->getArrayResult();
        if (isset($epin[0]['pinId']))
            $this->randomString($length);

        return $randomString;
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

}
