<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\ArticleRepository")
 * @UniqueEntity(
 *     fields={"slug"},
 *     message="ページスラッグはすでに利用されています"
 * )
 */
class Article
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @ORM\Column(name="body_extra", type="text", nullable=true)
     */
    private $body_extra;

    /**
     * @ORM\ManyToMany(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinTable(name="article_images",
     *   joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id")}
     * )
     */
	private $images;
	
    /**
     * @ORM\Column(name="is_published", type="boolean")
     */
    private $is_published = true;
    
    /**
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $is_deleted = false;

    /**
     * @ORM\Column(name="is_member", type="boolean")
     */
    private $is_member = false;

    /**
	 * @Assert\DateTime()
     * @ORM\Column(name="publishedAt", type="datetime", nullable=true)
     */
    private $publishedAt;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Article
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
     * Set title.
     *
     * @param string $title
     *
     * @return Article
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
     * @return Article
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
     * Set bodyExtra.
     *
     * @param string|null $bodyExtra
     *
     * @return Article
     */
    public function setBodyExtra($bodyExtra = null)
    {
        $this->body_extra = $bodyExtra;

        return $this;
    }

    /**
     * Get bodyExtra.
     *
     * @return string|null
     */
    public function getBodyExtra()
    {
        return $this->body_extra;
    }

    /**
     * Set isPublished.
     *
     * @param bool $isPublished
     *
     * @return Article
     */
    public function setIsPublished($isPublished)
    {
        $this->is_published = $isPublished;

        return $this;
    }

    /**
     * Get isPublished.
     *
     * @return bool
     */
    public function getIsPublished()
    {
        return $this->is_published;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return Article
     */
    public function setIsDeleted($isDeleted)
    {
        $this->is_deleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * Set isMember.
     *
     * @param bool $isMember
     *
     * @return Article
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
     * Set publishedAt.
     *
     * @param \DateTime|null $publishedAt
     *
     * @return Article
     */
    public function setPublishedAt($publishedAt = null)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt.
     *
     * @return \DateTime|null
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Article
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
     * @return Article
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
     * Add image.
     *
     * @param \CmsBundle\Entity\Image $image
     *
     * @return Article
     */
    public function addImage(\CmsBundle\Entity\Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image.
     *
     * @param \CmsBundle\Entity\Image $image
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeImage(\CmsBundle\Entity\Image $image)
    {
        return $this->images->removeElement($image);
    }

    /**
     * Get images.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set createdUser.
     *
     * @param \AppBundle\Entity\User|null $createdUser
     *
     * @return Article
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
     * @return Article
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
