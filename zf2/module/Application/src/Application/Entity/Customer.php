<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;


/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity
 * @Annotation\Name("Customer")
 * @Annotation\Options({"label":"Customer"})

 */
class Customer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":250}})
     * @Annotation\Attributes({"type":"text","id":"name"})
     * @Annotation\Options({"label":"New Customer Name"})
     * @Annotation\Required(true)
     * @ORM\Column(name="name", type="string", length=45, precision=0, scale=0, nullable=false, unique=false)
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(name="address", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     * @Annotation\Exclude()
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="code", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     * @Annotation\Exclude()
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     * @Annotation\Exclude()
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Control", mappedBy="customer")
     * @Annotation\Exclude()
     */
    public $controlsList;
    
    
    
	/**
	 * Constructor
	 */
    public function __construct()
    {
        $this->controlsList = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Customer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Customer
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
        /**
     * Add control
     *
     * @param \Application\Entity\Control $control
     * @return control
     */
    public function addControl(\Application\Entity\Control $control)
    {
        $this->controlsList[] = $control;

        return $this;
    }

    /**
     * Remove control
     *
     * @param \Application\Entity\Control $control
     */
    public function removeControl(\Application\Entity\Control $control)
    {
        $this->controlsList->removeElement($control);
    }

    /**
     * Get groupRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControlsList()
    {
        return $this->controlsList;

    }
}
