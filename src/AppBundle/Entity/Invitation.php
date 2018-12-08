<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="invitation")
 */
class Invitation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="招待状の名前は必ず入力してください")
     */
    protected $name;
    /**
     * @ORM\Column(name="code", type="string", length=255, unique=true)
    * @Assert\NotBlank(message="コードは必ず入力してください")
     */
    protected $code;
    /**
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message="招待状の説明文は必ず入力してください")
     */
    protected $description;
    /**
     * @ORM\Column(name="count_current", type="integer", nullable=true)
     */
    protected $count_current = 0;
    /**
     * @ORM\Column(name="count_limit", type="integer", nullable=true)
     */
    protected $count_limit = 10;
    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    protected $enabled = true;

 

 

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Invitation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Invitation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Invitation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set countCurrent.
     *
     * @param int|null $countCurrent
     *
     * @return Invitation
     */
    public function setCountCurrent($countCurrent = null)
    {
        $this->count_current = $countCurrent;

        return $this;
    }

    /**
     * Get countCurrent.
     *
     * @return int|null
     */
    public function getCountCurrent()
    {
        return $this->count_current;
    }

    /**
     * Set countLimit.
     *
     * @param int|null $countLimit
     *
     * @return Invitation
     */
    public function setCountLimit($countLimit = null)
    {
        $this->count_limit = $countLimit;

        return $this;
    }

    /**
     * Get countLimit.
     *
     * @return int|null
     */
    public function getCountLimit()
    {
        return $this->count_limit;
    }

    /**
     * Set enabled.
     *
     * @param bool|null $enabled
     *
     * @return Invitation
     */
    public function setEnabled($enabled = null)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool|null
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
}
