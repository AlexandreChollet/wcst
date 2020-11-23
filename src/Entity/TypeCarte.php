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

    /**
     * Trouve une règle qui match entre deux cartes
     *
     * @param TypeCarte $typeCarte
     *
     * @return string
     */
    public function getMatchedRegle(TypeCarte $typeCarte)
    {
        $codeCarteA = $this->getTypeCode();
        $codeCarteB = $typeCarte->getTypeCode();

        if($codeCarteA[0] === $codeCarteB[0]) {
            return 'nombre';
        }

        if($codeCarteA[1] === $codeCarteB[1]) {
            return 'forme';
        }

        if($codeCarteA[2] === $codeCarteB[2]) {
            return 'couleur';
        }

        return '';
    }
}
