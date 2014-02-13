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
use Zend\Mail\Message;

use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;



class InventoryController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    public function listCustomersAction()
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
		$rsm->addScalarResult('batch_no', 'batch_no');
		$rsm->addScalarResult('product_type', 'product_type');
		$rsm->addScalarResult('initial_ammount', 'initial_ammount');
		$rsm->addScalarResult('balance', 'balance');
		$rsm->addScalarResult('product_name', 'product_name');
		$rsm->addScalarResult('control_count', 'control_count');
		$rsm->addScalarResult('quarntine', 'quarntine');
		$rsm->addScalarResult('released', 'released');
		$rsm->addScalarResult('rejected', 'rejected');

		
 
		
		
		$sql = "SELECT code ,	product_name ,	batch_no ,	product_type,sum(initial_ammount ) initial_ammount, sum(balance) balance,sum(quarntine) quarntine,sum(released) released,sum(rejected) rejected, count(balance) control_count  FROM `control` WHERE `customer_id`=$customerId ";
		if($this->params()->fromPost('product_type')){
			$sql .=" and product_type=".$this->params()->fromPost('product_type'); 
			}

		$sql .= " group by (code )";
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
					$newControl->setQuarntine($newControl->getInitialAmmount());

					$newControl->setUser($this->identity());
					$newControl->setDateCreated(new \DateTime());
					
					$em->persist($newControl);
					$em->flush(); 

					$newControlTransactions = new \Application\Entity\ControlTransactions();
					$newControlTransactions->setIn($newControl->getInitialAmmount());
					$newControlTransactions->setbalance($newControl->getInitialAmmount());

					$newControlTransactions->setDescription('initial input ');
					$newControlTransactions->setcontrol($newControl);
					$newControlTransactions->setUser($this->identity());
					$newControl->setDateCreated(new \DateTime());

					$em->persist($newControlTransactions);

					$em->flush();
					
					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showCustomer', 'id' => $this->params()->fromRoute('id')) );
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

 public function sendDailyTransactionsReportAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        //$t = new   Application\Entity\ControlTransaction ;
		
		$query = $em->createQuery("SELECT t FROM Application\Entity\ControlTransactions t where t.dateCreated > :date");
		$query->setParameter('date', new \DateTime('today'));
		
		$transactions =  $query->getResult();
		
		
		$style ='<style>table {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-collapse: collapse;
    border-color: #87CEFA #F0F8FF;
    border-image: none;
    border-left: 3px solid #F0F8FF;
    border-right: 3px solid #F0F8FF;
    border-style: double solid;
    border-width: 5px 3px;
    font: 75%/150% Verdana,Arial,Helvetica,sans-serif;
}
th {
    color: #004477;
    font: small-caps bold 1.1em/120% Verdana,Arial,Helvetica,sans-serif;
    letter-spacing: -1px;
    padding: 5px 10px;
    text-align: left;
}
thead th {
    background: none repeat scroll 0 0 #F0F8FF;
    border: 1px solid #87CEFA;
    white-space: nowrap;
}
tbody td, tbody th {
    background: none repeat scroll 0 0 #FFFFFF;
    color: #000000;
    padding: 5px 10px;
}
tbody th {
    color: #004477;
    font-size: 1em;
    font-variant: normal;
    font-weight: normal;
}
tbody tr.odd {
    border: 1px solid #87CEFA;
}
tbody tr.odd td, tbody tr.odd th {
    background: none repeat scroll 0 0 #F0F8FF;
}
tfoot td, tfoot th {
    border: medium none;
    padding-top: 10px;
}
caption {
    color: #004477;
    font-family: "Georgia",serif;
    font-size: 150%;
    font-style: italic;
    letter-spacing: 5px;
    padding: 10px 0;
    text-align: left;
    text-indent: 2em;
    text-transform: uppercase;
}
table a:link {
    color: #DC143C;
}
table th a:link {
    color: #004477;
    text-decoration: none;
}
table a:visited {
    color: #003366;
    text-decoration: line-through;
}
table a:hover {
    color: #000000;
    text-decoration: none;
}
table a:active {
    color: #000000;
}</style>';
		
		
		$table = $style;
		$table .=  "<table> ";
		$table .=  "<tr> ";
		$table .=  "<th> Control Number </th> ";
		$table .=  "<th> Product Code</th> ";
		$table .=  "<th> Product Name </th> ";
		$table .=  "<th> In ammount </th> ";
		$table .=  "<th> Out ammount </th> ";
		$table .=  "<th> Balance </th> ";
		$table .=  "<th> Description </th> ";
		$table .=  "<th> Receipt No </th> ";
		$table .=  "<th> User </th> ";
		$table .=  "<th> Date Time </th> ";
		
		

		$table .=  "</tr> ";
		
		foreach($transactions as $trans){ 
			$table .=  "<tr> ";
		
			$table .=  "<td> ".$trans->getControl()->getControlNumber() ."</td>";
			$table .=  "<td> ".$trans->getControl()->getCode() ."</td>";
			$table .=  "<td> ".$trans->getControl()->getProductName() ."</td>";
			$table .=  "<td> ".$trans->getIn() ."</td>";
			$table .=  "<td> ".$trans->getOut() ."</td>";
			$table .=  "<td> ".$trans->getBalance() ."</td>";
			$table .=  "<td> ".$trans->getDescription() ."</td>";
			$table .=  "<td> ".$trans->getReceiptNo() ."</td>";
			$table .=  "<td> ".$trans->getUser()->getUsername()  ."</td>";
			$table .=  "<td> ".$trans->getDateCreated()->format('d/m/Y H:i') ."</td>";
 
			
		$table .=  "</tr> ";
		}  
		$table .=  "</table> ";
        
        

		$transport = $this->getServiceLocator()->get('mail.transport');
		$title = "<h2>".'Atco inventory : Transactions log for '. date('l \t\h\e jS')."</h2> " ;
		$messagContent = $title ."<hr />".$table ;
		
 

		$html = new MimePart($messagContent);
		$html->type = "text/html";

 
		$body = new MimeMessage();
		$body->setParts(array( $html ));

		$message = new Message();

	 	$admins = $em->getRepository('CsnUser\Entity\User')->findBy(array('role'=>3));

		$message->addTo('ahmed.gamal@ahmedgamal.info');
		foreach($admins as $admin){
				$message->addTo($admin->getEmail());
			}


		$message->addFrom('smpt@ahmedgamal.info')
						->setSubject('Atco inventory : Transactions log for '. date('l \t\h\e jS'))
						->setBody($body);
		$transport->send($message);
		return new ViewModel(array('table'=>$table));

     }



}
