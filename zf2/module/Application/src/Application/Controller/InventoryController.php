<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\AddControlForm;
use Application\Entity\Control;
use Doctrine\ORM\Query\ResultSetMapping;
class InventoryController extends AbstractActionController
{
    public function indexAction()
    {
        $em = $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default');
		$data = $em->getRepository('Application\Entity\Customer')->findAll();
        
        return new ViewModel(array('customers'=>$data));
    }

 public function showCustomerAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $customerId = $this->params()->fromRoute('id');
		$data = $em->getRepository('Application\Entity\Customer')->findById($customerId);


		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('code', 'code');
		$rsm->addScalarResult('product_code', 'product_code');
		$rsm->addScalarResult('batch_no', 'batch_no');
		$rsm->addScalarResult('product_type', 'product_type');
		$rsm->addScalarResult('initial_ammount', 'initial_ammount');
		$rsm->addScalarResult('balance', 'balance');
		$rsm->addScalarResult('product_name', 'product_name');
		$rsm->addScalarResult('control_count', 'control_count');
		$rsm->addScalarResult('quarntine', 'quarntine');
		$rsm->addScalarResult('released', 'released');
		$rsm->addScalarResult('rejected', 'rejected');

		
 
		
		
		$sql = "SELECT code ,	product_code ,	product_name ,	batch_no ,	product_type,sum(initial_ammount ) initial_ammount, sum(balance) balance,sum(quarntine) quarntine,sum(released) released,sum(rejected) rejected, count(balance) control_count  FROM `control` WHERE `customer_id`=$customerId ";
		if($this->params()->fromPost('product_type')){
			$sql .=" and product_type=".$this->params()->fromPost('product_type'); 
			}

		$sql .= " group by (product_code )";
		$query = $em->createNativeQuery($sql,$rsm);
		$result = $query->getResult();
		$addControlForm = new AddControlForm ();
		$form = $addControlForm->buildFilterForm($em);
        return new ViewModel(array('customer'=>$data[0],'controls'=>$result,'filter_form'=>$form));
    }
  public function moveToReleasedAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $controlId = $this->params()->fromRoute('id');
		$control = $em->find('Application\Entity\Control',$controlId ); 
		$control->setReleased($control->getQuarntine());
		$control->setQuarntine(0.0);
		
		$em->flush();
		$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );

		
 
    }
  public function moveToRejectedAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $controlId = $this->params()->fromRoute('id');
		$control = $em->find('Application\Entity\Control',$controlId ); 
		$control->setRejected($control->getQuarntine());
		$control->setQuarntine(0.0);
		
		$em->flush();
		$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );



        return new ViewModel(array('customer'=>$data[0],'controls'=>$result));
    }
    
 
 public function showProductAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $productId = $this->params()->fromRoute('id');
		$data = $em->getRepository('Application\Entity\Control')->findBy( array('code' => $productId));

		return new ViewModel(array('controls'=>$data  ));
		
    }



 public function addControlAction()
	{
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$addControlForm = new AddControlForm ();
		$form = $addControlForm->build($em);
		$newControl = new Control();
		$form->bind($newControl);

        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
				if ($form->isValid()) {
				 	$customer = $em->getRepository('Application\Entity\Customer')->findById($this->params()->fromRoute('id'));

					$newControl->setCustomer($customer[0]);
					$newControl->setbalance($newControl->getInitialAmmount());
					$newControlTransactions->setQuarntine($newControl->getInitialAmmount());

					$newControl->setUser($this->identity());
					$em->persist($newControl);
					$newControlTransactions = new \Application\Entity\ControlTransactions();
					$newControlTransactions->setIn($newControl->getInitialAmmount());
					$newControlTransactions->setbalance($newControl->getInitialAmmount());

					$newControlTransactions->setDescription('initial input ');
					$newControlTransactions->setcontrol($newControl);
					$newControlTransactions->setUser($this->identity());
					$em->persist($newControlTransactions);

					$em->flush();
					
					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showCustomer', 'id' => $this->params()->fromRoute('id')) );
					//echo "Good Form "; die();
				}

		}
        
		return new ViewModel(array('form'=>$form));
	}


 public function showControlAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$data = $em->getRepository('Application\Entity\Control')->findById($this->params()->fromRoute('id'));

        return new ViewModel(array('control'=>$data[0]));
    }


 public function addTransactionAction()
	{
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$addControlForm = new AddControlForm ();
		$form = $addControlForm->buildAddTransactionForm($em);
		$newControlTransactions = new \Application\Entity\ControlTransactions();

		$form->bind($newControlTransactions);

        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
				if ($form->isValid()) {
				 	$control = $em->find('Application\Entity\Control',$this->params()->fromRoute('id'));
 
				 	$control->setBalance($control->getBalance()-$newControlTransactions->getOut());
				 	
				 	if($control->getQuarntine()>0.0) $control->setQuarntine($control->getBalance());
				 	if($control->getReleased()>0.0) $control->setReleased($control->getBalance());
				 	if($control->getRejected()>0.0) $control->setRejected($control->getBalance());
				 	
				 	
 					$em->flush();

 					$newControlTransactions->setcontrol($control );
					$newControlTransactions->setUser($this->identity());
					$newControlTransactions->setBalance($control->getBalance());
					
					$em->persist($newControlTransactions);

					$em->flush();
					
					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
					
				}

		}
        
		return new ViewModel(array('form'=>$form));
	}

public function addInTransactionAction()
	{
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$addControlForm = new AddControlForm ();
		$form = $addControlForm->buildAddTransactionForm($em,'in');
		$newControlTransactions = new \Application\Entity\ControlTransactions();

		$form->bind($newControlTransactions);

        $request = $this->getRequest();
		if ($request->isPost()) {
				$form->setData($request->getPost());
				if ($form->isValid()) {
				 	$control = $em->find('Application\Entity\Control',$this->params()->fromRoute('id'));
				 	$control->setBalance($control->getBalance()+$newControlTransactions->getIn());
				 	if($control->getQuarntine()>0.0) $control->setQuarntine($control->getBalance());
				 	if($control->getReleased()>0.0) $control->setReleased($control->getBalance());
				 	if($control->getRejected()>0.0) $control->setRejected($control->getBalance());
				 	

					$newControlTransactions->setcontrol($control );
					$newControlTransactions->setUser($this->identity());
					$newControlTransactions->setBalance($control->getBalance());

					$em->persist($newControlTransactions);

					$em->flush();
					
					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
					
				}

		}
        
		return new ViewModel(array('form'=>$form));
	}



}
