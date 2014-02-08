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
        $em = $this->getServiceLocator()
            ->get('doctrine.entitymanager.orm_default');
		$data = $em->getRepository('Application\Entity\Customer')->findById($this->params()->fromRoute('id'));

        return new ViewModel(array('customer'=>$data[0]));
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
					$em->persist($newControl);
					$newControlTransactions = new \Application\Entity\ControlTransactions();
					$newControlTransactions->setIn($newControl->getInitialAmmount());
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

}
