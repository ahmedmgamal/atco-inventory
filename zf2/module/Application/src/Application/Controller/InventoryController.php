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
use Application\Entity\Unit;
use Zend\Stdlib\Hydrator\ObjectProperty;
use Doctrine\ORM\Query\ResultSetMapping;
use Zend\Mail\Message;

use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use PHPExcel;


class InventoryController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    public function listCustomersAction()
    {

    	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	$addControlForm = new AddControlForm ();
    	
    	if ($this->getRequest()->isPost()) {
    		$form = $addControlForm->buildAddCustomerForm($em);
    		$customer = new \Application\Entity\Customer();
    		$form->bind($customer);
    		$form->setData($this->getRequest()->getPost());
    		if($form->isValid()){
    	
    			$em->persist($customer);
    			$em->flush();
    		}
    			
    	}

		$data = $em->getRepository('Application\Entity\Customer')->findAll();

		$form = $addControlForm->buildAddCustomerForm($em);
		
		
        return new ViewModel(array('customers'=>$data,'form'=>$form));
    }

 public function showCustomerAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $customerId = $this->params()->fromRoute('id');
		$data = $em->getRepository('Application\Entity\Customer')->findById($customerId);


		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('code', 'code');
		$rsm->addScalarResult('batch_no', 'batch_no');
		$rsm->addScalarResult('product_type_id', 'product_type');
		$rsm->addScalarResult('initial_ammount', 'initial_ammount');
		$rsm->addScalarResult('balance', 'balance');
		$rsm->addScalarResult('product_name', 'product_name');
		$rsm->addScalarResult('control_count', 'control_count');
		$rsm->addScalarResult('quarntine', 'quarntine');
		$rsm->addScalarResult('released', 'released');
		$rsm->addScalarResult('rejected', 'rejected');
		$rsm->addScalarResult('expired', 'expired');
		$rsm->addScalarResult('status', 'status');
		
		
 		
 
		
		
		
		if($this->params()->fromPost('product_type')){
			//$sql = "SELECT code ,	product_name ,	batch_no ,	product_type_id, sum(initial_ammount ) initial_ammount, sum(balance) balance,sum(quarntine) quarntine,sum(released) released,sum(rejected) rejected, count(balance) control_count  FROM `control` WHERE `customer_id`=$customerId ";
			$sql = "SELECT code,product_name ,	batch_no ,	product_type_id, sum(initial_ammount ) initial_ammount, sum(balance) balance,sum(if(status='released',balance,0)) released,sum(if(status='quarantine',balance,0)) quarntine ,sum(if(status='rejected',balance,0)) rejected ,sum(if(status='expired',balance,0)) expired,status , count(balance) control_count   FROM `control` WHERE ";
			$sql .= " `customer_id`=$customerId ";
			$sql .= " and product_type_id=".$this->params()->fromPost('product_type'); 
			$sql .= " group by (code )";
			$query = $em->createNativeQuery($sql,$rsm);
			$result = $query->getResult();
		}


		$addControlForm = new AddControlForm ();
		$form = $addControlForm->buildFilterForm($em);
		if ($this->getRequest()->isPost()) {
			$form->setData($this->getRequest()->getPost());
		}
        return new ViewModel(array('customer'=>$data[0],'controls'=>$result,'filter_form'=>$form));
    }
  public function moveToReleasedAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $controlId = $this->params()->fromRoute('id');
		$control = $em->find('Application\Entity\Control',$controlId ); 
        $control->setStatus('released');
		$em->flush();
		$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
	
 
    }
    
    
  public function moveToExpiredAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $controlId = $this->params()->fromRoute('id');
		$control = $em->find('Application\Entity\Control',$controlId ); 
        $control->setStatus('expired');
		$em->flush();
		$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
	
 
    }
    
    
  public function moveToRejectedAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $controlId = $this->params()->fromRoute('id');
        $control = $em->find('Application\Entity\Control',$controlId ); 
        $control->setStatus('rejected');
		
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
		$newControl->setControlNumber("tmep".time());
		if ($this->params()->fromQuery('controlId')){

			
				$control = $em->getRepository('Application\Entity\Control')->findById($this->params()->fromQuery('controlId'));
			
				$newControl->setCode($control[0]->getCode());

				$newControl->setProductName($control[0]->getProductName());
				$newControl->setProductType($control[0]->getProductType());
				
				

			
			}
		
		
		$form->bind($newControl);

        $request = $this->getRequest();
		if ($request->isPost()) {
			$postDate=$request->getPost();
			foreach( array('retestDate','expiryDate','manufacturingDate') as $d ){
				if(($request->getPost($d))){
					$postDate[$d]=date_format(date_create_from_format('d/m/Y',$request->getPost($d)), 'Y-m-d');
				}	
						
			}
				
			//echo date_format($postDate['expiryDate'], 'Y-m-d');
				
				//print_r($postDate);die();
			$form->setData($postDate);
				
			
				if ($form->isValid()) {
				 	$customer = $em->getRepository('Application\Entity\Customer')->findById($this->params()->fromRoute('id'));

					$newControl->setCustomer($customer[0]);
					$newControl->setbalance($newControl->getInitialAmmount());
					$newControl->setStatus('quarantine');

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
					
					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showProduct', 'id' => $newControl->getcode()) );
				}

		}
        
		return new ViewModel(array('form'=>$form));
	}
function csv_to_array($filename='',$customerId,$productType)
{	 

    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;
    
    $handle = fopen($filename, 'r');
    $controlsChunk = array();
    $data = array();
    //ignoring first line
    $headerRow = fgetcsv($handle);
    $ChunksCounter = 1;
    
    $chunckRowsNo = ($productType ==2) ? 17: 48;
    
    
	while (($row = fgetcsv($handle)) !== FALSE)
	{	
		if((($productType ==2||$productType ==10)&& $ChunksCounter==1)  ){	$chunckRowsNo = 18 ; }
		if((($productType ==2||$productType ==10) && $ChunksCounter!=1)  ){	$chunckRowsNo = 17 ; }
		 

 
		
		
		$controlsChunk[] =   $row ;
		
		$endOfChunk = true;
		
 
		
		if(count($controlsChunk) == $chunckRowsNo) 
		//if($endOfChunk )
			{
				echo "\n Chunk number ".$ChunksCounter++.":";
				if($productType ==2||$productType ==10){
				
					$this->extractFinishControls($controlsChunk,$customerId,$productType);
				
				}else{
					$this->extractControls($controlsChunk,$customerId,$productType);
				}
				
				$controlsChunk =array();

			}

	}


	fclose($handle);
    return $data;
}

 

 public function extractControls($controlsChunk,$customerId,$productType)
	{	
		//$this->params()->fromQuery('productType');
		//$customerId = $this->params()->fromQuery('customerId');
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$user = $this->identity();
		if(!$this->identity()){
			$users =  $em->getRepository('CsnUser\Entity\User')->findById(1);
			$user = $users[0];
		}
        $counter =1;
		for ($i=0;$i<700;$i=$i+7){
			if($controlsChunk[0][$i+1] == null) {
				continue;
			}
			else{
				echo $counter++;//."(".$controlsChunk[0][$i+1].")";
				//echo '<hr/>Name:'.$controlsChunk[0][$i+1];
				

				$customer = $em->getRepository('Application\Entity\Customer')->findById($customerId);
				$productType = $em->getRepository('Application\Entity\ProductType')->findById($productType);
				$unit = null;
				$dbunit = $em->getRepository('Application\Entity\Unit')->findOneBy(   array('unit' => $controlsChunk[3][$i+5]));
				
				if ($dbunit){
					//echo "found";
					$unit = $dbunit ;
					
				}else{
					//echo "new unit !!!!";
					$unit =    new \Application\Entity\Unit();
					$unit->setUnit($controlsChunk[3][$i+5]);
					$em->persist($unit);
					$em->flush(); 
				}
					
 				
				$balance  = $controlsChunk[46][$i+2];
				$expiryDate = ($controlsChunk[1][$i+2] && !$controlsChunk[1][$i+2] == 'Reject')?$controlsChunk[1][$i+2] :"1/1/2000";
				//echo $expiryDate;
				
				$productName =  $controlsChunk[0][$i+1];
				
				$batchNo = $controlsChunk[1][$i+1];
				$productCode = $controlsChunk[1][$i+5]? $controlsChunk[1][$i+5] :md5($productName).'-00'.$customerId;
				$controlNo = $controlsChunk[3][$i+1]? $controlsChunk[3][$i+1] :md5($productName.$batchNo.$customerId);
 
				$newControl = new Control();	
				$newControl->setCustomer($customer[0]);
				$newControl->setbalance($controlsChunk[46][$i+2]);
				$newControl->setStatus('quarantine');
				$newControl->setBatchNo($batchNo);
				$newControl->setCode($productCode);
				$newControl->setControlNumber($controlNo);
				try{
					$newControl->setExpiryDate(new \DateTime($expiryDate));
				}catch(\Exception  $e){
					//hand the case of invalid date with format d/m/y 
					//all dates MUST be in format m/d/y
//					echo "!d/m/Y date!($expiryDate)";
					try{
 						$x = date_create_from_format('d/m/Y',$expiryDate);
 						//$x= \DateTime::createFromFormat('d/m/Y',$expiryDate);
 						$newControl->setExpiryDate($x);
						}catch(\Exception  $e){
							echo "nested catchccc";
							$newControl->setExpiryDate(new \DateTime("1/1/2000"));

						}
				} 

				
				
				$newControl->setInitialAmmount($controlsChunk[5][$i]);
				$newControl->setProductName($productName);
				$newControl->setProductType($productType[0]);
				$newControl->setSupplier($controlsChunk[2][$i+1]);
				$newControl->setUnit($unit);
				$newControl->setUser($user);
				$newControl->setDateCreated(new \DateTime());
				$em->persist($newControl);
				$em->flush(); 
		 

				//adding list of transactions 
				$transactionBalance = 0 ;
				for ($j=5;$j<46;$j++){

					
					if($controlsChunk[$j][$i] ||$controlsChunk[$j][$i+1]){
						$newControlTransactions = new \Application\Entity\ControlTransactions();
						$newControlTransactions->setIn($controlsChunk[$j][$i]);
						$newControlTransactions->setOut($controlsChunk[$j][$i+1]);
						$transactionBalance += $controlsChunk[$j][$i] - $controlsChunk[$j][$i+1];
						$newControlTransactions->setbalance($transactionBalance);

						$newControlTransactions->setDescription($controlsChunk[$j][$i+4].' - '.$controlsChunk[$j][$i+5]);
						$newControlTransactions->setReceiptNo($controlsChunk[$j][$i+3] );
						
						$newControlTransactions->setcontrol($newControl);
						$newControlTransactions->setUser($user);
						$newControlTransactions->setDateCreated(new \DateTime());

						 $em->persist($newControlTransactions);
						//echo "<br/>Transaction Added";
						$em->flush(); 
						}
					
					}
		 						
				}
                                $newControl->setBalance($transactionBalance);//break;
                                $em->merge($newControl);
                                $em->flush();

				echo ',';
			}
		}
		
public function extractFinishControls($controlsChunk,$customerId,$productType)
	{	
		//$this->params()->fromQuery('productType');
		//$customerId = $this->params()->fromQuery('customerId');
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$user = $this->identity();
		if(!$this->identity()){
			$users =  $em->getRepository('CsnUser\Entity\User')->findById(1);
			$user = $users[0];
		}
        $counter =1;
		for ($i=0;$i<336;$i=$i+7){
			$productName =  $controlsChunk[0][$i+1];
			$batchNo = $controlsChunk[2][$i+1];

			if($productName == null || $productName=='0' || $batchNo== null || $batchNo==0) {
				//"echo leave bye";
				//die();
				continue;
			}
			else{
				echo $counter++;//."(".$controlsChunk[0][$i+1].")";
				//echo '<hr/>Name:'.$controlsChunk[0][$i+1];
				$unitName ='finish_pack';
				$customer = $em->getRepository('Application\Entity\Customer')->findById($customerId);
				$productType = $em->getRepository('Application\Entity\ProductType')->findById($productType);
				$unit = null;
				$dbunit = $em->getRepository('Application\Entity\Unit')->findOneBy(   array('unit' => $unitName));
				
				if ($dbunit){
					//echo "found";
					$unit = $dbunit ;
					
				}else{
					//echo "new unit !!!!";
					$unit =    new \Application\Entity\Unit();
					$unit->setUnit($unitName);
					$em->persist($unit);
					$em->flush(); 
				}
				
	
				$balance  = $controlsChunk[16][$i+3];
				$expiryDate = $controlsChunk[1][$i+2] ?$controlsChunk[1][$i+3] :"1/1/2000";
				$manufacturingDate = $controlsChunk[1][$i+2] ?$controlsChunk[0][$i+3] :"1/1/2000";
				
				$productName =  $controlsChunk[0][$i+1];
				$productCode = $controlsChunk[1][$i+1]? $controlsChunk[1][$i+1] :md5($productName).'-00'.$customerId;
				$controlNo = md5($productName.$batchNo.$customerId);

 	
				$newControl = new Control();	
				$newControl->setCustomer($customer[0]);
				$newControl->setbalance($balance);
				$newControl->setStatus('quarantine');
				$newControl->setBatchNo($batchNo);
				$newControl->setCode($productCode);
				$newControl->setControlNumber($controlNo);
				$newControl->setManufacturingDate(new \DateTime($manufacturingDate));
				try{
					$newControl->setExpiryDate(new \DateTime($expiryDate));
				}catch(\Exception  $e){
					//hand the case of invalid date with format d/m/y 
					//all dates MUST be in format m/d/y
					echo "!d/m/Y date!($expiryDate)";
					try{
						

						 
						//$x= \DateTime::createFromFormat('d/m/Y',$expiryDate);
						$newControl->setExpiryDate($x);
						}catch(\Exception  $e){
							
							$newControl->setExpiryDate(new \DateTime("1/1/2000"));

						}
				} 

				
				
				$newControl->setInitialAmmount($controlsChunk[4][$i+1]);
				$newControl->setProductName($productName);
				$newControl->setProductType($productType[0]);
				//$newControl->setSupplier($controlsChunk[2][$i+1]);
				$newControl->setUnit($unit);
				$newControl->setUser($user);
				$newControl->setDateCreated(new \DateTime());
				$em->persist($newControl);
				$em->flush(); 
		 

				//adding list of transactions 
				$transactionBalance = 0 ;
				for ($j=4;$j<count($controlsChunk);$j++){

					
					if($controlsChunk[$j][$i] ||$controlsChunk[$j][$i+1]){
						$newControlTransactions = new \Application\Entity\ControlTransactions();
						$newControlTransactions->setIn($controlsChunk[$j][$i+1]);
						$newControlTransactions->setOut($controlsChunk[$j][$i+2]);
						$transactionBalance += $controlsChunk[$j][$i+1] - $controlsChunk[$j][$i+2];
						$newControlTransactions->setbalance($transactionBalance);

						$newControlTransactions->setDescription($controlsChunk[$j][$i+5] );
						$newControlTransactions->setReceiptNo($controlsChunk[$j][$i+4] );
						
						$newControlTransactions->setcontrol($newControl);
						$newControlTransactions->setUser($user);
						$newControlTransactions->setDateCreated(new \DateTime());

						 $em->persist($newControlTransactions);
						//echo "<br/>Transaction Added";
						$em->flush(); 
						}
					
					}
		 		$newControl->setBalance($transactionBalance);//break;				
                                $em->merge($newControl);
				$em->flush();
				}
				
                                echo ',';
			}
		
		
	}
 public function importControlsAction()
	{
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		
		$request = $this->getRequest();
		$filename   = $request->getParam('filename');
		$inputs = explode('_',$filename  );
		print_r($inputs );
		$customer = $em->getRepository('Application\Entity\Customer')->findOneBy(   array('name' => $inputs[0]));
		$productType = $em->getRepository('Application\Entity\ProductType')->findOneBy(   array('name' => $inputs[1]));
		
		echo $productType->getId();
		if (!($customer && $productType)){
			echo "No Customer or wrong type ";
			die();
		}

 	
	
		$inputFileName = "/var/www/atco/zf2/public/csv/".$filename.'.csv';
		$customerId=$customer->getId();
		$productType=$productType->getId();
		
		echo $inputFileName.'\n';
		echo $customerId.'\n$productType';
		echo  $productType.'\n';
		 
		
		$data = $this->csv_to_array($inputFileName,$customerId,$productType);
		rename ($inputFileName , $inputFileName.'.done');
	return ;
		
	}


 public function showControlAction()
    {
       
		$allowedActions = array(
			'quarantine'=>array(array('method'=>'moveToReleased','label'=>'Move To Released'),array('method'=>'moveToRejected','label'=>'Move To Rejected'),),
			'released'=>array(array('method'=>'moveToExpired','label'=>'Move To Expired'),array('method'=>'moveToRejected','label'=>'Move To Rejected')),
			
			
		);
		
		
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$data = $em->getRepository('Application\Entity\Control')->findById($this->params()->fromRoute('id'));

        return new ViewModel(array('control'=>$data[0],'allowedActions'=>$allowedActions));
    }

 public function editRetestDateAction()
    {
       
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		$control = $em->find('Application\Entity\Control',$this->params()->fromRoute('id'));
		$addControlForm = new AddControlForm ();
		$form = $addControlForm->buildEditControlDatesForm($em);
		$form->setHydrator(new  ObjectProperty());
		$form->bind($control );
 
		echo '<br/>';
 
       $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				 //print_r($control->getRetestDate());//die();
				$control->setRetestDate(date_create_from_format('d/m/Y',$form->getData()->getRetestDate())) ;
				
				$control->setExpiryDate(date_create_from_format('d/m/Y',$form->getData()->getExpiryDate())) ;
				$em->flush();
				$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
			
				}

		}
        
		return new ViewModel(array('form'=>$form,'control'=>$control));
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
				 	$newBalance = $control->getBalance()-$newControlTransactions->getOut();
				 	if($newBalance<0){
						$form->get('out')->setMessages(array('Insufficent ammmount '));				 		return new ViewModel(array('form'=>$form));
				 			
				 	}
				 	$control->setBalance($newBalance);

 					$em->flush();

 					$newControlTransactions->setcontrol($control );
					$newControlTransactions->setUser($this->identity());
					$newControlTransactions->setBalance($control->getBalance());
					$em->persist($newControlTransactions);
					$em->flush();

					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
					
				}

		}
        
		return new ViewModel(array('form'=>$form,'controlId'=>$this->params()->fromRoute('id')));
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
			 	

					$newControlTransactions->setcontrol($control );
					$newControlTransactions->setUser($this->identity());
					$newControlTransactions->setBalance($control->getBalance());

					$em->persist($newControlTransactions);

					$em->flush();
					
					$this->redirect()->toRoute('application/default',array('controller'=>'inventory','action'=>'showControl', 'id' => $this->params()->fromRoute('id')) );
					
				}

		}
        
		return new ViewModel(array('form'=>$form,'controlId'=>$this->params()->fromRoute('id')));
	}

 public function sendDailyTransactionsReportAction()
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        //$t = new   Application\Entity\ControlTransaction ;
		$query = $em->createQuery("SELECT t FROM Application\Entity\ControlTransactions t where t.dateCreated > :date");
		//$query->setParameter('date', new \DateTime('today'));
		$hours24Ago = new \DateTime('-24 hour');

		$query->setParameter('date',$hours24Ago);
		$transactions =  $query->getResult();

		$table = $this->generateTransactionsTable($transactions );
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
		

//		if(!$this->params()->fromQuery('viewOnly')==1){
		$transport->send($message);
	//	}

		//echo "message sent ";
		$viewModel = new ViewModel(array('table'=>$table));
	//	$viewModel->setTemplate('application/inventory/send-daily-transactions-report');
		
	//	$renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
	//	$htmlString = $renderer->render($viewModel);

		
		return $viewModel;

     }
	
	public function dailyTransactionsReportAction()
     {
     	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
     	//$t = new   Application\Entity\ControlTransaction ;
     	$query = $em->createQuery("SELECT t,c FROM Application\Entity\ControlTransactions t  join t.control c  where t.dateCreated > :date");

     	     	
     	//$query->setParameter('date', new \DateTime('today'));
     	$hours24Ago = new \DateTime('-24 hour');
     	
     	$query->setParameter('date',$hours24Ago);
      	
     	$transactions =  $query->getResult();
     	$table = $this->generateTransactionsTable($transactions );
     	
     	//$viewModel = new ViewModel(array('table'=>$transactions));
     	$viewModel = new ViewModel(array('table'=>$table));
 
     	//	$viewModel->setTemplate('application/inventory/send-daily-transactions-report');
     	
     	//	$renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
     	//	$htmlString = $renderer->render($viewModel);
     	
     	
     	return $viewModel;
     }
public function expiredControlsReportAction()
     {
     	
     	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
     	$query = $em->createQuery("SELECT  c,customer,pt FROM Application\Entity\Control  c  join c.customer customer join c.productType pt where c.balance>0.0 and  c.expiryDate < :date");
     	$today = new \DateTime('today');
     	$query->setParameter('date',$today);
     	$controls =  $query->getResult();
     	$viewModel = new ViewModel(array('controls'=>$controls));
     	return $viewModel ;
     }
     public function expiredControlsIn6MonthsReportAction()
     {
     
     	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
     	$query = $em->createQuery("SELECT  c,customer,pt FROM Application\Entity\Control  c  join c.customer customer join c.productType pt where c.balance>0.0 and  c.expiryDate > :todaydate and c.expiryDate < :after6months order by  c.expiryDate  ");
     	$today = new \DateTime('today');
     	
     	$query->setParameter('todaydate',$today);
     	$query->setParameter('after6months',new \DateTime('+6 Month'));
     	$controls =  $query->getResult();
     	$viewModel = new ViewModel(array('controls'=>$controls));
     	return $viewModel ;
     }
     
      
      
     public function generateTransactionsTable($transactions){
     	
     	
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
     	
     	
     	return $table;
     	
     }
     public function customerControlsReportAction()
     {
     	$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
     
     	$addControlForm = new AddControlForm ();
     	$form = $addControlForm->buildCustomerControlsReportForm($em);
     	$newControl = new Control();
     
     	$request = $this->getRequest();
     	if ($request->isPost()) {
     		$form->setData($request->getPost());
     		 
     			$filterData = array();
     			$requestData =$request->getPost();

     			if($requestData['customer'])		{ $filterData['customer'] =$requestData['customer'];}
     	
     			//if($requestData['showEmptyStock'])	{ $filterData['balance'] =" > 0.00";}
     			$dql = "SELECT  c,customer,pt FROM Application\Entity\Control  c  join c.customer customer join c.productType pt where c.balance > 0 ";
     			
     			if($requestData['productType'])		{ $dql .= " and c.productType=".$requestData['productType'] ;}
     			if($requestData['customer'])		{ $dql .= " and c.customer=".$requestData['customer'] ;}
     	
     			
     			$query = $em->createQuery($dql);
     			
     	
     		 
       			$data = $query->getResult(); 
       			//$em->getRepository('Application\Entity\Control')->findBy($filterData);
       			
     			return new ViewModel(array('controls'=>$data,'filter_form'=>$form  ));
 
     	}
     
     	return new ViewModel(array('filter_form'=>$form));
     }
}
