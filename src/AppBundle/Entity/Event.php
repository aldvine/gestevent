<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    // public function __construct(){
    //     $this->users =  new ArrayCollection();
    // }

    public function __construct()
    {
        $this->inscriptions= new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=10000, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlace", type="integer")
     *   @Assert\Range(
     *      min = 1,
     *  
     * )
     */
    private $nbPlace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     *  @Assert\Range(
     *      min = "now",
     *     
     *  
     * )
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=500, nullable=true)
     * 
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=500, nullable=true)
     */
    private $theme;


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;
    
    public function getUser(){
        return $this->user;
    }

    public function setUser($user){
        $this->user = $user;
        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity ="Inscription", mappedBy="event" )
     * 
     */
    private $inscriptions;
    /**
     * @return Collection|Inscriptions[]
     */
    public function getInscriptions(){
        return $this->inscriptions;
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
     * Set title
     *
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set nbPlace
     *
     * @param integer $nbPlace
     *
     * @return Event
     */
    public function setNbPlace($nbPlace)
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    /**
     * Get nbPlace
     *
     * @return int
     */
    public function getNbPlace()
    {
        return $this->nbPlace;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Event
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set theme
     *
     * @param string $theme
     *
     * @return Event
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }
}

