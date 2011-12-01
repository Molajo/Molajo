<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\Assets
 *
 * @ORM\Table(name="assets")
 * @ORM\Entity
 */
class Assets
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
     * @var integer $sourceId
     *
     * @ORM\Column(name="source_id", type="integer", nullable=false)
     */
    private $sourceId;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string $sefRequest
     *
     * @ORM\Column(name="sef_request", type="string", length=2048, nullable=false)
     */
    private $sefRequest;

    /**
     * @var string $request
     *
     * @ORM\Column(name="request", type="string", length=2048, nullable=false)
     */
    private $request;

    /**
     * @var integer $primaryCategoryId
     *
     * @ORM\Column(name="primary_category_id", type="integer", nullable=false)
     */
    private $primaryCategoryId;

    /**
     * @var integer $templateId
     *
     * @ORM\Column(name="template_id", type="integer", nullable=false)
     */
    private $templateId;

    /**
     * @var string $language
     *
     * @ORM\Column(name="language", type="string", length=7, nullable=false)
     */
    private $language;

    /**
     * @var integer $translationOfId
     *
     * @ORM\Column(name="translation_of_id", type="integer", nullable=false)
     */
    private $translationOfId;

    /**
     * @var integer $redirectToId
     *
     * @ORM\Column(name="redirect_to_id", type="integer", nullable=false)
     */
    private $redirectToId;

    /**
     * @var integer $viewGroupId
     *
     * @ORM\Column(name="view_group_id", type="integer", nullable=false)
     */
    private $viewGroupId;

    /**
     * @var Categories
     *
     * @ORM\ManyToMany(targetEntity="Categories", inversedBy="asset")
     * @ORM\JoinTable(name="asset_categories",
     *   joinColumns={
     *     @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *   }
     * )
     */
    private $category;

    /**
     * @var ExtensionInstances
     *
     * @ORM\ManyToMany(targetEntity="ExtensionInstances", inversedBy="asset")
     * @ORM\JoinTable(name="asset_modules",
     *   joinColumns={
     *     @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="extension_instance_id", referencedColumnName="id")
     *   }
     * )
     */
    private $extensionInstance;

    /**
     * @var ContentTypes
     *
     * @ORM\ManyToOne(targetEntity="ContentTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     * })
     */
    private $contentType;

    public function __construct()
    {
        $this->category = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set sourceId
     *
     * @param integer $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * Get sourceId
     *
     * @return integer 
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sefRequest
     *
     * @param string $sefRequest
     */
    public function setSefRequest($sefRequest)
    {
        $this->sefRequest = $sefRequest;
    }

    /**
     * Get sefRequest
     *
     * @return string 
     */
    public function getSefRequest()
    {
        return $this->sefRequest;
    }

    /**
     * Set request
     *
     * @param string $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Get request
     *
     * @return string 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set primaryCategoryId
     *
     * @param integer $primaryCategoryId
     */
    public function setPrimaryCategoryId($primaryCategoryId)
    {
        $this->primaryCategoryId = $primaryCategoryId;
    }

    /**
     * Get primaryCategoryId
     *
     * @return integer 
     */
    public function getPrimaryCategoryId()
    {
        return $this->primaryCategoryId;
    }

    /**
     * Set templateId
     *
     * @param integer $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * Get templateId
     *
     * @return integer 
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set translationOfId
     *
     * @param integer $translationOfId
     */
    public function setTranslationOfId($translationOfId)
    {
        $this->translationOfId = $translationOfId;
    }

    /**
     * Get translationOfId
     *
     * @return integer 
     */
    public function getTranslationOfId()
    {
        return $this->translationOfId;
    }

    /**
     * Set redirectToId
     *
     * @param integer $redirectToId
     */
    public function setRedirectToId($redirectToId)
    {
        $this->redirectToId = $redirectToId;
    }

    /**
     * Get redirectToId
     *
     * @return integer 
     */
    public function getRedirectToId()
    {
        return $this->redirectToId;
    }

    /**
     * Set viewGroupId
     *
     * @param integer $viewGroupId
     */
    public function setViewGroupId($viewGroupId)
    {
        $this->viewGroupId = $viewGroupId;
    }

    /**
     * Get viewGroupId
     *
     * @return integer 
     */
    public function getViewGroupId()
    {
        return $this->viewGroupId;
    }

    /**
     * Add category
     *
     * @param data\Entity\Categories $category
     */
    public function addCategories(\data\Entity\Categories $category)
    {
        $this->category[] = $category;
    }

    /**
     * Get category
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategory()
    {
        return $this->category;
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
     * Set contentType
     *
     * @param data\Entity\ContentTypes $contentType
     */
    public function setContentType(\data\Entity\ContentTypes $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get contentType
     *
     * @return data\Entity\ContentTypes 
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}