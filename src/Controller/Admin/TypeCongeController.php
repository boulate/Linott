<?php

namespace App\Controller\Admin;

use App\Entity\TypeConge;
use App\Form\Admin\TypeCongeType;
use App\Repository\TypeCongeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/types-conge')]
class TypeCongeController extends AbstractController
{
    public function __construct(
        private TypeCongeRepository $typeCongeRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_admin_types_conge')]
    public function index(): Response
    {
        return $this->render('admin/types_conge/index.html.twig', [
            'typesConge' => $this->typeCongeRepository->findBy([], ['code' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_admin_types_conge_new')]
    public function new(Request $request): Response
    {
        $typeConge = new TypeConge();
        $form = $this->createForm(TypeCongeType::class, $typeConge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($typeConge);
            $this->entityManager->flush();

            $this->addFlash('success', 'Type de congé créé.');
            return $this->redirectToRoute('app_admin_types_conge');
        }

        return $this->render('admin/types_conge/form.html.twig', [
            'form' => $form,
            'typeConge' => $typeConge,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_types_conge_edit')]
    public function edit(TypeConge $typeConge, Request $request): Response
    {
        $form = $this->createForm(TypeCongeType::class, $typeConge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Type de congé modifié.');
            return $this->redirectToRoute('app_admin_types_conge');
        }

        return $this->render('admin/types_conge/form.html.twig', [
            'form' => $form,
            'typeConge' => $typeConge,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_types_conge_delete', methods: ['POST'])]
    public function delete(TypeConge $typeConge, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $typeConge->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($typeConge);
            $this->entityManager->flush();
            $this->addFlash('success', 'Type de congé supprimé.');
        }

        return $this->redirectToRoute('app_admin_types_conge');
    }
}
