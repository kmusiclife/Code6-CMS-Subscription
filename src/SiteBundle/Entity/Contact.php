<?php

namespace SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="contact")
 */
class Contact
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="タイトルを入力してください")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank(message="メールアドレスを入力してください")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="お名前を入力してください")
     */
    private $name;

    /**
     * @ORM\Column(name="tel", type="string", length=128, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(name="zip", type="string", length=64, nullable=true)
     */
    private $zip;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(name="body", type="text")
     * @Assert\NotBlank(message="本文を入力してください")
     */
    private $body;

    /**
	 * @Assert\DateTime()
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
	 * @Assert\DateTime()
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;


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
     * Set title.
     *
     * @param string $title
     *
     * @return Contact
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Contact
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
     * Set tel.
     *
     * @param string|null $tel
     *
     * @return Contact
     */
    public function setTel($tel = null)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel.
     *
     * @return string|null
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set zip.
     *
     * @param string|null $zip
     *
     * @return Contact
     */
    public function setZip($zip = null)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip.
     *
     * @return string|null
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Contact
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set body.
     *
     * @param string $body
     *
     * @return Contact
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Contact
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Contact
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
