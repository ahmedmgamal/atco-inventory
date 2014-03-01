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
			'type' => 'date',
			'name' => 'retestDate',
			'id' => 'retest_date',
			'attributes'=>array("id"=>"retest_date"),			
			'options' => array(
 				'label' => 'Retest Date(m/d/Y)',
 				'format' => 'm/d/Y'

			),
		));
 
		$form->add(array(
			'type' => 'date',
			'name' => 'expiryDate',
			'id' => 'expiry_date',
			'attributes'=>array("id"=>"expiry_date"),
			'options' => array(
 				'label' => 'expiryDate(m/d/Y)',
 				'format' => 'm/d/Y'

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






}
 
