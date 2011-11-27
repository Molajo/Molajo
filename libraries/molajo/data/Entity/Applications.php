<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\Applications
 *
 * @ORM\Table(name="applications")
 * @ORM\Entity
 */
class Applications
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string $path
     *
     * @ORM\Column(name="path", type="string", length=2048, nullable=false)
     */
    private $path;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer $home
     *
     * @ORM\Column(name="home", type="integer", nullable=false)
     */
    private $home;

    /**
     * @var text $parameters
     *
     * @ORM\Column(name="parameters", type="text", nullable=true)
     */
    private $parameters;

    /**
     * @var text $customFields
     *
     * @ORM\Column(name="custom_fields", type="text", nullable=true)
     */
    private $customFields;

    /**
     * @var ExtensionInstances
     *
     * @ORM\ManyToMany(targetEntity="ExtensionInstances", mappedBy="application")
     */
    private $extensionInstance;

    /**
     * @var Sites
     *
     * @ORM\ManyToMany(targetEntity="Sites", mappedBy="application")
     */
    private $site;
	
    /**
     * @var Users
     *
     * @ORM\ManyToMany(targetEntity="Users", mappedBy="application")
     */
    private $user;

    public function __construct()
    {
        $this->extensionInstance = new \Doctrine\Common\Collections\ArrayCollection();
		$this->site = new \Doctrine\Common\Collections\ArrayCollection();
		$this->user = new \Doctrine\Common\Collections\ArrayCollection();
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set home
     *
     * @param integer $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * Get home
     *
     * @return integer 
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Set parameters
     *
     * @param text $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Get parameters
     *
     * @return text 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set customFields
     *
     * @param text $customFields
     */
    public function setCustomFields($customFields)
    {
        $this->customFields = $customFields;
    }

    /**
     * Get customFields
     *
     * @return text 
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }

    /**
     * Add extensionInstance
     *
     * @param data\Entity\ExtensionInstances $extensionInstance
     */
    public function addExtensionInstances(\data\Entity\ExtensionInstances $extensionInstance)
    {
        $this->extensionInstance[] = $extensionInstance;
    }

    /**
     * Get extensionInstance
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getExtensionInstance()
    {
        return $this->extensionInstance;
    }

    /**
     * Add site
     *
     * @param data\Entity\Sites $site
     */
    public function addSites(\data\Entity\Sites $site)
    {
        $this->site[] = $site;
    }

    /**
     * Get site
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Add user
     *
     * @param data\Entity\Users $user
     */
    public function addUsers(\data\Entity\Users $user)
    {
        $this->user[] = $user;
    }

    /**
     * Get user
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }
}