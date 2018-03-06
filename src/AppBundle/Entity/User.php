<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FosUser;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends FosUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        parent::__construct();
       
    }

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

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
     * @ORM\OneToMany(targetEntity ="Event", mappedBy="user")
     */
    private $events;
    /**
     * @return Collection|Event[]
     */
    public function getEvents(){
        return $this->events;
    }

     /**
     * @return Collection|Event[]
     */
    public function addEvents($event){
        $this->events[]=$event;
        return $this->events;
    }

    /**
     * @ORM\OneToMany(targetEntity ="Inscription", mappedBy="user")
     */
    private $inscriptions;
    /**
     * @return Collection|Inscriptions[]
     */
    public function getInscriptions(){
        return $this->inscriptions;
    }

      /**
     * @return Collection|Inscriptions[]
     */
    public function addInscriptions($inscription){
        $this->inscriptions[]=$inscription;
        return $this->inscriptions;
    }
}

