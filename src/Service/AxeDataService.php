<?php

namespace App\Service;

use App\Repository\Axe1Repository;
use App\Repository\Axe2Repository;
use App\Repository\Axe3Repository;
use App\Repository\SectionRepository;

class AxeDataService
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private Axe1Repository $axe1Repository,
        private Axe2Repository $axe2Repository,
        private Axe3Repository $axe3Repository
    ) {
    }

    /**
     * Récupère toutes les données des axes pour le sélecteur Alpine.js
     *
     * @return array{sections: array, axes1: array, axes2: array, axes3: array}
     */
    public function getAllAxesData(): array
    {
        $sections = $this->sectionRepository->findAllActive();
        $axes1 = $this->axe1Repository->findAllActive();
        $axes2 = $this->axe2Repository->findAllActive();
        $axes3 = $this->axe3Repository->findAllActive();

        return [
            'sections' => array_map(fn($s) => [
                'id' => $s->getId(),
                'code' => $s->getCode(),
                'libelle' => $s->getLibelle(),
            ], $sections),
            'axes1' => array_map(fn($a) => [
                'id' => $a->getId(),
                'code' => $a->getCode(),
                'libelle' => $a->getLibelle(),
                'sectionId' => $a->getSection()->getId(),
            ], $axes1),
            'axes2' => array_map(fn($a) => [
                'id' => $a->getId(),
                'code' => $a->getCode(),
                'libelle' => $a->getLibelle(),
                'axe1Id' => $a->getAxe1()->getId(),
            ], $axes2),
            'axes3' => array_map(fn($a) => [
                'id' => $a->getId(),
                'code' => $a->getCode(),
                'libelle' => $a->getLibelle(),
                'axe2Id' => $a->getAxe2()->getId(),
            ], $axes3),
        ];
    }
}
