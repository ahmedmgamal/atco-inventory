<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Privilege
 *
 * @ORM\Table(name="privilege", indexes={@ORM\Index(name="IDX_87209A8789329D25", columns={"resource_id"}), @ORM\Index(name="IDX_87209A87D60322AC", columns={"role_id"})})
 * @ORM\Entity
 */
class Privilege
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permission_allow", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $permissionAllow;

    /**
     * @var \Application\Entity\Resource
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Resource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resource_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $resource;

    /**
     * @var \Application\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $role;


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
     * @return Privilege
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
     * Set permissionAllow
     *
     * @param boolean $permissionAllow
     * @return Privilege
     */
    public function setPermissionAllow($permissionAllow)
    {
        $this->permissionAllow = $permissionAllow;

        return $this;
    }

    /**
     * Get permissionAllow
     *
     * @return boolean 
     */
    public function getPermissionAllow()
    {
        return $this->permissionAllow;
    }

    /**
     * Set resource
     *
     * @param \Application\Entity\Resource $resource
     * @return Privilege
     */
    public function setResource(\Application\Entity\Resource $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \Application\Entity\Resource 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set role
     *
     * @param \Application\Entity\Role $role
     * @return Privilege
     */
    public function setRole(\Application\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Application\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }
}
