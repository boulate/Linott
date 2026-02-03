<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\PeriodeRepository;
use App\Repository\SectionRepository;

class StatsService
{
    public function __construct(
        private PeriodeRepository $periodeRepository,
        private SectionRepository $sectionRepository
    ) {
    }

    public function getStatsBySection(User $user, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $periodes = $this->periodeRepository->findByUserAndDateRange($user, $start, $end);

        $stats = [];
        foreach ($periodes as $periode) {
            $sectionCode = $periode->getSection()->getCode();
            if (!isset($stats[$sectionCode])) {
                $stats[$sectionCode] = [
                    'section' => $periode->getSection(),
                    'totalMinutes' => 0,
                    'count' => 0,
                ];
            }
            $stats[$sectionCode]['totalMinutes'] += $periode->getDureeMinutes();
            $stats[$sectionCode]['count']++;
        }

        // Format totals
        foreach ($stats as &$stat) {
            $stat['totalFormatted'] = $this->formatMinutes($stat['totalMinutes']);
            $stat['percentage'] = 0;
        }

        // Calculate percentages
        $totalMinutes = array_sum(array_column($stats, 'totalMinutes'));
        if ($totalMinutes > 0) {
            foreach ($stats as &$stat) {
                $stat['percentage'] = round(($stat['totalMinutes'] / $totalMinutes) * 100, 1);
            }
        }

        return $stats;
    }

    public function getWeeklyEvolution(User $user, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $data = [];
        $current = $start instanceof \DateTimeImmutable
            ? $start
            : \DateTimeImmutable::createFromInterface($start);
        $endDate = $end instanceof \DateTimeImmutable
            ? $end
            : \DateTimeImmutable::createFromInterface($end);

        // Find Monday of start week
        $dayOfWeek = (int) $current->format('N');
        $current = $current->modify('-' . ($dayOfWeek - 1) . ' days');

        while ($current <= $endDate) {
            $weekEnd = $current->modify('+6 days');
            $periodes = $this->periodeRepository->findByUserAndDateRange($user, $current, $weekEnd);

            $totalMinutes = 0;
            foreach ($periodes as $periode) {
                $totalMinutes += $periode->getDureeMinutes();
            }

            $data[] = [
                'weekStart' => $current,
                'weekEnd' => $weekEnd,
                'label' => 'S' . $current->format('W'),
                'totalMinutes' => $totalMinutes,
                'totalHours' => round($totalMinutes / 60, 1),
            ];

            $current = $current->modify('+7 days');
        }

        return $data;
    }

    public function exportToCsv(User $user, \DateTimeInterface $start, \DateTimeInterface $end): string
    {
        $periodes = $this->periodeRepository->findByUserAndDateRange($user, $start, $end);

        $output = "Date;Debut;Fin;Duree;Section;Axe1;Axe2;Axe3;Commentaire;Validee\n";

        foreach ($periodes as $periode) {
            $output .= sprintf(
                "%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n",
                $periode->getDate()->format('d/m/Y'),
                $periode->getHeureDebut()->format('H:i'),
                $periode->getHeureFin()->format('H:i'),
                $periode->getDureeFormatted(),
                $periode->getSection()->getCode(),
                $periode->getAxe1()?->getCode() ?? '',
                $periode->getAxe2()?->getCode() ?? '',
                $periode->getAxe3()?->getCode() ?? '',
                str_replace(["\n", ";"], [" ", ","], $periode->getCommentaire() ?? ''),
                $periode->isValidee() ? 'Oui' : 'Non'
            );
        }

        return $output;
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%d:%02d', $hours, $mins);
    }
}
