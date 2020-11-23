<?php

namespace App\Service;

use App\Entity\Partie;
use App\Entity\TypeCarte;
use Doctrine\ORM\EntityManagerInterface;

class CarteService {
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * CarteService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Retourne tous les types de carte possibles sous forme de tableau avec le code en index
     *
     * @return array
     */
    public function getAllCardTypesByCode()
    {
        $typesCartes = $this->entityManager->getRepository(TypeCarte::class)->findAll();
        $typesCartesByCode = [];
        /** @var TypeCarte $typeCarte */
        foreach($typesCartes as $typeCarte) {
            $typesCartesByCode[$typeCarte->getTypeCode()] = $typeCarte;
        }

        return $typesCartesByCode;
    }
}