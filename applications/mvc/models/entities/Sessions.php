<?php

namespace data\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * data\Entity\Sessions
 *
 * @ORM\Table(name="sessions")
 * @ORM\Entity
 */
class Sessions
{
    /**
     * @var string $sessionId
     *
     * @ORM\Column(name="session_id", type="string", length=32, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sessionId;

    /**
     * @var string $sessionTime
     *
     * @ORM\Column(name="session_time", type="string", length=14, nullable=true)
     */
    private $sessionTime;

    /**
     * @var text $data
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;

    /**
     * @var integer $userId
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var Applications
     *
     * @ORM\ManyToOne(targetEntity="Applications")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     * })
     */
    private $application;


    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set sessionTime
     *
     * @param string $sessionTime
     */
    public function setSessionTime($sessionTime)
    {
        $this->sessionTime = $sessionTime;
    }

    /**
     * Get sessionTime
     *
     * @return string
     */
    public function getSessionTime()
    {
        return $this->sessionTime;
    }

    /**
     * Set data
     *
     * @param text $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return text
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set application
     *
     * @param data\Entity\Applications $application
     */
    public function setApplication(\data\Entity\Applications $application)
    {
        $this->application = $application;
    }

    /**
     * Get application
     *
     * @return data\Entity\Applications
     */
    public function getApplication()
    {
        return $this->application;
    }
}