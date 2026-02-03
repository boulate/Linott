<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\User;
use App\Form\CongeType;
use App\Repository\CongeRepository;
use App\Service\CongeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/calendrier')]
class CalendrierController extends AbstractController
{
    public function __construct(
        private CongeService $congeService,
        private CongeRepository $congeRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/{year?}/{month?}', name: 'app_calendrier', requirements: ['year' => '\d{4}', 'month' => '\d{1,2}'])]
    public function index(?int $year, ?int $month, Request $request, #[CurrentUser] User $user): Response
    {
        $now = new \DateTimeImmutable();
        $year = $year ?? (int) $now->format('Y');
        $month = $month ?? (int) $now->format('n');

        $calendarData = $this->congeService->getCalendarData($user, $year, $month);
        $compteurs = $this->congeService->getCompteurs($user, $year);

        $params = [
            'calendarData' => $calendarData,
            'compteurs' => $compteurs,
            'year' => $year,
            'month' => $month,
        ];

        // Only return partial for targeted HTMX requests, not boosted navigation
        $isHtmxRequest = $request->headers->has('HX-Request');
        $isBoosted = $request->headers->get('HX-Boosted') === 'true';

        if ($isHtmxRequest && !$isBoosted) {
            return $this->render('calendrier/_month_grid.html.twig', $params);
        }

        return $this->render('calendrier/index.html.twig', $params);
    }

    #[Route('/conge/new', name: 'app_calendrier_conge_new')]
    public function newConge(Request $request, #[CurrentUser] User $user): Response
    {
        $conge = new Conge();
        $conge->setUser($user);

        $form = $this->createForm(CongeType::class, $conge, [
            'action' => $this->generateUrl('app_calendrier_conge_new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calculate nb jours
            $nbJours = $this->congeService->calculateNbJours(
                $conge->getDateDebut(),
                $conge->getDateFin()
            );
            $conge->setNbJours((string) $nbJours);

            $this->entityManager->persist($conge);
            $this->entityManager->flush();

            $this->addFlash('success', 'Conge enregistre.');

            return $this->redirectToRoute('app_calendrier', [
                'year' => $conge->getDateDebut()->format('Y'),
                'month' => $conge->getDateDebut()->format('n'),
            ]);
        }

        return $this->render('calendrier/_conge_form.html.twig', [
            'form' => $form,
            'conge' => $conge,
            'isEdit' => false,
        ]);
    }

    #[Route('/conge/{id}/edit', name: 'app_calendrier_conge_edit')]
    public function editConge(Conge $conge, Request $request, #[CurrentUser] User $user): Response
    {
        if ($conge->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CongeType::class, $conge, [
            'action' => $this->generateUrl('app_calendrier_conge_edit', ['id' => $conge->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nbJours = $this->congeService->calculateNbJours(
                $conge->getDateDebut(),
                $conge->getDateFin()
            );
            $conge->setNbJours((string) $nbJours);

            $this->entityManager->flush();

            $this->addFlash('success', 'Conge modifie.');

            return $this->redirectToRoute('app_calendrier', [
                'year' => $conge->getDateDebut()->format('Y'),
                'month' => $conge->getDateDebut()->format('n'),
            ]);
        }

        return $this->render('calendrier/_conge_form.html.twig', [
            'form' => $form,
            'conge' => $conge,
            'isEdit' => true,
        ]);
    }

    #[Route('/conge/{id}/delete', name: 'app_calendrier_conge_delete', methods: ['POST', 'DELETE'])]
    public function deleteConge(Conge $conge, Request $request, #[CurrentUser] User $user): Response
    {
        if ($conge->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $year = $conge->getDateDebut()->format('Y');
        $month = $conge->getDateDebut()->format('n');

        if ($this->isCsrfTokenValid('delete' . $conge->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($conge);
            $this->entityManager->flush();
            $this->addFlash('success', 'Conge supprime.');
        }

        return $this->redirectToRoute('app_calendrier', [
            'year' => $year,
            'month' => $month,
        ]);
    }
}
