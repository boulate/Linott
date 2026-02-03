<?php

namespace App\Controller\Admin;

use App\Entity\JourType;
use App\Entity\JourTypePeriode;
use App\Form\JourTypeType;
use App\Repository\JourTypeRepository;
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
        private EntityManagerInterface $entityManager
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
}
