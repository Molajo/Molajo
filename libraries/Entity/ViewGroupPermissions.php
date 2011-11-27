<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\ViewGroupPermissions
 *
 * @ORM\Table(name="view_group_permissions")
 * @ORM\Entity
 */
class ViewGroupPermissions
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
     * @var Actions
     *
     * @ORM\ManyToOne(targetEntity="Actions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     * })
     */
    private $action;

    /**
     * @var Assets
     *
     * @ORM\ManyToOne(targetEntity="Assets")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     * })
     */
    private $asset;

    /**
     * @var ViewGroups
     *
     * @ORM\ManyToOne(targetEntity="ViewGroups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="view_group_id", referencedColumnName="id")
     * })
     */
    private $viewGroup;



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
     * Set action
     *
     * @param data\Entity\Actions $action
     */
    public function setAction(\data\Entity\Actions $action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     *
     * @return data\Entity\Actions 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set asset
     *
     * @param data\Entity\Assets $asset
     */
    public function setAsset(\data\Entity\Assets $asset)
    {
        $this->asset = $asset;
    }

    /**
     * Get asset
     *
     * @return data\Entity\Assets 
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * Set viewGroup
     *
     * @param data\Entity\ViewGroups $viewGroup
     */
    public function setViewGroup(\data\Entity\ViewGroups $viewGroup)
    {
        $this->viewGroup = $viewGroup;
    }

    /**
     * Get viewGroup
     *
     * @return data\Entity\ViewGroups 
     */
    public function getViewGroup()
    {
        return $this->viewGroup;
    }
}