<?php

namespace App\Controller;

use App\Entity\Partie;
use App\Service\CarteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CarteService
     */
    protected $carteService;

    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     * @param CarteService $carteService
     */
    public function __construct(EntityManagerInterface $entityManager, CarteService $carteService)
    {
        $this->entityManager = $entityManager;
        $this->carteService = $carteService;
    }

    /**
     * @Route("/admin/resultat", name="admin-resultat", methods={"GET"})
     */
    public function resultats()
    {
        $parties = $this->entityManager->getRepository(Partie::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'parties' => $parties
        ]);
    }

    /**
     * @Route("/admin/resultat/{id}", name="admin-detail-resultat", methods={"GET"})
     *
     * @ParamConverter("partie", class="App\Entity\Partie")
     */
    public function detailsResultat(Partie $partie)
    {
        return $this->render('admin/detailResultat.html.twig', [
            'partie' => $partie
        ]);
    }
}