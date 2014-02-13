<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * ControlTransactions
 *
 * @ORM\Table(name="control_transactions", indexes={@ORM\Index(name="fk_control_transactions_Control1_idx", columns={"control_id"}), @ORM\Index(name="fk_control_transactions_user1_idx", columns={"user_id"})})
 * @ORM\Entity
 * @Annotation\Name("Control")
 */
class ControlTransactions
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
     * @Annotation\Attributes({"type":"Number","id":"in"})
     * @Annotation\Options({"label":"in"})
     * @ORM\Column(name="`in`", type="decimal", precision=10, scale=2, nullable=true, unique=false)
     */
    private $in;

    /**
     * @var string
     * @Annotation\Attributes({"type":"Number","id":"out"})
     * @Annotation\Options({"label":"out"})
     * @ORM\Column(name="`out`", type="decimal", precision=10, scale=2, nullable=true, unique=false)
     */
    private $out;

    /**
     * @var string
     * @Annotation\Exclude()
     * @ORM\Column(name="ammount", type="decimal", precision=10, scale=2, nullable=true, unique=false)
     */
    private $ammount;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":512}})
     * @Annotation\Attributes({"type":"text","id":"description"})
     * @Annotation\Options({"label":"description"})
     * @ORM\Column(name="description", type="string", length=512, precision=0, scale=0, nullable=true, unique=false)
     */
    private $description;

    /**
     * @var string
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":512}})
     * @Annotation\Attributes({"type":"text","id":"receiptNo"})
     * @Annotation\Options({"label":"receiptNo"})
     * @ORM\Column(name="receipt_no", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $receiptNo;

    /**
     * @var \DateTime
     * @Annotation\Exclude()
     * @ORM\Column(name="date_created", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $dateCreated;

    /**
     * @var \Application\Entity\Control
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Control",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="control_id", referencedColumnName="id", nullable=true)
     * })
	 * @Annotation\Exclude()
	*/
    private $control;

    /**
     * @var \Application\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="CsnUser\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * })
     * @Annotation\Exclude()
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2, nullable=true, unique=false)
     * @Annotation\Exclude()
     */
    public $balance;
    
    
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
     * Set in
     *
     * @param string $in
     * @return ControlTransactions
     */
    public function setIn($in)
    {
        $this->in = $in;

        return $this;
    }

    /**
     * Get in
     *
     * @return string 
     */
    public function getIn()
    {
        return $this->in;
    }

    /**
     * Set out
     *
     * @param string $out
     * @return ControlTransactions
     */
    public function setOut($out)
    {
        $this->out = $out;

        return $this;
    }

    /**
     * Get out
     *
     * @return string 
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * Set ammount
     *
     * @param string $ammount
     * @return ControlTransactions
     */
    public function setAmmount($ammount)
    {
        $this->ammount = $ammount;

        return $this;
    }

    /**
     * Get ammount
     *
     * @return string 
     */
    public function getAmmount()
    {
        return $this->ammount;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ControlTransactions
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set receiptNo
     *
     * @param string $receiptNo
     * @return ControlTransactions
     */
    public function setReceiptNo($receiptNo)
    {
        $this->receiptNo = $receiptNo;

        return $this;
    }

    /**
     * Get receiptNo
     *
     * @return string 
     */
    public function getReceiptNo()
    {
        return $this->receiptNo;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return ControlTransactions
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set control
     *
     * @param \Application\Entity\Control $control
     * @return ControlTransactions
     */
    public function setControl(\Application\Entity\Control $control = null)
    {
        $this->control = $control;

        return $this;
    }

    /**
     * Get control
     *
     * @return \Application\Entity\Control 
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Set user
     *
     * @param \CsnUser\Entity\User $user
     * @return ControlTransactions
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
     * @return ControlTransaction
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }
}
