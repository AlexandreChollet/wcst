<?php

namespace App\Controller;

use App\Entity\ChoixCarte;
use App\Entity\Partie;
use App\Entity\TypeCarte;
use App\Service\CarteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use DateTime;

class PartieController extends AbstractController
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
     * @Route("/", name="test", methods={"GET"})
     */
    public function test()
    {
        $cartesReference = $this->entityManager->getRepository(TypeCarte::class)->findByOrdre(true);
        $cartesTirage = $this->entityManager->getRepository(TypeCarte::class)->findByOrdre();

        return $this->render('test/test.html.twig', [
            'cartesReference' => $cartesReference,
            'cartesTirage' => array_reverse($cartesTirage)
        ]);
    }

    /**
     * @param Request $request
     *
     * @Route("/save-test", name="save-test", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function saveTest(Request $request)
    {
        $newPartie = new Partie();
        $newPartie->setDate(new DateTime());
        $newPartie->setIpClient($request->getClientIp());
        $newPartie->setTempsNecessaire($request->request->get('partie')['tempsNecessaire']);

        // Récupération des types de carte possibles
        $typesCartes = $this->carteService->getAllCardTypesByCode();

        foreach($request->request->get('partie')['choixCartes'] as $key => $choix) {
            $newChoix = new ChoixCarte();
            $newChoix->setNumero($key + 1);
            $newChoix->setCartePosee($typesCartes[$choix['cartePosee']]);
            $newChoix->setCarteReference($typesCartes[$choix['carteReference']]);
            $newChoix->setTempsMicrosecondes($choix['tempsMicrosecondes']);
            $newChoix->setRegleActuelle($choix['regleActuelle']);
            $newChoix->setIsBonneReponse($choix['isBonneReponse'] === 'true');

            $this->entityManager->persist($newChoix);
            $newChoix->setPartie($newPartie);
        }

        if(array_key_exists('changementsRegles', $request->request->get('partie'))) {
            $newPartie->setChangementsRegle($request->request->get('partie')['changementsRegles']);
        }

        $this->entityManager->persist($newPartie);
        $this->entityManager->flush();

        return new JsonResponse($this->generateUrl('end', [], UrlGeneratorInterface::ABSOLUTE_URL), 200, array(), true);
    }

    /**
     * @Route("/end", name="end", methods={"GET"})
     */
    public function end()
    {
        return $this->render('test/end.html.twig');
    }
}