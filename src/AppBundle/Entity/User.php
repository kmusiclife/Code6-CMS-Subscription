<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="お名前(姓)を入力してください", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="一文字以上は必ず入力してください",
     *     maxMessage="これ以上入力することは出来ません",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $fname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="お名前(名)を入力してください", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="一文字以上は必ず入力してください",
     *     maxMessage="これ以上入力することは出来ません",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $lname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="郵便番号を入力してください", groups={"Registration", "Profile"})
     * @Assert\Regex(pattern="/^\d{3}-\d{4}$|^\d{7}$/", message="郵便番号のフォーマットが間違っています") 
     * @Assert\Length(
     *     min=1,
     *     max=12,
     *     minMessage="7文字以上は必ず入力してください",
     *     maxMessage="これ以上入力することは出来ません",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="住所を入力してください", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=5,
     *     max=255,
     *     minMessage="5文字以上は必ず入力してください",
     *     maxMessage="これ以上入力することは出来ません",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=7, max=20, minMessage = "電話番号の桁数が足りません", maxMessage = "電話番号の桁数が多すぎます")
     * @Assert\Regex(pattern="/^\d{10}$|^\d{11}$|^\d{3,}-\d{3,}-\d{3,}$/", message="電話番号のフォーマットが間違っています") 
     * @Assert\NotBlank(message="電話番号を入力してください", groups={"Registration", "Profile"})
     */
    protected $tel;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $facebook_url;

    /**
     * @Assert\NotBlank(message="クレジットカード番号を登録してください", groups={"Registration"})
     */
    protected $stripe_token_id;
    public function getStripeTokenId(){ return $this->stripe_token_id; }
    public function setStripeTokenId($stripe_token_id){ $this->stripe_token_id = $stripe_token_id; }
    
    /**
     * @Assert\NotBlank(message="プランは必ず選択してください", groups={"Registration"})
     */
    protected $stripe_plan_id;
    public function getStripePlanId(){ return $this->stripe_plan_id; }
    public function setStripePlanId($stripe_plan_id){ $this->stripe_plan_id = $stripe_plan_id; }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stripe_customer_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stripe_subscription_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $theme;
    public function setTheme($theme = null){
        $this->theme = $theme;
        return $this;
    }
    public function getTheme(){
        return $this->theme;
    }
    
    /* Google Recaptcha */
    private $recaptcha;
    public function getRecaptcha(){ return $this->recaptcha; }
    public function setRecaptcha($recaptcha){ $this->recaptcha = $recaptcha; }



    /**
     * Set fname.
     *
     * @param string|null $fname
     *
     * @return User
     */
    public function setFname($fname = null)
    {
        $this->fname = $fname;

        return $this;
    }

    /**
     * Get fname.
     *
     * @return string|null
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set lname.
     *
     * @param string|null $lname
     *
     * @return User
     */
    public function setLname($lname = null)
    {
        $this->lname = $lname;

        return $this;
    }

    /**
     * Get lname.
     *
     * @return string|null
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * Set zip.
     *
     * @param string|null $zip
     *
     * @return User
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
     * @return User
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
     * Set tel.
     *
     * @param string|null $tel
     *
     * @return User
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
     * Set facebookUrl.
     *
     * @param string|null $facebookUrl
     *
     * @return User
     */
    public function setFacebookUrl($facebookUrl = null)
    {
        $this->facebook_url = $facebookUrl;

        return $this;
    }

    /**
     * Get facebookUrl.
     *
     * @return string|null
     */
    public function getFacebookUrl()
    {
        return $this->facebook_url;
    }

    /**
     * Set stripeCustomerId.
     *
     * @param string|null $stripeCustomerId
     *
     * @return User
     */
    public function setStripeCustomerId($stripeCustomerId = null)
    {
        $this->stripe_customer_id = $stripeCustomerId;

        return $this;
    }

    /**
     * Get stripeCustomerId.
     *
     * @return string|null
     */
    public function getStripeCustomerId()
    {
        return $this->stripe_customer_id;
    }

    /**
     * Set stripeSubscriptionId.
     *
     * @param string|null $stripeSubscriptionId
     *
     * @return User
     */
    public function setStripeSubscriptionId($stripeSubscriptionId = null)
    {
        $this->stripe_subscription_id = $stripeSubscriptionId;

        return $this;
    }

    /**
     * Get stripeSubscriptionId.
     *
     * @return string|null
     */
    public function getStripeSubscriptionId()
    {
        return $this->stripe_subscription_id;
    }
}
