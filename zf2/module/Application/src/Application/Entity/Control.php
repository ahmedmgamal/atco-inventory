<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * Control
 *
 * @ORM\Table(name="control", uniqueConstraints={@ORM\UniqueConstraint(name="control_number", columns={"control_number"})}, indexes={@ORM\Index(name="fk_Control_Customer1_idx", columns={"customer_id"}), @ORM\Index(name="fk_Control_user1_idx", columns={"user_id"}), @ORM\Index(name="fk_control_Product_Type1_idx", columns={"Product_Type_id"}), @ORM\Index(name="fk_control_unit1_idx", columns={"unit_id"})})
 * @ORM\Entity
 * @Annotation\Name("Control")
 * @Annotation\Options({"label":"Control"})

 */
class Control
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    public $id;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text","id":"controlNumber"})
     * @Annotation\Options({"label":"controlNumber"})
     * @Annotation\Required(true)
     * @ORM\Column(name="control_number", type="string", length=45, precision=0, scale=0, nullable=false, unique=true)
     */
    public $controlNumber;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text","id":"code"})
     * @Annotation\Options({"label":"Code"})
     * @Annotation\Required(true)
     * @ORM\Column(name="code", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    public $code;

 

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text","id":"productName"})
     * @Annotation\Options({"label":"productName"})
     * @Annotation\Required(true)
     * @ORM\Column(name="product_name", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    public $productName;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text","id":"batchNo"})
     * @Annotation\Options({"label":"batchNo"})
     * @Annotation\Required(true)
     * @ORM\Column(name="product_name", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     * @ORM\Column(name="batch_no", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    public $batchNo;

 

    /**
     * @var \DateTime
     * @Annotation\Attributes({"type":"Date","id":"expiry_date"})
     * @Annotation\Options({"label":"expiry_date (m/d/y)"})
     * @Annotation\Required(true)
     * @ORM\Column(name="expiry_date", type="date", precision=0, scale=0, nullable=true, unique=false)
     */
    public $expiryDate;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":25}})
     * @Annotation\Attributes({"type":"text","id":"supplier"})
     * @Annotation\Options({"label":"supplier"})

     * @ORM\Column(name="supplier", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    public $supplier;

    /**
     * @var string
 
     * @Annotation\Attributes({"type":"Number","id":"initial_ammount"})
     * @Annotation\Options({"label":"initial_ammount"})
     * @Annotation\Required(true)
     * @ORM\Column(name="initial_ammount", type="decimal", precision=10, scale=2, nullable=true, unique=false)
     */
    public $initialAmmount;


    /**
     * @var string
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2, nullable=true, unique=false)
     * @Annotation\Exclude()
     */
    public $balance;


    /**
     * @var string
     * @ORM\Column(name="date_created", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     * @Annotation\Exclude()

     */
    public $dateCreated;

    /**
     * @var \DateTime
     * @ORM\Column(name="retest_date", type="date", precision=0, scale=0, nullable=true, unique=false)
     * @Annotation\Attributes({"type":"Date","id":"retest_date"})
     * @Annotation\Options({"label":"Retest Date (m/d/y)"})
     */
    public $retestDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="manufacturing_date", type="date", precision=0, scale=0, nullable=true, unique=false)
     * @Annotation\Attributes({"type":"Date","id":"manufacturingDate"})
     * @Annotation\Options({"label":"manufacturingDate (m/d/y)"})
     */
    public $manufacturingDate;

    /**
     * @var \Application\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     * })
     * @Annotation\Exclude()

     */
    public $customer;

    /**
     * @var \Application\Entity\ProductType
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\ProductType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Product_Type_id", referencedColumnName="id", nullable=true)
     * })
     * @Annotation\Exclude()
     * @Annotation\Exclude()

     */
    public $productType;

 

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     * @Annotation\Exclude()
     */
    public $user;
    
    /**
     * @var \Application\Entity\Unit
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Unit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unit_id", referencedColumnName="id", nullable=true)
     * })
     * @Annotation\Exclude()
     */
    public $unit;
    
    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=45, nullable=true, unique=false)
     * @Annotation\Exclude()
     */
    public $status;
    
    

    /**
     * @ORM\OneToMany(targetEntity="ControlTransactions", mappedBy="control",cascade={"persist"})
     * @Annotation\Exclude()
     */
    public $controlTransactionsList;
   
   	/**
	 * Constructor
	 */
    public function __construct()
    {
        $this->controlTransactionsList = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set controlNumber
     *
     * @param string $controlNumber
     * @return Control
     */
    public function setControlNumber($controlNumber)
    {
        $this->controlNumber = $controlNumber;

        return $this;
    }

    /**
     * Get controlNumber
     *
     * @return string 
     */
    public function getControlNumber()
    {
        return $this->controlNumber;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Control
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
     * Set productName
     *
     * @param string $productName
     * @return Control
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set batchNo
     *
     * @param string $batchNo
     * @return Control
     */
    public function setBatchNo($batchNo)
    {
        $this->batchNo = $batchNo;

        return $this;
    }

    /**
     * Get batchNo
     *
     * @return string 
     */
    public function getBatchNo()
    {
        return $this->batchNo;
    }

   /**
     * Set expiryDate
     *
     * @param \DateTime $expiryDate
     * @return Control
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get expiryDate
     *
     * @return \DateTime 
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set supplier
     *
     * @param string $supplier
     * @return Control
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return string 
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

 

 

    /**
     * Set initialAmmount
     *
     * @param string $initialAmmount
     * @return Control
     */
    public function setInitialAmmount($initialAmmount)
    {
        $this->initialAmmount = $initialAmmount;

        return $this;
    }

    /**
     * Get balance
     *
     * @return decimal 
     */
    public function getBalance()
    {
        return $this->balance;
    }
    /**
     * Set balance
     *
     * @param string $balance
     * @return Control
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get initialAmmount
     *
     * @return string 
     */
    public function getInitialAmmount()
    {
        return $this->initialAmmount;
    }

    /**
     * Set dateCreated
     *
     * @param string $dateCreated
     * @return Control
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return string 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set customer
     *
     * @param \Application\Entity\Customer $customer
     * @return Control
     */
    public function setCustomer(\Application\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Application\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set productType
     *
     * @param \Application\Entity\ProductType $productType
     * @return Control
     */
    public function setProductType(\Application\Entity\ProductType $productType = null)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * Get productType 
     *
     * @return \Application\Entity\ProductType 
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set unit
     *
     * @param \Application\Entity\Unit $unit
     * @return Control
     */
    public function setUnit(\Application\Entity\Unit $unit = null)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return \Application\Entity\Unit 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set user
     *
     * @param \CsnUser\Entity\User $user
     * @return Control
     */
    public function setUser(\CsnUser\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \CsnUser\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
            /**
     * Add controlTransaction
     *
     * @param \Application\Entity\ControlTransactions.php $controlTransaction
     * @return control
     */
    public function addControlTransaction(\Application\Entity\ControlTransactions $controlTransactions)
    {
        $this->controlTransactionsList[] = $controlTransactions;

        return $this;
    }

    /**
     * Remove controlTransactions
     *
     * @param \Application\Entity\ControlTransactions $controlTransactions
     */
    public function removeControlTransaction(\Application\Entity\ControlTransactions $controlTransaction)
    {
        $this->controlTransactionsList->removeElement($controlTransactions);
    }

    /**
     * Get controlTransactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControlTransactionsList()
    {
        return $this->controlTransactionsList;

    }
    /**
     * Set status
     *
     * @param string $status
     * @return Control
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
    
        /**
     * Set retest Date
     *
     * @param \DateTime $retestDate
     * @return Control
     */
    public function setRetestDate($retestDate)
    {
        $this->retestDate = $retestDate;

        return $this;
    }

    /**
     * Get retestDate
     *
     * @return \DateTime 
     */
    public function getRetestDate()
    {
        return $this->retestDate;
    }
     /**
     * Set ManufacturingDate

     * @param \DateTime $manufacturingDate
     * @return Control
     */
    public function setManufacturingDate($manufacturingDate)
    {
        $this->manufacturingDate = $manufacturingDate;

        return $this;
    }

    /**
     * Get manufacturingDate
     *
     * @return \DateTime 
     */
    public function getManufacturingDate()
    {
        return $this->manufacturingDate;
    }
    
}
