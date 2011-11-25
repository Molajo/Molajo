<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\ViewGroups
 *
 * @ORM\Table(name="view_groups")
 * @ORM\Entity
 */
class ViewGroups
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
     * @var text $viewGroupNameList
     *
     * @ORM\Column(name="view_group_name_list", type="text", nullable=false)
     */
    private $viewGroupNameList;

    /**
     * @var text $viewGroupIdList
     *
     * @ORM\Column(name="view_group_id_list", type="text", nullable=false)
     */
    private $viewGroupIdList;

    /**
     * @var ContentTypes
     *
     * @ORM\ManyToOne(targetEntity="ContentTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     * })
     */
    private $contentType;
	
    /**
     * @var Categories
     *
     * @ORM\ManyToMany(targetEntity="Categories", mappedBy="viewGroup")
     */
    private $group;
	
    /**
     * @var Users
     *
     * @ORM\ManyToMany(targetEntity="Users", mappedBy="viewGroup")
     */
    private $user;

    public function __construct()
    {
        $this->group = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set viewGroupNameList
     *
     * @param text $viewGroupNameList
     */
    public function setViewGroupNameList($viewGroupNameList)
    {
        $this->viewGroupNameList = $viewGroupNameList;
    }

    /**
     * Get viewGroupNameList
     *
     * @return text 
     */
    public function getViewGroupNameList()
    {
        return $this->viewGroupNameList;
    }

    /**
     * Set viewGroupIdList
     *
     * @param text $viewGroupIdList
     */
    public function setViewGroupIdList($viewGroupIdList)
    {
        $this->viewGroupIdList = $viewGroupIdList;
    }

    /**
     * Get viewGroupIdList
     *
     * @return text 
     */
    public function getViewGroupIdList()
    {
        return $this->viewGroupIdList;
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