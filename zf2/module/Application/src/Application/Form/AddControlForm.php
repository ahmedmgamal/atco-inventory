<?php
namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Filter\FilterChain;
use Zend\Filter\FilterPluginManager;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element;
use Zend\Form\Element\Csrf;
use Zend\Form\Fieldset;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Validator\StringLength;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\ValidatorChain;
use Zend\Form\Annotation;
use Zend\Form\Form;

use Zend\Form\Annotation\AnnotationBuilder;
use Application\Entity\Control;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class AddControlForm 
{
	function build($em)
	{
		$builder = new AnnotationBuilder();
        $form    = $builder->createForm('Application\Entity\Control');

		$hydrator = new DoctrineHydrator($em);
		$form->setHydrator($hydrator);
		
      $form->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => 'productType',
			'options' => array(
				'object_manager' => $em,
				'target_class'   => '\Application\Entity\ProductType',
				'property'       => 'id',
				'label_generator' => function($targetEntity) {
					return $targetEntity->getId() . ' - ' . $targetEntity->getName();
				},

			),
		));
		$form->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => 'unit',
			'options' => array(
				'object_manager' => $em,
				'target_class'   => '\Application\Entity\Unit',
				'property'       => 'id',
				'label_generator' => function($targetEntity) {
					return $targetEntity->getId() . ' - ' . $targetEntity->getUnit();
				},

			),
		));
		
        
        
		$form->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Save',
				'id' => 'submitbutton',
			),
		));

        
        return $form;
	}


function buildAddTransactionForm($em,$type='out')
	{
		$builder = new AnnotationBuilder();
        $form    = $builder->createForm('Application\Entity\ControlTransactions');

		$hydrator = new DoctrineHydrator($em);
		$form->setHydrator($hydrator);
		
		if($type=='out'){
			$form->remove('in');	
		}else{
			$form->remove('out');	
			
		}
			
        
        
		$form->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Save',
				'id' => 'submitbutton',
			),
		));

        
        return $form;
	}


function buildFilterForm($em )
	{
         $form    = new Form();

		$form->add(array(
			'type' => 'DoctrineModule\Form\Element\ObjectSelect',
			'name' => 'product_type',
			'options' => array(
				'object_manager' => $em,
				'target_class'   => '\Application\Entity\ProductType',
				'property'       => 'id',
				'label' => 'Filter By',
				'label_generator' => function($targetEntity) {
					return $targetEntity->getId() . ' - ' . $targetEntity->getName();
				},

			),
		));
		
			
        
        
		$form->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Filter',
				'id' => 'submitbutton',
			),

		));

        
        return $form;
	}



function buildEditControlDatesForm($em )
	{
         $form    = new Form();
 
		$form->add(array(
			'type' => 'text',
			'name' => 'controlNumber',
			'id' => 'control_number',
			'attributes'=>array("id"=>"control_number"),			

			'options' => array(
 				'label' => 'controlNumber',

			),
		));
 
		$form->add(array(
			'type' => 'Date',
			'name' => 'retestDate',

			'attributes'=>array("id"=>"retest_date"  ),			
			'options' => array(
 				'label' => 'Retest Date(d/m/Y)',
 				'format' => 'd/m/Y',


			),
		));
  
		$form->add(array(
			'type' => 'Date',
			'name' => 'expiryDate',
			'id' => 'expiry_date',
			'attributes'=>array("id"=>"expiry_date"),
			'options' => array(
 				'label' => 'expiryDate(d/m/Y)',
 				'format' => 'd/m/Y'

			),
		));
 
		$form->add(array(
			'type' => 'text',
			'name' => 'location',
			'id' => 'location',
			'attributes'=>array("id"=>"location"),			
			
			'options' => array(
 				'label' => 'Location',
 				

			),
		));
 
		
       $form->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Save',
				'id' => 'submitbutton',
			),

		));
	
        
        


        
        return $form;
	}

	function buildCustomerControlsReportForm($em )
	{
		$form    = new Form("customerInventory");
	
		$form->setAttributes(array("class"=>"form-horizontal","role"=>"form"));
		$form->add(array(
				'type' => 'DoctrineModule\Form\Element\ObjectSelect',
				'name' => 'customer',
				'options' => array(
						'id'=>'customer',
						'object_manager' => $em,
						'target_class'   => '\Application\Entity\Customer',
						'property'       => 'id',
						'label' => 'Filter  By Customer',
						'label_generator' => function($targetEntity) {
							return $targetEntity->getId() . ' - ' . $targetEntity->getName();
						},
		
				),
		));
		$form->add(array(
				'type' => 'DoctrineModule\Form\Element\ObjectSelect',
				'name' => 'productType',
				'options' => array(
						'id' => 'product_type',
						'display_empty_item' => true,
						
						'object_manager' => $em,
						'target_class'   => '\Application\Entity\ProductType',
						'property'       => 'id',
						'label' => 'Filter By Product Type',
						'label_generator' => function($targetEntity) {
							return $targetEntity->getId() . ' - ' . $targetEntity->getName();
						},
		
				),
		));
		
 /*
		$form->add(array(
				'type' => 'text',
				'name' => 'productName',
				'id' => 'product_name',
				'attributes'=>array("id"=>"product_name"),
		
				'options' => array(
						'label' => 'productName',
		
				),
		));

		$form->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => 'showEmptyStock',
				'id' => 'showEmptyStock',
				'attributes'=>array("id"=>"showEmptyStock"),
		
				'options' => array(
						'label' => 'showEmptyStock',
		
				),
		));
		
				*/
 

	
		$form->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type' => 'submit',
						'value' => 'Filter',
						'id' => 'submitbutton',
				),
	
		));
	
	
	
	
	
	
		return $form;
	}
	
	
	function buildAddCustomerForm($em)
	{
		$builder = new AnnotationBuilder();
		$form    = $builder->createForm('Application\Entity\Customer');
	
		$hydrator = new DoctrineHydrator($em);
		$form->setHydrator($hydrator);
		$form->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type' => 'submit',
						'value' => 'Add Customer',
						'id' => 'submitbutton',
				),
		));
	
	
		return $form;
	}
	
	

	 

}
 
