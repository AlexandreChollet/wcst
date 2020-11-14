<?php

namespace App\Entity;

use App\Repository\TypeCarteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(TypeCarteRepository::class)
 */
class TypeCarte
{
    /**
     * Formes
     */
    const FORME_TRIANGLE    = 'T';
    const FORME_CROIX       = 'C';
    const FORME_ROND        = 'R';
    const FORME_ETOILE      = 'E';
    const CORRESPONDANCE_FORMES = [
        self::FORME_TRIANGLE    => 'triangle',
        self::FORME_CROIX       => 'croix',
        self::FORME_ROND        => 'rond',
        self::FORME_ETOILE      => 'etoile'
    ];

    /**
     * Couleurs
     */
    const COULEUR_ROUGE     = 'R';
    const COULEUR_BLEU      = 'B';
    const COULEUR_VERT      = 'V';
    const COULEUR_JAUNE     = 'J';
    const CORRESPONDANCE_COULEURS = [
        self::COULEUR_ROUGE     => 'rouge',
        self::COULEUR_BLEU      => 'bleu',
        self::COULEUR_VERT      => 'vert',
        self::COULEUR_JAUNE     => 'jaune'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $typeCode;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isCarteReference;

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
     * @return string
     */
    public function getTypeCode(): string
    {
        return $this->typeCode;
    }

    /**
     * @param string $typeCode
     */
    public function setTypeCode(string $typeCode): void
    {
        $this->typeCode = $typeCode;
    }

    /**
     * @return int
     */
    public function getOrdre(): int
    {
        return $this->ordre;
    }

    /**
     * @param int $ordre
     */
    public function setOrdre(int $ordre): void
    {
        $this->ordre = $ordre;
    }

    /**
     * @return bool
     */
    public function isCarteReference(): bool
    {
        return $this->isCarteReference;
    }

    /**
     * @param bool $isCarteReference
     */
    public function setIsCarteReference(bool $isCarteReference): void
    {
        $this->isCarteReference = $isCarteReference;
    }
}
