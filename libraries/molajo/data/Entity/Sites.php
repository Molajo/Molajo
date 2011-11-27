<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\Sites
 *
 * @ORM\Table(name="sites")
 * @ORM\Entity
 */
class Sites
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
     * @var string $baseUrl
     *
     * @ORM\Column(name="base_url", type="string", length=2048, nullable=false)
     */
    private $baseUrl;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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
     * @var Applications
     *
     * @ORM\ManyToMany(targetEntity="Applications", inversedBy="site")
     * @ORM\JoinTable(name="site_applications",
     *   joinColumns={
     *     @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   }
     * )
     */
    private $application;
	
    /**
     * @var ExtensionInstances
     *
     * @ORM\ManyToMany(targetEntity="ExtensionInstances", mappedBy="site")
     */
    private $extensionInstance;
	

    public function __construct()
    {
        $this->application = new \Doctrine\Common\Collections\ArrayCollection();
		$this->extensionInstance = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set baseUrl
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get baseUrl
     *
     * @return string 
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
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
     * Add application
     *
     * @param data\Entity\Applications $application
     */
    public function addApplications(\data\Entity\Applications $application)
    {
        $this->application[] = $application;
    }

    /**
     * Get application
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getApplication()
    {
        return $this->application;
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
}