<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use App\Repository\PartieRepository;

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
     * @ORM\OneToMany(targetEntity="ChoixCarte", mappedBy="partie")
     */
    protected $choixCartes;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $changementsRegle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $ipClient;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $tempsNecessaire;

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
     * @return ArrayCollection
     */
    public function getChoixCartes()
    {
        return $this->choixCartes;
    }

    /**
     * @param ArrayCollection $choixCartes
     */
    public function setChoixCartes(ArrayCollection $choixCartes)
    {
        $this->choixCartes = $choixCartes;
    }

    /**
     * @return array
     */
    public function getChangementsRegle()
    {
        return $this->changementsRegle;
    }

    /**
     * @param array $changementsRegle
     */
    public function setChangementsRegle($changementsRegle)
    {
        $this->changementsRegle = $changementsRegle;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getIpClient(): string
    {
        return $this->ipClient;
    }

    /**
     * @param string $ipClient
     */
    public function setIpClient(string $ipClient)
    {
        $this->ipClient = $ipClient;
    }

    /**
     * @return int
     */
    public function getTempsNecessaire(): int
    {
        return $this->tempsNecessaire;
    }

    /**
     * @param int $tempsNecessaire
     */
    public function setTempsNecessaire(int $tempsNecessaire)
    {
        $this->tempsNecessaire = $tempsNecessaire;
    }

    /**
     * Retourne les choix de carte d'une partie triés par numéro
     *
     * @return ArrayCollection
     */
    public function getChoixParOrdre()
    {
        try {
            $iterator = $this->choixCartes->getIterator();
            $iterator->uasort(function (ChoixCarte $a, ChoixCarte $b) {
                return ($a->getNumero() < $b->getNumero()) ? -1 : 1;
            });
            return new ArrayCollection(iterator_to_array($iterator));
        } catch (\Exception $e) {
            return $this->choixCartes;
        }
    }

    /**
     * Retourne le nombre totale d'erreurs
     */
    public function getNbErreurs()
    {
        $erreurs = 0;

        /** @var ChoixCarte $choix */
        foreach($this->choixCartes as $choix) {
            $erreurs = !$choix->isBonneReponse() ? $erreurs + 1 : $erreurs;
        }

        return $erreurs;
    }

    /**
     * Retourne la liste des erreurs persévératives
     *
     * @return array
     */
    public function getErreursPerseveratrices()
    {
        $listeEP = [];

        /** @var ChoixCarte $choix */
        foreach($this->getChoixParOrdre() as $key => $choix) {
            // Ne peut pas être la première réponse
            if($key === 0) {
                continue;
            }

            if(!$choix->isBonneReponse()) {
                /** @var ChoixCarte $choixPrecedent */
                $choixPrecedent = $this->choixCartes->get($key - 1);
                $regleMatcheeChoixPrecedent = $choixPrecedent->getCarteReference()->getMatchedRegle($choixPrecedent->getCartePosee());
                $regleMatcheeChoix = $choix->getCarteReference()->getMatchedRegle($choix->getCartePosee());
                if($regleMatcheeChoix === $regleMatcheeChoixPrecedent && !$choixPrecedent->isBonneReponse()) {
                    $listeEP[] = ['numero' => $choix->getNumero(), 'regle' => $regleMatcheeChoix];
                }
            }
        }

        return $listeEP;
    }

    /**
     * Retourne la liste des abadons prématurés
     *
     * @return array
     */
    public function getAbandonsPrematures()
    {
        $listeAP = [];

        /** @var ChoixCarte $choix */
        foreach ($this->getChoixParOrdre() as $key => $choix) {
            // Ne peut pas être la première réponse
            if ($key === 0) {
                continue;
            }

            if (!$choix->isBonneReponse()) {
                /** @var ChoixCarte $choixPrecedent */
                $choixPrecedent = $this->choixCartes->get($key - 1);
                $regleMatcheeChoixPrecedent = $choixPrecedent->getCarteReference()->getMatchedRegle($choixPrecedent->getCartePosee());
                $regleMatcheeChoix = $choix->getCarteReference()->getMatchedRegle($choix->getCartePosee());

                // On ignore si la réponse précédente était un changement de règle
                $ignore = false;
                foreach($this->changementsRegle as $changement) {
                    if($changement['index'] == $choixPrecedent->getNumero()) {
                        $ignore = true;
                    }
                }

                if ($regleMatcheeChoix !== $regleMatcheeChoixPrecedent && $choixPrecedent->isBonneReponse() && $ignore === false) {
                    $listeAP[] = ['numero' => $choix->getNumero(), 'regle' => $regleMatcheeChoix];
                }
            }
        }

        return $listeAP;
    }


}