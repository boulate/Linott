<?php

namespace App\Controller;

use App\Entity\JourType;
use App\Entity\User;
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
}
