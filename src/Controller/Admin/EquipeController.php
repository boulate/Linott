<?php

namespace App\Controller\Admin;

use App\Entity\Equipe;
use App\Form\Admin\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/equipes')]
class EquipeController extends AbstractController
{
    public function __construct(
        private EquipeRepository $equipeRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_admin_equipes')]
    public function index(): Response
    {
        $equipes = $this->equipeRepository->findAllOrdered();

        return $this->render('admin/equipes/index.html.twig', [
            'equipes' => $equipes,
        ]);
    }

    #[Route('/new', name: 'app_admin_equipes_new')]
    public function new(Request $request): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($equipe);
            $this->entityManager->flush();

            $this->addFlash('success', 'Equipe créée.');
            return $this->redirectToRoute('app_admin_equipes');
        }

        return $this->render('admin/equipes/form.html.twig', [
            'form' => $form,
            'equipe' => $equipe,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_equipes_edit')]
    public function edit(Equipe $equipe, Request $request): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Equipe modifiée.');
            return $this->redirectToRoute('app_admin_equipes');
        }

        return $this->render('admin/equipes/form.html.twig', [
            'form' => $form,
            'equipe' => $equipe,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_equipes_delete', methods: ['POST'])]
    public function delete(Equipe $equipe, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $equipe->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($equipe);
            $this->entityManager->flush();
            $this->addFlash('success', 'Equipe supprimée.');
        }

        return $this->redirectToRoute('app_admin_equipes');
    }

    #[Route('/{id}/toggle', name: 'app_admin_equipes_toggle', methods: ['POST'])]
    public function toggle(Equipe $equipe, Request $request): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $equipe->getId(), $request->request->get('_token'))) {
            $equipe->setActif(!$equipe->isActif());
            $this->entityManager->flush();
            $this->addFlash('success', $equipe->isActif() ? 'Equipe activée.' : 'Equipe désactivée.');
        }

        return $this->redirectToRoute('app_admin_equipes');
    }
}
