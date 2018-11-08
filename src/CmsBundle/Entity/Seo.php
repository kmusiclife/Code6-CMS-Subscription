<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Seo
 *
 * @ORM\Table(name="seo")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\SeoRepository")
 */
class Seo
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank(message="説明を入力してください")
     */
    private $description;

    /**
     * @ORM\Column(name="keywords", type="text", nullable=true)
     * @Assert\NotBlank(message="キーワードを入力してください(,半角のカンマでキーワードを区切ってください)")
     */
    private $keywords;

    /**
    * @ORM\OneToOne(targetEntity="CmsBundle\Entity\Image", cascade={"persist"})
    */
    private $image;

	/*
	 * File Upload Interface
	*/
	private $file;
	public function getFile(){
		return $this->file;
	}
	public function setFile($file){
		$this->file = $file;
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
     * Set description.
     *
     * @param string|null $description
     *
     * @return Seo
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords.
     *
     * @param string|null $keywords
     *
     * @return Seo
     */
    public function setKeywords($keywords = null)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return string|null
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set image.
     *
     * @param \CmsBundle\Entity\Image|null $image
     *
     * @return Seo
     */
    public function setImage(\CmsBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return \CmsBundle\Entity\Image|null
     */
    public function getImage()
    {
        return $this->image;
    }
}
