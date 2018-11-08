<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\PageRepository")
 * @UniqueEntity(
 *     fields={"slug"},
 *     message="ページスラッグはすでに利用されています"
 * )
 */
class Page
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
     * @ORM\Column(name="body", type="text")
     * @Assert\NotBlank(message="本文を入力してください")
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="slugを入力してください")
     * @Assert\Regex(
     *     pattern = "/^[a-zA-Z0-9]+$/i",
     *     message = "半角英数・数字のみ入力が可能です"
     * )
     */
    private $slug;

    /**
     * @ORM\Column(name="is_member", type="boolean")
     */
    private $is_member = false;

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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    */
    protected $createdUser;

    /**
    * @ORM\OneToOne(targetEntity="CmsBundle\Entity\Seo", cascade={"persist"})
    */
    protected $seo;




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
     * @return Page
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
     * Set body.
     *
     * @param string $body
     *
     * @return Page
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set isMember.
     *
     * @param bool $isMember
     *
     * @return Page
     */
    public function setIsMember($isMember)
    {
        $this->is_member = $isMember;

        return $this;
    }

    /**
     * Get isMember.
     *
     * @return bool
     */
    public function getIsMember()
    {
        return $this->is_member;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Page
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
     * @return Page
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

    /**
     * Set createdUser.
     *
     * @param \AppBundle\Entity\User|null $createdUser
     *
     * @return Page
     */
    public function setCreatedUser(\AppBundle\Entity\User $createdUser = null)
    {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * Get createdUser.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getCreatedUser()
    {
        return $this->createdUser;
    }

    /**
     * Set seo.
     *
     * @param \CmsBundle\Entity\Seo|null $seo
     *
     * @return Page
     */
    public function setSeo(\CmsBundle\Entity\Seo $seo = null)
    {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo.
     *
     * @return \CmsBundle\Entity\Seo|null
     */
    public function getSeo()
    {
        return $this->seo;
    }
}
