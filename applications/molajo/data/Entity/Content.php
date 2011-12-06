<?php

use Gedmo\Mapping\Annotation as Gedmo;
namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Gedmo\Tree(type="nested")
 * data\Entity\Content
 *
 * @ORM\Table(name="content")
 * @ORM\Entity
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class Content
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string $subtitle
     *
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=false)
     */
    private $subtitle;

    /**
     * @var string $alias
     *
     * @ORM\Column(name="alias", type="string", length=255, nullable=false)
     */
    private $alias;

    /**
     * @var text $contentText
     *
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    private $contentText;

    /**
     * @var boolean $protected
     *
     * @ORM\Column(name="protected", type="boolean", nullable=false)
     */
    private $protected;

    /**
     * @var boolean $featured
     *
     * @ORM\Column(name="featured", type="boolean", nullable=false)
     */
    private $featured;

    /**
     * @var boolean $stickied
     *
     * @ORM\Column(name="stickied", type="boolean", nullable=false)
     */
    private $stickied;

    /**
     * @var boolean $status
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var datetime $startPublishingDatetime
     *
     * @ORM\Column(name="start_publishing_datetime", type="datetime", nullable=false)
     */
    private $startPublishingDatetime;

    /**
     * @var datetime $stopPublishingDatetime
     *
     * @ORM\Column(name="stop_publishing_datetime", type="datetime", nullable=false)
     */
    private $stopPublishingDatetime;

    /**
     * @var integer $version
     *
     * @ORM\Column(name="version", type="integer", nullable=false)
     */
    private $version;

    /**
     * @var integer $versionOfId
     *
     * @ORM\Column(name="version_of_id", type="integer", nullable=false)
     */
    private $versionOfId;

    /**
     * @var integer $statusPriorToVersion
     *
     * @ORM\Column(name="status_prior_to_version", type="integer", nullable=false)
     */
    private $statusPriorToVersion;

    /**
     * @var datetime $createdDatetime
     *
     * @ORM\Column(name="created_datetime", type="datetime", nullable=false)
     */
    private $createdDatetime;

    /**
     * @var integer $createdBy
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     */
    private $createdBy;

    /**
     * @var datetime $modifiedDatetime
     *
     * @ORM\Column(name="modified_datetime", type="datetime", nullable=false)
     */
    private $modifiedDatetime;

    /**
     * @var integer $modifiedBy
     *
     * @ORM\Column(name="modified_by", type="integer", nullable=false)
     */
    private $modifiedBy;

    /**
     * @var datetime $checkedOutDatetime
     *
     * @ORM\Column(name="checked_out_datetime", type="datetime", nullable=false)
     */
    private $checkedOutDatetime;

    /**
     * @var integer $checkedOutBy
     *
     * @ORM\Column(name="checked_out_by", type="integer", nullable=false)
     */
    private $checkedOutBy;

    /**
     * @var integer $parentId
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    private $parentId;

    /**
     * @var integer $root
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=false)
     */
    private $root;

    /**
     * @var integer $lft
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer", nullable=false)
     */
    private $lft;

    /**
     * @var integer $rgt
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer", nullable=false)
     */
    private $rgt;

    /**
     * @var integer $lvl
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", nullable=false)
     */
    private $lvl;

    /**
     * @var boolean $home
     *
     * @ORM\Column(name="home", type="boolean", nullable=false)
     */
    private $home;

    /**
     * @var string $position
     *
     * @ORM\Column(name="position", type="string", length=45, nullable=false)
     */
    private $position;

    /**
     * @var text $customFields
     *
     * @ORM\Column(name="custom_fields", type="text", nullable=true)
     */
    private $customFields;

    /**
     * @var text $parameters
     *
     * @ORM\Column(name="parameters", type="text", nullable=true)
     */
    private $parameters;

    /**
     * @var text $metadata
     *
     * @ORM\Column(name="metadata", type="text", nullable=true)
     */
    private $metadata;

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
     * @var integer $ordering
     *
     * @ORM\Column(name="ordering", type="integer", nullable=false)
     */
    private $ordering;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @var ContentTypes
     *
     * @ORM\ManyToOne(targetEntity="ContentTypes")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     * })
     */
    private $contentType;

    /**
     * @var ExtensionInstances
     *
     * @ORM\ManyToOne(targetEntity="ExtensionInstances")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="extension_instance_id", referencedColumnName="id")
     * })
     */
    private $extensionInstance;

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
     * Set subtitle
     *
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set alias
     *
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
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
     * Set protected
     *
     * @param boolean $protected
     */
    public function setProtected($protected)
    {
        $this->protected = $protected;
    }

    /**
     * Get protected
     *
     * @return boolean
     */
    public function getProtected()
    {
        return $this->protected;
    }

    /**
     * Set featured
     *
     * @param boolean $featured
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }

    /**
     * Get featured
     *
     * @return boolean
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set stickied
     *
     * @param boolean $stickied
     */
    public function setStickied($stickied)
    {
        $this->stickied = $stickied;
    }

    /**
     * Get stickied
     *
     * @return boolean
     */
    public function getStickied()
    {
        return $this->stickied;
    }

    /**
     * Set status
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set startPublishingDatetime
     *
     * @param datetime $startPublishingDatetime
     */
    public function setStartPublishingDatetime($startPublishingDatetime)
    {
        $this->startPublishingDatetime = $startPublishingDatetime;
    }

    /**
     * Get startPublishingDatetime
     *
     * @return datetime
     */
    public function getStartPublishingDatetime()
    {
        return $this->startPublishingDatetime;
    }

    /**
     * Set stopPublishingDatetime
     *
     * @param datetime $stopPublishingDatetime
     */
    public function setStopPublishingDatetime($stopPublishingDatetime)
    {
        $this->stopPublishingDatetime = $stopPublishingDatetime;
    }

    /**
     * Get stopPublishingDatetime
     *
     * @return datetime
     */
    public function getStopPublishingDatetime()
    {
        return $this->stopPublishingDatetime;
    }

    /**
     * Set version
     *
     * @param integer $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set versionOfId
     *
     * @param integer $versionOfId
     */
    public function setVersionOfId($versionOfId)
    {
        $this->versionOfId = $versionOfId;
    }

    /**
     * Get versionOfId
     *
     * @return integer
     */
    public function getVersionOfId()
    {
        return $this->versionOfId;
    }

    /**
     * Set statusPriorToVersion
     *
     * @param integer $statusPriorToVersion
     */
    public function setStatusPriorToVersion($statusPriorToVersion)
    {
        $this->statusPriorToVersion = $statusPriorToVersion;
    }

    /**
     * Get statusPriorToVersion
     *
     * @return integer
     */
    public function getStatusPriorToVersion()
    {
        return $this->statusPriorToVersion;
    }

    /**
     * Set createdDatetime
     *
     * @param datetime $createdDatetime
     */
    public function setCreatedDatetime($createdDatetime)
    {
        $this->createdDatetime = $createdDatetime;
    }

    /**
     * Get createdDatetime
     *
     * @return datetime
     */
    public function getCreatedDatetime()
    {
        return $this->createdDatetime;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set modifiedDatetime
     *
     * @param datetime $modifiedDatetime
     */
    public function setModifiedDatetime($modifiedDatetime)
    {
        $this->modifiedDatetime = $modifiedDatetime;
    }

    /**
     * Get modifiedDatetime
     *
     * @return datetime
     */
    public function getModifiedDatetime()
    {
        return $this->modifiedDatetime;
    }

    /**
     * Set modifiedBy
     *
     * @param integer $modifiedBy
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;
    }

    /**
     * Get modifiedBy
     *
     * @return integer
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set checkedOutDatetime
     *
     * @param datetime $checkedOutDatetime
     */
    public function setCheckedOutDatetime($checkedOutDatetime)
    {
        $this->checkedOutDatetime = $checkedOutDatetime;
    }

    /**
     * Get checkedOutDatetime
     *
     * @return datetime
     */
    public function getCheckedOutDatetime()
    {
        return $this->checkedOutDatetime;
    }

    /**
     * Set checkedOutBy
     *
     * @param integer $checkedOutBy
     */
    public function setCheckedOutBy($checkedOutBy)
    {
        $this->checkedOutBy = $checkedOutBy;
    }

    /**
     * Get checkedOutBy
     *
     * @return integer
     */
    public function getCheckedOutBy()
    {
        return $this->checkedOutBy;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set root
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set home
     *
     * @param boolean $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * Get home
     *
     * @return boolean
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Set position
     *
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
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
     * Set metadata
     *
     * @param text $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get metadata
     *
     * @return text
     */
    public function getMetadata()
    {
        return $this->metadata;
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
     * Set ordering
     *
     * @param integer $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * Get ordering
     *
     * @return integer
     */
    public function getOrdering()
    {
        return $this->ordering;
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

    /**
     * Set extensionInstance
     *
     * @param data\Entity\ExtensionInstances $extensionInstance
     */
    public function setExtensionInstance(\data\Entity\ExtensionInstances $extensionInstance)
    {
        $this->extensionInstance = $extensionInstance;
    }

    /**
     * Get extensionInstance
     *
     * @return data\Entity\ExtensionInstances
     */
    public function getExtensionInstance()
    {
        return $this->extensionInstance;
    }

    public function setParent(Content $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }
}