<?php

namespace App\Controller\Admin;

use App\Entity\Section;
use App\Form\Admin\SectionType;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sections')]
class SectionController extends AbstractController
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_admin_sections')]
    public function index(): Response
    {
        return $this->render('admin/sections/index.html.twig', [
            'sections' => $this->sectionRepository->findBy([], ['ordre' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_admin_sections_new')]
    public function new(Request $request): Response
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($section);
            $this->entityManager->flush();

            $this->addFlash('success', 'Section créée.');
            return $this->redirectToRoute('app_admin_sections');
        }

        return $this->render('admin/sections/form.html.twig', [
            'form' => $form,
            'section' => $section,
            'isNew' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_sections_edit')]
    public function edit(Section $section, Request $request): Response
    {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Section modifiée.');
            return $this->redirectToRoute('app_admin_sections');
        }

        return $this->render('admin/sections/form.html.twig', [
            'form' => $form,
            'section' => $section,
            'isNew' => false,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_sections_delete', methods: ['POST'])]
    public function delete(Section $section, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $section->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($section);
            $this->entityManager->flush();
            $this->addFlash('success', 'Section supprimée.');
        }

        return $this->redirectToRoute('app_admin_sections');
    }
}
