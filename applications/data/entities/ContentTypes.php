<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\ContentTypes
 *
 * @ORM\Table(name="content_types")
 * @ORM\Entity
 */
class ContentTypes
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
     * @var string $contentType
     *
     * @ORM\Column(name="content_type", type="string", length=255, nullable=false)
     */
    private $contentType;

    /**
     * @var boolean $protected
     *
     * @ORM\Column(name="protected", type="boolean", nullable=false)
     */
    private $protected;

    /**
     * @var string $sourceTable
     *
     * @ORM\Column(name="source_table", type="string", length=255, nullable=false)
     */
    private $sourceTable;

    /**
     * @var string $componentOption
     *
     * @ORM\Column(name="component_option", type="string", length=45, nullable=false)
     */
    private $componentOption;


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
     * Set contentType
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
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
     * Set sourceTable
     *
     * @param string $sourceTable
     */
    public function setSourceTable($sourceTable)
    {
        $this->sourceTable = $sourceTable;
    }

    /**
     * Get sourceTable
     *
     * @return string
     */
    public function getSourceTable()
    {
        return $this->sourceTable;
    }

    /**
     * Set componentOption
     *
     * @param string $componentOption
     */
    public function setComponentOption($componentOption)
    {
        $this->componentOption = $componentOption;
    }

    /**
     * Get componentOption
     *
     * @return string
     */
    public function getComponentOption()
    {
        return $this->componentOption;
    }
}