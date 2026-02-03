<?php

namespace App\Controller;

use App\Entity\JourType;
use App\Entity\Periode;
use App\Entity\User;
use App\Form\PeriodeType;
use App\Repository\JourTypeRepository;
use App\Repository\PeriodeRepository;
use App\Service\AxeDataService;
use App\Service\ComptaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/compta')]
class ComptaController extends AbstractController
{
    public function __construct(
        private ComptaService $comptaService,
        private PeriodeRepository $periodeRepository,
        private JourTypeRepository $jourTypeRepository,
        private EntityManagerInterface $entityManager,
        private AxeDataService $axeDataService
    ) {
    }

    #[Route('/{date?}', name: 'app_compta', requirements: ['date' => '\d{4}-\d{2}-\d{2}'])]
    public function index(?string $date, Request $request, #[CurrentUser] User $user): Response
    {
        $currentDate = $date
            ? new \DateTimeImmutable($date)
            : new \DateTimeImmutable();

        $periodes = $this->periodeRepository->findByUserAndDate($user, $currentDate);
        $weekData = $this->comptaService->getWeekData($user, $currentDate);
        $jourTypes = $this->jourTypeRepository->findForUser($user);

        $params = [
            'date' => $currentDate,
            'previousDate' => $currentDate->modify('-1 day')->format('Y-m-d'),
            'nextDate' => $currentDate->modify('+1 day')->format('Y-m-d'),
            'periodes' => $periodes,
            'totalHeures' => $this->comptaService->getTotalForDate($user, $currentDate),
            'weekData' => $weekData,
            'jourTypes' => $jourTypes,
        ];

        // Only return partial for targeted HTMX requests, not boosted navigation
        $isHtmxRequest = $request->headers->has('HX-Request');
        $isBoosted = $request->headers->get('HX-Boosted') === 'true';

        if ($isHtmxRequest && !$isBoosted) {
            return $this->render('compta/_fiche.html.twig', $params);
        }

        return $this->render('compta/index.html.twig', $params);
    }

    #[Route('/periode/new/{date}', name: 'app_compta_periode_new', requirements: ['date' => '\d{4}-\d{2}-\d{2}'])]
    public function newPeriode(string $date, Request $request, #[CurrentUser] User $user): Response
    {
        $currentDate = new \DateTimeImmutable($date);

        $periode = new Periode();
        $periode->setUser($user);
        $periode->setDate($currentDate);

        // Default times
        $lastPeriode = $this->periodeRepository->findOneBy(
            ['user' => $user, 'date' => $currentDate],
            ['heureFin' => 'DESC']
        );

        if ($lastPeriode) {
            $periode->setHeureDebut($lastPeriode->getHeureFin());
            $periode->setHeureFin($lastPeriode->getHeureFin()->modify('+1 hour'));
        } else {
            $periode->setHeureDebut(new \DateTimeImmutable('09:00'));
            $periode->setHeureFin(new \DateTimeImmutable('12:00'));
        }

        $form = $this->createForm(PeriodeType::class, $periode, [
            'action' => $this->generateUrl('app_compta_periode_new', ['date' => $date]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for overlapping periods
            if ($this->periodeRepository->hasOverlap($periode)) {
                $this->addFlash('error', 'Cette periode chevauche une periode existante.');
                return $this->render('compta/_periode_form.html.twig', [
                    'form' => $form,
                    'periode' => $periode,
                    'date' => $currentDate,
                    'isEdit' => false,
                    'axesData' => $this->axeDataService->getAllAxesData(),
                ]);
            }

            $this->entityManager->persist($periode);
            $this->entityManager->flush();

            if ($request->headers->has('HX-Request')) {
                return $this->redirectToRoute('app_compta', ['date' => $date]);
            }

            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->render('compta/_periode_form.html.twig', [
            'form' => $form,
            'periode' => $periode,
            'date' => $currentDate,
            'isEdit' => false,
            'axesData' => $this->axeDataService->getAllAxesData(),
        ]);
    }

    #[Route('/periode/{id}/edit', name: 'app_compta_periode_edit')]
    public function editPeriode(Periode $periode, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check
        if ($periode->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette periode.');
        }

        $date = $periode->getDate()->format('Y-m-d');

        $form = $this->createForm(PeriodeType::class, $periode, [
            'action' => $this->generateUrl('app_compta_periode_edit', ['id' => $periode->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for overlapping periods
            if ($this->periodeRepository->hasOverlap($periode)) {
                $this->addFlash('error', 'Cette periode chevauche une periode existante.');
                return $this->render('compta/_periode_form.html.twig', [
                    'form' => $form,
                    'periode' => $periode,
                    'date' => $periode->getDate(),
                    'isEdit' => true,
                    'axesData' => $this->axeDataService->getAllAxesData(),
                ]);
            }

            $this->entityManager->flush();

            if ($request->headers->has('HX-Request')) {
                return $this->redirectToRoute('app_compta', ['date' => $date]);
            }

            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->render('compta/_periode_form.html.twig', [
            'form' => $form,
            'periode' => $periode,
            'date' => $periode->getDate(),
            'isEdit' => true,
            'axesData' => $this->axeDataService->getAllAxesData(),
        ]);
    }

    #[Route('/periode/{id}/delete', name: 'app_compta_periode_delete', methods: ['POST', 'DELETE'])]
    public function deletePeriode(Periode $periode, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check
        if ($periode->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette periode.');
        }

        $date = $periode->getDate()->format('Y-m-d');

        if ($this->isCsrfTokenValid('delete' . $periode->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($periode);
            $this->entityManager->flush();
        }

        if ($request->headers->has('HX-Request')) {
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->redirectToRoute('app_compta', ['date' => $date]);
    }

    #[Route('/copy/{date}', name: 'app_compta_copy', requirements: ['date' => '\d{4}-\d{2}-\d{2}'], methods: ['POST'])]
    public function copyFromPreviousDay(string $date, Request $request, #[CurrentUser] User $user): Response
    {
        $currentDate = new \DateTimeImmutable($date);

        // Check if day already has periods
        $existingPeriodes = $this->periodeRepository->findByUserAndDate($user, $currentDate);
        if (count($existingPeriodes) > 0) {
            $this->addFlash('warning', 'Cette journee contient deja des periodes.');
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        $newPeriodes = $this->comptaService->copyFromPreviousDay($user, $currentDate);

        if (count($newPeriodes) === 0) {
            $this->addFlash('info', 'Aucune periode a copier depuis le jour precedent.');
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        foreach ($newPeriodes as $periode) {
            $this->entityManager->persist($periode);
        }
        $this->entityManager->flush();

        $this->addFlash('success', count($newPeriodes) . ' periode(s) copiee(s).');

        if ($request->headers->has('HX-Request')) {
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->redirectToRoute('app_compta', ['date' => $date]);
    }

    #[Route('/validate/{date}', name: 'app_compta_validate_day', requirements: ['date' => '\d{4}-\d{2}-\d{2}'], methods: ['POST'])]
    public function validateDay(string $date, Request $request, #[CurrentUser] User $user): Response
    {
        $currentDate = new \DateTimeImmutable($date);

        $count = $this->comptaService->validateDay($user, $currentDate);
        $this->entityManager->flush();

        if ($count > 0) {
            $this->addFlash('success', "$count periode(s) validee(s).");
        } else {
            $this->addFlash('info', 'Aucune periode a valider.');
        }

        if ($request->headers->has('HX-Request')) {
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->redirectToRoute('app_compta', ['date' => $date]);
    }

    #[Route('/validate-week/{date}', name: 'app_compta_validate_week', requirements: ['date' => '\d{4}-\d{2}-\d{2}'], methods: ['POST'])]
    public function validateWeek(string $date, Request $request, #[CurrentUser] User $user): Response
    {
        $currentDate = new \DateTimeImmutable($date);
        $startOfWeek = $this->comptaService->getStartOfWeek($currentDate);

        $count = $this->comptaService->validateWeek($user, $startOfWeek);
        $this->entityManager->flush();

        if ($count > 0) {
            $this->addFlash('success', "$count periode(s) validee(s) pour la semaine.");
        } else {
            $this->addFlash('info', 'Aucune periode a valider pour cette semaine.');
        }

        if ($request->headers->has('HX-Request')) {
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->redirectToRoute('app_compta', ['date' => $date]);
    }

    #[Route('/apply-jour-type/{id}/{date}', name: 'app_compta_apply_jour_type', requirements: ['date' => '\d{4}-\d{2}-\d{2}'], methods: ['POST'])]
    public function applyJourType(JourType $jourType, string $date, Request $request, #[CurrentUser] User $user): Response
    {
        $currentDate = new \DateTimeImmutable($date);

        // Check if day already has periods
        $existingPeriodes = $this->periodeRepository->findByUserAndDate($user, $currentDate);
        if (count($existingPeriodes) > 0) {
            $this->addFlash('warning', 'Cette journee contient deja des periodes.');
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        // Check access to the template (personal or shared)
        if ($jourType->getUser() !== null && $jourType->getUser() !== $user && !$jourType->isPartage()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas acces a ce modele.');
        }

        $newPeriodes = $this->comptaService->applyJourType($user, $jourType, $currentDate);

        if (count($newPeriodes) === 0) {
            $this->addFlash('info', 'Ce modele ne contient aucune periode.');
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        foreach ($newPeriodes as $periode) {
            $this->entityManager->persist($periode);
        }
        $this->entityManager->flush();

        $this->addFlash('success', count($newPeriodes) . ' periode(s) creee(s) depuis le modele "' . $jourType->getNom() . '".');

        if ($request->headers->has('HX-Request')) {
            return $this->redirectToRoute('app_compta', ['date' => $date]);
        }

        return $this->redirectToRoute('app_compta', ['date' => $date]);
    }
}
