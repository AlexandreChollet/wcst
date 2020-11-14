<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Partie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Partie
     *
     * @ORM\ManyToOne(targetEntity="TypeCarte", inversedBy="parties")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(referencedColumnName="id")
     * })
     */
    private $sujet;

    /**
     * @ORM\OneToMany(targetEntity="ChoixCarte", mappedBy="partie")
     */
    protected $choixCartes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->choixCartes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return Partie
     */
    public function getSujet(): Partie
    {
        return $this->sujet;
    }

    /**
     * @param Partie $sujet
     */
    public function setSujet(Partie $sujet): void
    {
        $this->sujet = $sujet;
    }

    /**
     * @return ArrayCollection
     */
    public function getChoixCartes(): ArrayCollection
    {
        return $this->choixCartes;
    }

    /**
     * @param ArrayCollection $choixCartes
     */
    public function setChoixCartes(ArrayCollection $choixCartes): void
    {
        $this->choixCartes = $choixCartes;
    }
}