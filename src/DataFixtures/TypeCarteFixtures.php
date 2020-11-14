<?php

namespace App\DataFixtures;

use App\Entity\TypeCarte;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeCarteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cartesReference = [
            '1TR',
            '2EV',
            '3CJ',
            '4RB'
        ];
        foreach($cartesReference as $key => $carteCode) {
            $newtypeCarte = new TypeCarte();
            $newtypeCarte->setIsCarteReference(true);
            $newtypeCarte->setOrdre($key + 1);
            $newtypeCarte->setTypeCode($carteCode);

            $manager->persist($newtypeCarte);
        }

        $ordreTirage = [
            // Première colonne
            '2RR',
            '4EJ',
            '3TV',
            '1CB',
            '3ER',
            '2TB',
            '1RJ',
            '4CV',
            '2TJ',
            '1EB',
            '3RV',
            '4CR',
            '3TB',
            '2RJ',
            '4ER',
            '1CV',
            '3EB',
            '1RV',
            '2CR',
            '4TJ',
            '3RR',
            '2CB',
            '4TV',
            '1EJ',

            // Deuxième colonne
            '2RR',
            '4EJ',
            '3TV',
            '1CB',
            '3ER',
            '2TB',
            '1RR',
            '4CV',
            '2TJ',
            '1EB',
            '3RV',
            '4CR',
            '3TB',
            '2RR',
            '4ER',
            '1CV',
            '3EB',
            '1RV',
            '2CR',
            '4TJ',
            '3RR',
            '2CB',
            '4TV',
            '1EJ'
        ];
        foreach($ordreTirage as $key => $carteCode) {
            $newtypeCarte = new TypeCarte();
            $newtypeCarte->setIsCarteReference(false);
            $newtypeCarte->setOrdre($key + 1);
            $newtypeCarte->setTypeCode($carteCode);

            $manager->persist($newtypeCarte);
        }

        $manager->flush();
    }
}
