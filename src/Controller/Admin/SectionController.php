<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Legacy controller - all routes redirect to the unified AxeController
 */
#[Route('/admin/sections')]
class SectionController extends AbstractController
{
    #[Route('', name: 'app_admin_sections')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_admin_axes', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/new', name: 'app_admin_sections_new')]
    public function new(): Response
    {
        return $this->redirectToRoute('app_admin_axes', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/{id}/edit', name: 'app_admin_sections_edit')]
    public function edit(int $id): Response
    {
        return $this->redirectToRoute('app_admin_axes', [], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/{id}/delete', name: 'app_admin_sections_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        return $this->redirectToRoute('app_admin_axes', [], Response::HTTP_MOVED_PERMANENTLY);
    }
}
