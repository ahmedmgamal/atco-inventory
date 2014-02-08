<?php
namespace Application\Form;

use Zend\Form\Form;
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


}
 
