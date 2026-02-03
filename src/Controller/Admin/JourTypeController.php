<?php

namespace App\Controller\Admin;

use App\Entity\JourType;
use App\Entity\JourTypePeriode;
use App\Form\JourTypePeriodeSingleType;
use App\Form\JourTypeType;
use App\Repository\JourTypeRepository;
use App\Service\AxeDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/jour-types')]
class JourTypeController extends AbstractController
{
    public function __construct(
        private JourTypeRepository $jourTypeRepository,
        private EntityManagerInterface $entityManager,
        private AxeDataService $axeDataService
    ) {
    }

    #[Route('', name: 'app_admin_jour_types')]
    public function index(): Response
    {
        return $this->render('admin/jour_types/index.html.twig', [
            'jourTypes' => $this->jourTypeRepository->findAllPartages(),
        ]);
    }

    #[Route('/new', name: 'app_admin_jour_types_new')]
    public function new(Request $request): Response
    {
        $jourType = new JourType();
        $jourType->setUser(null);
        $jourType->setPartage(true);

        $form = $this->createForm(JourTypeType::class, $jourType, ['is_admin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure shared templates have no user
            $jourType->setUser(null);

            $this->entityManager->persist($jourType);
            $this->entityManager->flush();

            $this->addFlash('success', 'Modele partage "' . $jourType->getNom() . '" cree.');
            return $this->redirectToRoute('app_admin_jour_types');
        }

        return $this->render('admin/jour_types/form.html.twig', [
            'form' => $form,
            'jourType' => $jourType,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_jour_types_edit')]
    public function edit(JourType $jourType, Request $request): Response
    {
        $form = $this->createForm(JourTypeType::class, $jourType, ['is_admin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ensure shared templates have no user
            if ($jourType->isPartage()) {
                $jourType->setUser(null);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Modele "' . $jourType->getNom() . '" modifie.');
            return $this->redirectToRoute('app_admin_jour_types');
        }

        return $this->render('admin/jour_types/form.html.twig', [
            'form' => $form,
            'jourType' => $jourType,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_jour_types_delete', methods: ['POST'])]
    public function delete(JourType $jourType, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $jourType->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($jourType);
            $this->entityManager->flush();
            $this->addFlash('success', 'Modele supprime.');
        }

        return $this->redirectToRoute('app_admin_jour_types');
    }

    #[Route('/{id}/duplicate', name: 'app_admin_jour_types_duplicate', methods: ['POST'])]
    public function duplicate(JourType $jourType, Request $request): Response
    {
        if ($this->isCsrfTokenValid('duplicate' . $jourType->getId(), $request->request->get('_token'))) {
            $newJourType = new JourType();
            $newJourType->setNom($jourType->getNom() . ' (copie)');
            $newJourType->setDescription($jourType->getDescription());
            $newJourType->setUser(null);
            $newJourType->setPartage(true);
            $newJourType->setActif(true);

            foreach ($jourType->getPeriodes() as $periode) {
                $newPeriode = new JourTypePeriode();
                $newPeriode->setHeureDebut($periode->getHeureDebut());
                $newPeriode->setHeureFin($periode->getHeureFin());
                $newPeriode->setSection($periode->getSection());
                $newPeriode->setAxe1($periode->getAxe1());
                $newPeriode->setAxe2($periode->getAxe2());
                $newPeriode->setAxe3($periode->getAxe3());
                $newPeriode->setCommentaire($periode->getCommentaire());
                $newPeriode->setOrdre($periode->getOrdre());
                $newJourType->addPeriode($newPeriode);
            }

            $this->entityManager->persist($newJourType);
            $this->entityManager->flush();

            $this->addFlash('success', 'Modele duplique.');
        }

        return $this->redirectToRoute('app_admin_jour_types');
    }

    #[Route('/{id}/periode/new', name: 'app_admin_jour_types_periode_new')]
    public function newPeriode(JourType $jourType, Request $request): Response
    {
        $periode = new JourTypePeriode();
        $periode->setJourType($jourType);

        // Default times based on last period
        $lastPeriode = $jourType->getPeriodes()->last();
        if ($lastPeriode) {
            $periode->setHeureDebut($lastPeriode->getHeureFin());
            $periode->setHeureFin($lastPeriode->getHeureFin()->modify('+1 hour'));
        } else {
            $periode->setHeureDebut(new \DateTimeImmutable('09:00'));
            $periode->setHeureFin(new \DateTimeImmutable('12:00'));
        }

        $form = $this->createForm(JourTypePeriodeSingleType::class, $periode, [
            'action' => $this->generateUrl('app_admin_jour_types_periode_new', ['id' => $jourType->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $periode->setOrdre($jourType->getPeriodes()->count());
            $this->entityManager->persist($periode);
            $this->entityManager->flush();

            return $this->render('jour_type/_periodes_list.html.twig', [
                'jourType' => $jourType,
                'periodes' => $jourType->getPeriodes(),
                'totalFormatted' => $jourType->getTotalFormatted(),
                'editRoute' => 'app_admin_jour_types_periode_edit',
                'deleteRoute' => 'app_admin_jour_types_periode_delete',
            ]);
        }

        return $this->render('compta/_periode_form.html.twig', [
            'form' => $form,
            'isEdit' => false,
            'hxTarget' => '#periodes-list',
            'axesData' => $this->axeDataService->getAllAxesData(),
        ]);
    }

    #[Route('/{id}/periode/{periodeId}/edit', name: 'app_admin_jour_types_periode_edit')]
    public function editPeriode(JourType $jourType, int $periodeId, Request $request): Response
    {
        $periode = $this->entityManager->getRepository(JourTypePeriode::class)->find($periodeId);
        if (!$periode || $periode->getJourType() !== $jourType) {
            throw $this->createNotFoundException('Periode non trouvee.');
        }

        $form = $this->createForm(JourTypePeriodeSingleType::class, $periode, [
            'action' => $this->generateUrl('app_admin_jour_types_periode_edit', ['id' => $jourType->getId(), 'periodeId' => $periodeId]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->render('jour_type/_periodes_list.html.twig', [
                'jourType' => $jourType,
                'periodes' => $jourType->getPeriodes(),
                'totalFormatted' => $jourType->getTotalFormatted(),
                'editRoute' => 'app_admin_jour_types_periode_edit',
                'deleteRoute' => 'app_admin_jour_types_periode_delete',
            ]);
        }

        return $this->render('compta/_periode_form.html.twig', [
            'form' => $form,
            'isEdit' => true,
            'hxTarget' => '#periodes-list',
            'axesData' => $this->axeDataService->getAllAxesData(),
        ]);
    }

    #[Route('/{id}/periode/{periodeId}/delete', name: 'app_admin_jour_types_periode_delete', methods: ['DELETE'])]
    public function deletePeriode(JourType $jourType, int $periodeId): Response
    {
        $periode = $this->entityManager->getRepository(JourTypePeriode::class)->find($periodeId);
        if ($periode && $periode->getJourType() === $jourType) {
            $this->entityManager->remove($periode);
            $this->entityManager->flush();
        }

        return $this->render('jour_type/_periodes_list.html.twig', [
            'jourType' => $jourType,
            'periodes' => $jourType->getPeriodes(),
            'totalFormatted' => $jourType->getTotalFormatted(),
            'editRoute' => 'app_admin_jour_types_periode_edit',
            'deleteRoute' => 'app_admin_jour_types_periode_delete',
        ]);
    }
}
