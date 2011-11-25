<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users
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
     * @var string $username
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string $firstName
     *
     * @ORM\Column(name="first_name", type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @var string $lastName
     *
     * @ORM\Column(name="last_name", type="string", length=150, nullable=true)
     */
    private $lastName;

    /**
     * @var text $contentText
     *
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    private $contentText;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=100, nullable=false)
     */
    private $password;

    /**
     * @var boolean $block
     *
     * @ORM\Column(name="block", type="boolean", nullable=false)
     */
    private $block;

    /**
     * @var string $activation
     *
     * @ORM\Column(name="activation", type="string", length=100, nullable=false)
     */
    private $activation;

    /**
     * @var boolean $sendEmail
     *
     * @ORM\Column(name="send_email", type="boolean", nullable=false)
     */
    private $sendEmail;

    /**
     * @var datetime $registerDatetime
     *
     * @ORM\Column(name="register_datetime", type="datetime", nullable=false)
     */
    private $registerDatetime;

    /**
     * @var datetime $lastVisitDatetime
     *
     * @ORM\Column(name="last_visit_datetime", type="datetime", nullable=false)
     */
    private $lastVisitDatetime;

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
     * @ORM\ManyToMany(targetEntity="Applications", inversedBy="user")
     * @ORM\JoinTable(name="user_applications",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   }
     * )
     */
    private $application;
	
    /**
     * @var  Categories
     *
     * @ORM\ManyToMany(targetEntity=" Categories", inversedBy="user")
     * @ORM\JoinTable(name="user_groups",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *   }
     * )
     */
    private $group;
	
    /**
     * @var ViewGroups
     *
     * @ORM\ManyToMany(targetEntity="ViewGroups", inversedBy="user")
     * @ORM\JoinTable(name="user_view_groups",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="view_group_id", referencedColumnName="id")
     *   }
     * )
     */
    private $viewGroup;

    public function __construct()
    {
        $this->application = new \Doctrine\Common\Collections\ArrayCollection();
		$this->group = new \Doctrine\Common\Collections\ArrayCollection();
		$this->viewGroup = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set contentText
     *
     * @param text $contentText
     */
    public function setContentText($contentText)
    {
        $this->contentText = $contentText;
    }

    /**
     * Get contentText
     *
     * @return text 
     */
    public function getContentText()
    {
        return $this->contentText;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set block
     *
     * @param boolean $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     * Get block
     *
     * @return boolean 
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set activation
     *
     * @param string $activation
     */
    public function setActivation($activation)
    {
        $this->activation = $activation;
    }

    /**
     * Get activation
     *
     * @return string 
     */
    public function getActivation()
    {
        return $this->activation;
    }

    /**
     * Set sendEmail
     *
     * @param boolean $sendEmail
     */
    public function setSendEmail($sendEmail)
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * Get sendEmail
     *
     * @return boolean 
     */
    public function getSendEmail()
    {
        return $this->sendEmail;
    }

    /**
     * Set registerDatetime
     *
     * @param datetime $registerDatetime
     */
    public function setRegisterDatetime($registerDatetime)
    {
        $this->registerDatetime = $registerDatetime;
    }

    /**
     * Get registerDatetime
     *
     * @return datetime 
     */
    public function getRegisterDatetime()
    {
        return $this->registerDatetime;
    }

    /**
     * Set lastVisitDatetime
     *
     * @param datetime $lastVisitDatetime
     */
    public function setLastVisitDatetime($lastVisitDatetime)
    {
        $this->lastVisitDatetime = $lastVisitDatetime;
    }

    /**
     * Get lastVisitDatetime
     *
     * @return datetime 
     */
    public function getLastVisitDatetime()
    {
        return $this->lastVisitDatetime;
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
     * Add group
     *
     * @param data\Entity\Categories $group
     */
    public function addCategories(\data\Entity\Categories $group)
    {
        $this->group[] = $group;
    }

    /**
     * Get group
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add viewGroup
     *
     * @param data\Entity\ViewGroups $viewGroup
     */
    public function addViewGroups(\data\Entity\ViewGroups $viewGroup)
    {
        $this->viewGroup[] = $viewGroup;
    }

    /**
     * Get viewGroup
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getViewGroup()
    {
        return $this->viewGroup;
    }
}