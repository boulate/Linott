<?php

namespace App\Service;

use App\Entity\Periode;
use App\Entity\User;
use App\Repository\PeriodeRepository;

class ComptaService
{
    public function __construct(
        private PeriodeRepository $periodeRepository
    ) {
    }

    public function calculateTotalMinutes(array $periodes): int
    {
        $total = 0;
        foreach ($periodes as $periode) {
            $total += $periode->getDureeMinutes();
        }
        return $total;
    }

    public function formatMinutesToHours(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%d:%02d', $hours, $mins);
    }

    public function getTotalForDate(User $user, \DateTimeInterface $date): string
    {
        $minutes = $this->periodeRepository->getTotalMinutesByUserAndDate($user, $date);
        return $this->formatMinutesToHours($minutes);
    }

    public function getTotalForWeek(User $user, \DateTimeInterface $startOfWeek): string
    {
        $minutes = $this->periodeRepository->getTotalMinutesByUserAndWeek($user, $startOfWeek);
        return $this->formatMinutesToHours($minutes);
    }

    public function getWeekData(User $user, \DateTimeInterface $date): array
    {
        $startOfWeek = $this->getStartOfWeek($date);
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->modify("+$i days");
            $periodes = $this->periodeRepository->findByUserAndDate($user, $currentDate);
            $totalMinutes = $this->calculateTotalMinutes($periodes);

            // Check if all periods are validated
            $isFullyValidated = count($periodes) > 0 && array_reduce(
                $periodes,
                fn($carry, $p) => $carry && $p->isValidee(),
                true
            );

            $days[] = [
                'date' => $currentDate,
                'periodes' => $periodes,
                'total' => $this->formatMinutesToHours($totalMinutes),
                'totalMinutes' => $totalMinutes,
                'isWeekend' => in_array($currentDate->format('N'), ['6', '7']),
                'isToday' => $currentDate->format('Y-m-d') === (new \DateTimeImmutable())->format('Y-m-d'),
                'isFullyValidated' => $isFullyValidated,
            ];
        }

        $weekTotal = array_sum(array_column($days, 'totalMinutes'));

        return [
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $startOfWeek->modify('+6 days'),
            'days' => $days,
            'weekTotal' => $this->formatMinutesToHours($weekTotal),
            'weekTotalMinutes' => $weekTotal,
        ];
    }

    public function getStartOfWeek(\DateTimeInterface $date): \DateTimeImmutable
    {
        $dateImmutable = $date instanceof \DateTimeImmutable
            ? $date
            : \DateTimeImmutable::createFromInterface($date);

        // Monday = 1, so we go back to Monday
        $dayOfWeek = (int) $dateImmutable->format('N');
        $daysToSubtract = $dayOfWeek - 1;

        return $dateImmutable->modify("-$daysToSubtract days")->setTime(0, 0, 0);
    }

    public function copyFromPreviousDay(User $user, \DateTimeInterface $date): array
    {
        $previousDate = $date instanceof \DateTimeImmutable
            ? $date->modify('-1 day')
            : \DateTimeImmutable::createFromInterface($date)->modify('-1 day');

        $previousPeriodes = $this->periodeRepository->findByUserAndDate($user, $previousDate);
        $newPeriodes = [];

        $targetDate = $date instanceof \DateTimeImmutable
            ? $date
            : \DateTimeImmutable::createFromInterface($date);

        foreach ($previousPeriodes as $previousPeriode) {
            $newPeriode = new Periode();
            $newPeriode->setUser($user);
            $newPeriode->setDate($targetDate);
            $newPeriode->setHeureDebut($previousPeriode->getHeureDebut());
            $newPeriode->setHeureFin($previousPeriode->getHeureFin());
            $newPeriode->setSection($previousPeriode->getSection());
            $newPeriode->setAxe1($previousPeriode->getAxe1());
            $newPeriode->setAxe2($previousPeriode->getAxe2());
            $newPeriode->setAxe3($previousPeriode->getAxe3());
            $newPeriode->setCommentaire($previousPeriode->getCommentaire());
            $newPeriode->setValidee(false);

            $newPeriodes[] = $newPeriode;
        }

        return $newPeriodes;
    }

    public function validateDay(User $user, \DateTimeInterface $date): int
    {
        $periodes = $this->periodeRepository->findByUserAndDate($user, $date);
        $count = 0;

        foreach ($periodes as $periode) {
            if (!$periode->isValidee()) {
                $periode->setValidee(true);
                $count++;
            }
        }

        return $count;
    }

    public function validateWeek(User $user, \DateTimeInterface $startOfWeek): int
    {
        $periodes = $this->periodeRepository->findByUserAndWeek($user, $startOfWeek);
        $count = 0;

        foreach ($periodes as $periode) {
            if (!$periode->isValidee()) {
                $periode->setValidee(true);
                $count++;
            }
        }

        return $count;
    }
}
