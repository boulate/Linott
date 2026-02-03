<?php

namespace App\Controller;

use App\Entity\JourType;
use App\Entity\JourTypePeriode;
use App\Entity\User;
use App\Form\JourTypePeriodeSingleType;
use App\Form\JourTypeType;
use App\Repository\JourTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/jour-type')]
class JourTypeController extends AbstractController
{
    public function __construct(
        private JourTypeRepository $jourTypeRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_jour_type_index')]
    public function index(#[CurrentUser] User $user): Response
    {
        $jourTypes = $this->jourTypeRepository->findPersonnelsByUser($user);

        return $this->render('jour_type/index.html.twig', [
            'jourTypes' => $jourTypes,
        ]);
    }

    #[Route('/new', name: 'app_jour_type_new')]
    public function new(Request $request, #[CurrentUser] User $user): Response
    {
        $jourType = new JourType();
        $jourType->setUser($user);

        $form = $this->createForm(JourTypeType::class, $jourType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($jourType);
            $this->entityManager->flush();

            $this->addFlash('success', 'Modele "' . $jourType->getNom() . '" cree.');
            return $this->redirectToRoute('app_jour_type_index');
        }

        return $this->render('jour_type/form.html.twig', [
            'form' => $form,
            'jourType' => $jourType,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_jour_type_edit')]
    public function edit(JourType $jourType, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check: user can only edit their own templates
        if ($jourType->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce modele.');
        }

        $form = $this->createForm(JourTypeType::class, $jourType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Modele "' . $jourType->getNom() . '" modifie.');
            return $this->redirectToRoute('app_jour_type_index');
        }

        return $this->render('jour_type/form.html.twig', [
            'form' => $form,
            'jourType' => $jourType,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_jour_type_delete', methods: ['POST'])]
    public function delete(JourType $jourType, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check: user can only delete their own templates
        if ($jourType->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce modele.');
        }

        if ($this->isCsrfTokenValid('delete' . $jourType->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($jourType);
            $this->entityManager->flush();
            $this->addFlash('success', 'Modele supprime.');
        }

        return $this->redirectToRoute('app_jour_type_index');
    }

    #[Route('/{id}/duplicate', name: 'app_jour_type_duplicate', methods: ['POST'])]
    public function duplicate(JourType $jourType, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check: user can only duplicate accessible templates
        if ($jourType->getUser() !== null && $jourType->getUser() !== $user && !$jourType->isPartage()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas dupliquer ce modele.');
        }

        if ($this->isCsrfTokenValid('duplicate' . $jourType->getId(), $request->request->get('_token'))) {
            $newJourType = new JourType();
            $newJourType->setNom($jourType->getNom() . ' (copie)');
            $newJourType->setDescription($jourType->getDescription());
            $newJourType->setUser($user);
            $newJourType->setPartage(false);
            $newJourType->setActif(true);

            foreach ($jourType->getPeriodes() as $periode) {
                $newPeriode = new \App\Entity\JourTypePeriode();
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

        return $this->redirectToRoute('app_jour_type_index');
    }

    #[Route('/{id}/periode/new', name: 'app_jour_type_periode_new')]
    public function newPeriode(JourType $jourType, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check
        if ($jourType->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce modele.');
        }

        // Get last period BEFORE creating new one (for default times)
        $lastPeriode = $jourType->getPeriodes()->last();

        $periode = new JourTypePeriode();
        $jourType->addPeriode($periode);

        // Default times based on last period
        if ($lastPeriode) {
            $periode->setHeureDebut($lastPeriode->getHeureFin());
            $periode->setHeureFin($lastPeriode->getHeureFin()->modify('+1 hour'));
        } else {
            $periode->setHeureDebut(new \DateTimeImmutable('09:00'));
            $periode->setHeureFin(new \DateTimeImmutable('12:00'));
        }

        $form = $this->createForm(JourTypePeriodeSingleType::class, $periode, [
            'action' => $this->generateUrl('app_jour_type_periode_new', ['id' => $jourType->getId()]),
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
                'editRoute' => 'app_jour_type_periode_edit',
                'deleteRoute' => 'app_jour_type_periode_delete',
            ]);
        }

        return $this->render('compta/_periode_form.html.twig', [
            'form' => $form,
            'isEdit' => false,
            'hxTarget' => '#periodes-list',
        ]);
    }

    #[Route('/{id}/periode/{periodeId}/edit', name: 'app_jour_type_periode_edit')]
    public function editPeriode(JourType $jourType, int $periodeId, Request $request, #[CurrentUser] User $user): Response
    {
        // Security check
        if ($jourType->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce modele.');
        }

        $periode = $this->entityManager->getRepository(JourTypePeriode::class)->find($periodeId);
        if (!$periode || $periode->getJourType() !== $jourType) {
            throw $this->createNotFoundException('Periode non trouvee.');
        }

        $form = $this->createForm(JourTypePeriodeSingleType::class, $periode, [
            'action' => $this->generateUrl('app_jour_type_periode_edit', ['id' => $jourType->getId(), 'periodeId' => $periodeId]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->render('jour_type/_periodes_list.html.twig', [
                'jourType' => $jourType,
                'periodes' => $jourType->getPeriodes(),
                'totalFormatted' => $jourType->getTotalFormatted(),
                'editRoute' => 'app_jour_type_periode_edit',
                'deleteRoute' => 'app_jour_type_periode_delete',
            ]);
        }

        return $this->render('compta/_periode_form.html.twig', [
            'form' => $form,
            'isEdit' => true,
            'hxTarget' => '#periodes-list',
        ]);
    }

    #[Route('/{id}/periode/{periodeId}/delete', name: 'app_jour_type_periode_delete', methods: ['DELETE'])]
    public function deletePeriode(JourType $jourType, int $periodeId, #[CurrentUser] User $user): Response
    {
        // Security check
        if ($jourType->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce modele.');
        }

        $periode = $this->entityManager->getRepository(JourTypePeriode::class)->find($periodeId);
        if ($periode && $periode->getJourType() === $jourType) {
            $this->entityManager->remove($periode);
            $this->entityManager->flush();
        }

        return $this->render('jour_type/_periodes_list.html.twig', [
            'jourType' => $jourType,
            'periodes' => $jourType->getPeriodes(),
            'totalFormatted' => $jourType->getTotalFormatted(),
            'editRoute' => 'app_jour_type_periode_edit',
            'deleteRoute' => 'app_jour_type_periode_delete',
        ]);
    }
}
