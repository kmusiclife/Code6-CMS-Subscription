<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Entity\User;

/**
 * @ORM\Table(name="image")
 * @ORM\Entity
 */
class Image
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    */
    protected $createdUser;
    
    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;
    
    /**
     * @var string $body
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;
    
    /**
     * @ORM\Column(name="is_lock", type="boolean")
     */
    private $is_lock = false;
    
    /**
     * @Assert\Image(
	 *    maxSize = "3M",
	 *    maxSizeMessage = "3MB以下のサイズの画像をアップロードしてください",
	 *    minWidth = 1000,
	 *    maxWidth = 2000,
	 *    minWidthMessage = "1000px以上の画像をアップロードしてください",
	 *    maxWidthMessage = "2000px以上の画像はアップロードできません",
	 *    minRatio = 1.1,
	 *    maxRatio = 1.8,
	 *    minRatioMessage = "横写真のみアップロードが可能です",
	 *    maxRatioMessage = "縦のサイズが足りません",
     *    mimeTypes={ "image/jpeg", "image/pjpeg", "image/jpg", "image/png", "image/x-png" },
     *    mimeTypesMessage = "「%filename%」は有効なファイルではありません。画像はjpgまたはpngのみアップロード可能です"
     * )
     */
    private $file;
	public function getFile(){
		return $this->file;
	}
	public function setFile($file){
		$this->file = $file;
	}
    
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
     * @param string|null $title
     *
     * @return Image
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body.
     *
     * @param string|null $body
     *
     * @return Image
     */
    public function setBody($body = null)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body.
     *
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set image.
     *
     * @param string|null $image
     *
     * @return Image
     */
    public function setImage($image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set isLock.
     *
     * @param bool $isLock
     *
     * @return Image
     */
    public function setIsLock($isLock)
    {
        $this->is_lock = $isLock;

        return $this;
    }

    /**
     * Get isLock.
     *
     * @return bool
     */
    public function getIsLock()
    {
        return $this->is_lock;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Image
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
     * @return Image
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
     * @return Image
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
}
