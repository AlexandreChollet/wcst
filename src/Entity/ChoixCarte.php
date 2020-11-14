<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ChoixCarte
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
     * @ORM\ManyToOne(targetEntity="TypeCarte", inversedBy="choixCartes")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(referencedColumnName="id")
     * })
     */
    private $partie;

    /**
     * @var TypeCarte
     *
     * @ORM\ManyToOne(targetEntity="TypeCarte")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(referencedColumnName="id")
     * })
     */
    private $cartePosee;

    /**
     * @var TypeCarte
     *
     * @ORM\ManyToOne(targetEntity="TypeCarte")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(referencedColumnName="id")
     * })
     */
    private $carteReference;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $tempsMicrosecondes;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return TypeCarte
     */
    public function getCartePosee(): TypeCarte
    {
        return $this->cartePosee;
    }

    /**
     * @param TypeCarte $cartePosee
     */
    public function setCartePosee(TypeCarte $cartePosee): void
    {
        $this->cartePosee = $cartePosee;
    }

    /**
     * @return TypeCarte
     */
    public function getCarteReference(): TypeCarte
    {
        return $this->carteReference;
    }

    /**
     * @param TypeCarte $carteReference
     */
    public function setCarteReference(TypeCarte $carteReference): void
    {
        $this->carteReference = $carteReference;
    }

    /**
     * @return int
     */
    public function getTempsMicrosecondes(): int
    {
        return $this->tempsMicrosecondes;
    }

    /**
     * @param int $tempsMicrosecondes
     */
    public function setTempsMicrosecondes(int $tempsMicrosecondes): void
    {
        $this->tempsMicrosecondes = $tempsMicrosecondes;
    }

    /**
     * @return Partie
     */
    public function getPartie(): Partie
    {
        return $this->partie;
    }

    /**
     * @param Partie $partie
     */
    public function setPartie(Partie $partie): void
    {
        $this->partie = $partie;
    }
}
