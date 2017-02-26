<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique",columns={"email"})}
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    const MATCH_VALUE_THRESHOLD = 25;

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
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="Preference", mappedBy="user")
     * @var Preference[]
     */
    protected $preferences;

    public function __construct()
    {
        $this->preferences = new ArrayCollection();
    }

    public function preferencesMatch($categories)
    {
        $matchValue = 0;
        foreach ($this->preferences as $preference) {
            /** @var Category $category */
            foreach ($categories as $category) {
                if ($preference->match($category)) {
                    $matchValue += $preference->getValue() * $category->getValue();
                }
            }
        }

        return $matchValue >= self::MATCH_VALUE_THRESHOLD;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstname($firstName)
    {
        $this->firstname = $firstName;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Preference[]
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * @param Preference[] $preferences
     * @return User
     */
    public function setPreferences($preferences)
    {
        $this->preferences = $preferences;
    }
}

