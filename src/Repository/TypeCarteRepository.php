<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TypeCarteRepository.
 */
class TypeCarteRepository extends EntityRepository
{
    public function findByOrdre($isCarteReference = false)
    {
        $query = $this->createQueryBuilder('tc')
            ->where('tc.isCarteReference = :isReference')
            ->orderBy('tc.ordre')
            ->setParameter('isReference', $isCarteReference)
            ->getQuery()
            ->execute();

        return $query;
    }
}
