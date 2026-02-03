<?php

namespace App\Controller;

use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private ConfigurationRepository $configurationRepository
    ) {
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        $dashboardEnabled = $this->configurationRepository->getValue('module_dashboard');

        if (!filter_var($dashboardEnabled, FILTER_VALIDATE_BOOLEAN)) {
            return $this->redirectToRoute('app_compta');
        }

        return $this->render('dashboard/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
