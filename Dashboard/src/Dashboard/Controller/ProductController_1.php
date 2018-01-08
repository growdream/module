<?php

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session;
use Zend\Session\Container;

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

    public function allepinAction() {
        $userdata = $this->_checkIfUserIsLoggedIn();
//        echo $userdata->user_id;
        $this->layout()->setVariable('UserSession', $userdata);
        $allPin = $this->showPins();
        return new ViewModel([
            "userdata" => $allPin,
        ]);
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

    public function showPins() {
        $user = $this->_checkIfUserIsLoggedIn();
        $id = $user->user_id;
        $qb = $this->em->createQueryBuilder();
        $qb->select("r.firstName, r.lastName, u.status, u.userId, u.pinId, p.productName, p.baseValue")
                ->from("Registration\Entity\Epin", "u")
 //                ->leftJoin("Registration\Entity\Registration", "r", "WITH", "u.uId = r.id")
                ->leftJoin("Registration\Entity\Registration", "r", "WITH", "u.userId = r.user_id")
                ->leftJoin("Registration\Entity\Product", "p", "WITH", "u.productId = p.id");
        $dataPin = $qb->getQuery()->getArrayResult();
        return $dataPin;
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
