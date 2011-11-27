<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\ComponentOptions
 *
 * @ORM\Table(name="component_options")
 * @ORM\Entity
 */
class ComponentOptions
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
     * @var boolean $protected
     *
     * @ORM\Column(name="protected", type="boolean", nullable=false)
     */
    private $protected;

    /**
     * @var integer $optionId
     *
     * @ORM\Column(name="option_id", type="integer", nullable=false)
     */
    private $optionId;

    /**
     * @var string $optionValueLiteral
     *
     * @ORM\Column(name="option_value_literal", type="string", length=255, nullable=false)
     */
    private $optionValueLiteral;

    /**
     * @var string $optionValue
     *
     * @ORM\Column(name="option_value", type="string", length=80, nullable=false)
     */
    private $optionValue;

    /**
     * @var integer $ordering
     *
     * @ORM\Column(name="ordering", type="integer", nullable=false)
     */
    private $ordering;

    /**
     * @var ExtensionInstances
     *
     * @ORM\ManyToOne(targetEntity="ExtensionInstances")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="extension_instance_id", referencedColumnName="id")
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
     * Set optionId
     *
     * @param integer $optionId
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
    }

    /**
     * Get optionId
     *
     * @return integer 
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * Set optionValueLiteral
     *
     * @param string $optionValueLiteral
     */
    public function setOptionValueLiteral($optionValueLiteral)
    {
        $this->optionValueLiteral = $optionValueLiteral;
    }

    /**
     * Get optionValueLiteral
     *
     * @return string 
     */
    public function getOptionValueLiteral()
    {
        return $this->optionValueLiteral;
    }

    /**
     * Set optionValue
     *
     * @param string $optionValue
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;
    }

    /**
     * Get optionValue
     *
     * @return string 
     */
    public function getOptionValue()
    {
        return $this->optionValue;
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
}