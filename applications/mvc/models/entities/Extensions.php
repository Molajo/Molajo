<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\Extensions
 *
 * @ORM\Table(name="extensions")
 * @ORM\Entity
 */
class Extensions
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
     * @var string $element
     *
     * @ORM\Column(name="element", type="string", length=100, nullable=false)
     */
    private $element;

    /**
     * @var string $folder
     *
     * @ORM\Column(name="folder", type="string", length=255, nullable=false)
     */
    private $folder;

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
     * @var UpdateSites
     *
     * @ORM\ManyToOne(targetEntity="UpdateSites")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="update_site_id", referencedColumnName="id")
     * })
     */
    private $updateSite;


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
     * Set element
     *
     * @param string $element
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * Get element
     *
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set folder
     *
     * @param string $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
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
     * Set updateSite
     *
     * @param data\Entity\UpdateSites $updateSite
     */
    public function setUpdateSite(\data\Entity\UpdateSites $updateSite)
    {
        $this->updateSite = $updateSite;
    }

    /**
     * Get updateSite
     *
     * @return data\Entity\UpdateSites
     */
    public function getUpdateSite()
    {
        return $this->updateSite;
    }
}