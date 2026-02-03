<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/stats')]
class StatsController extends AbstractController
{
    public function __construct(
        private StatsService $statsService
    ) {
    }

    #[Route('', name: 'app_stats')]
    public function index(Request $request, #[CurrentUser] User $user): Response
    {
        $now = new \DateTimeImmutable();

        // Default: current month
        $startDate = $request->query->get('start')
            ? new \DateTimeImmutable($request->query->get('start'))
            : $now->modify('first day of this month');

        $endDate = $request->query->get('end')
            ? new \DateTimeImmutable($request->query->get('end'))
            : $now->modify('last day of this month');

        $statsBySection = $this->statsService->getStatsBySection($user, $startDate, $endDate);
        $weeklyEvolution = $this->statsService->getWeeklyEvolution($user, $startDate, $endDate);

        // Prepare chart data
        $chartLabels = array_map(fn($s) => $s['section']->getCode(), $statsBySection);
        $chartData = array_map(fn($s) => round($s['totalMinutes'] / 60, 1), $statsBySection);
        $chartColors = ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4'];

        return $this->render('stats/index.html.twig', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'statsBySection' => $statsBySection,
            'weeklyEvolution' => $weeklyEvolution,
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
            'chartColors' => json_encode(array_slice($chartColors, 0, count($chartLabels))),
            'weeklyLabels' => json_encode(array_map(fn($w) => $w['label'], $weeklyEvolution)),
            'weeklyData' => json_encode(array_map(fn($w) => $w['totalHours'], $weeklyEvolution)),
        ]);
    }

    #[Route('/export', name: 'app_stats_export')]
    public function export(Request $request, #[CurrentUser] User $user): Response
    {
        $startDate = $request->query->get('start')
            ? new \DateTimeImmutable($request->query->get('start'))
            : (new \DateTimeImmutable())->modify('first day of this month');

        $endDate = $request->query->get('end')
            ? new \DateTimeImmutable($request->query->get('end'))
            : (new \DateTimeImmutable())->modify('last day of this month');

        $csv = $this->statsService->exportToCsv($user, $startDate, $endDate);

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', sprintf(
            'attachment; filename="export_heures_%s_%s.csv"',
            $startDate->format('Ymd'),
            $endDate->format('Ymd')
        ));

        return $response;
    }
}
