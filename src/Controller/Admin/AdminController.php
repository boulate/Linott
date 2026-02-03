<?php

namespace App\Controller\Admin;

use App\Repository\CongeRepository;
use App\Repository\PeriodeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(
        UserRepository $userRepository,
        PeriodeRepository $periodeRepository,
        CongeRepository $congeRepository
    ): Response {
        $stats = [
            'users' => $userRepository->count(['actif' => true]),
            'periodes' => $periodeRepository->count([]),
            'conges' => $congeRepository->count([]),
        ];

        return $this->render('admin/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}
