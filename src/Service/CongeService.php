<?php

namespace App\Service;

use App\Entity\Conge;
use App\Entity\TypeConge;
use App\Entity\User;
use App\Repository\CongeRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\TypeCongeRepository;

class CongeService
{
    public function __construct(
        private CongeRepository $congeRepository,
        private TypeCongeRepository $typeCongeRepository,
        private ConfigurationRepository $configurationRepository
    ) {
    }

    public function getCompteurs(User $user, int $year): array
    {
        $compteurs = [];

        // Get CP allocation
        $cpAnnuels = (int) $this->configurationRepository->getValue('cp_annuels', '25');
        $rttAnnuels = (int) $this->configurationRepository->getValue('rtt_annuels', '10');

        $typesDecompte = $this->typeCongeRepository->findBy(['decompte' => true, 'actif' => true]);

        foreach ($typesDecompte as $type) {
            $pris = $this->congeRepository->countJoursByUserAndTypeAndYear($user, $type, $year);

            $allocation = 0;
            if ($type->getCode() === 'CP') {
                $allocation = $cpAnnuels;
            } elseif ($type->getCode() === 'RTT') {
                $allocation = $rttAnnuels;
            }

            $compteurs[] = [
                'type' => $type,
                'allocation' => $allocation,
                'pris' => $pris,
                'restant' => $allocation - $pris,
            ];
        }

        return $compteurs;
    }

    public function calculateNbJours(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): float
    {
        $nbJours = 0;
        $current = $dateDebut instanceof \DateTimeImmutable
            ? $dateDebut
            : \DateTimeImmutable::createFromInterface($dateDebut);
        $end = $dateFin instanceof \DateTimeImmutable
            ? $dateFin
            : \DateTimeImmutable::createFromInterface($dateFin);

        while ($current <= $end) {
            $dayOfWeek = (int) $current->format('N');
            // Exclude weekends
            if ($dayOfWeek < 6) {
                $nbJours++;
            }
            $current = $current->modify('+1 day');
        }

        return $nbJours;
    }

    public function getCalendarData(User $user, int $year, int $month): array
    {
        $firstDay = new \DateTimeImmutable("$year-$month-01");
        $lastDay = $firstDay->modify('last day of this month');

        // Get start of calendar (Monday of first week)
        $startDayOfWeek = (int) $firstDay->format('N');
        $calendarStart = $firstDay->modify('-' . ($startDayOfWeek - 1) . ' days');

        // Get end of calendar (Sunday of last week)
        $endDayOfWeek = (int) $lastDay->format('N');
        $calendarEnd = $lastDay->modify('+' . (7 - $endDayOfWeek) . ' days');

        $conges = $this->congeRepository->findByUserAndMonth($user, $year, $month);

        // Index conges by date
        $congesByDate = [];
        foreach ($conges as $conge) {
            $current = $conge->getDateDebut();
            while ($current <= $conge->getDateFin()) {
                $dateKey = $current->format('Y-m-d');
                if (!isset($congesByDate[$dateKey])) {
                    $congesByDate[$dateKey] = [];
                }
                $congesByDate[$dateKey][] = $conge;
                $current = $current->modify('+1 day');
            }
        }

        $weeks = [];
        $currentDay = $calendarStart;
        $currentWeek = [];

        while ($currentDay <= $calendarEnd) {
            $dateKey = $currentDay->format('Y-m-d');
            $currentWeek[] = [
                'date' => $currentDay,
                'isCurrentMonth' => (int) $currentDay->format('m') === $month,
                'isToday' => $currentDay->format('Y-m-d') === (new \DateTimeImmutable())->format('Y-m-d'),
                'isWeekend' => in_array($currentDay->format('N'), ['6', '7']),
                'conges' => $congesByDate[$dateKey] ?? [],
            ];

            if (count($currentWeek) === 7) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }

            $currentDay = $currentDay->modify('+1 day');
        }

        return [
            'year' => $year,
            'month' => $month,
            'monthName' => $firstDay->format('F'),
            'weeks' => $weeks,
            'previousMonth' => $firstDay->modify('-1 month'),
            'nextMonth' => $firstDay->modify('+1 month'),
        ];
    }
}
