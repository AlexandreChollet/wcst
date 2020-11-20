<?php

namespace App\Controller;

use App\Entity\ChoixCarte;
use App\Entity\TypeCarte;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PartieController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * HomeController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $cartesReference = $this->entityManager->getRepository(TypeCarte::class)->findByOrdre(true);
        $cartesTirage = $this->entityManager->getRepository(TypeCarte::class)->findByOrdre();

        return $this->render('test/test.html.twig', [
            'cartesReference' => $cartesReference,
            'cartesTirage' => array_reverse($cartesTirage)
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('admin/admin.html.twig');
    }
}