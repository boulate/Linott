<?php

namespace App\Service;

use App\Entity\Axe1;
use App\Entity\Axe2;
use App\Entity\Axe3;
use App\Entity\Section;
use App\Repository\Axe1Repository;
use App\Repository\Axe2Repository;
use App\Repository\Axe3Repository;
use App\Repository\ConfigurationRepository;
use App\Repository\SectionRepository;

class AxeTreeService
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private Axe1Repository $axe1Repository,
        private Axe2Repository $axe2Repository,
        private Axe3Repository $axe3Repository,
        private ConfigurationRepository $configurationRepository
    ) {
    }

    /**
     * Build the complete tree hierarchy.
     *
     * @return array{
     *     sections: array<array{entity: Section, children: array}>,
     *     independent: array{axe1: array, axe2: array, axe3: array}
     * }
     */
    public function buildTree(): array
    {
        $axe1InheritsSection = $this->configurationRepository->getValue('axe1_inherits_section', '1') === '1';
        $axe2InheritsAxe1 = $this->configurationRepository->getValue('axe2_inherits_axe1', '1') === '1';
        $axe3InheritsAxe2 = $this->configurationRepository->getValue('axe3_inherits_axe2', '1') === '1';

        $sections = $this->sectionRepository->findBy([], ['ordre' => 'ASC', 'libelle' => 'ASC']);
        $allAxes1 = $this->axe1Repository->findBy([], ['ordre' => 'ASC', 'libelle' => 'ASC']);
        $allAxes2 = $this->axe2Repository->findBy([], ['ordre' => 'ASC', 'libelle' => 'ASC']);
        $allAxes3 = $this->axe3Repository->findBy([], ['ordre' => 'ASC', 'libelle' => 'ASC']);

        // Build lookup maps for faster access
        $axes1BySection = [];
        $axes2ByAxe1 = [];
        $axes3ByAxe2 = [];

        foreach ($allAxes1 as $axe1) {
            $sectionId = $axe1->getSection()?->getId();
            if ($sectionId) {
                $axes1BySection[$sectionId][] = $axe1;
            }
        }

        foreach ($allAxes2 as $axe2) {
            $axe1Id = $axe2->getAxe1()?->getId();
            if ($axe1Id) {
                $axes2ByAxe1[$axe1Id][] = $axe2;
            }
        }

        foreach ($allAxes3 as $axe3) {
            $axe2Id = $axe3->getAxe2()?->getId();
            if ($axe2Id) {
                $axes3ByAxe2[$axe2Id][] = $axe3;
            }
        }

        // Build the tree for sections (excluding _IND_ placeholders)
        $tree = [];
        $independentSectionIds = [];

        foreach ($sections as $section) {
            // Skip independent placeholder sections
            if (str_starts_with($section->getCode(), '_IND_')) {
                $independentSectionIds[] = $section->getId();
                continue;
            }

            $sectionNode = [
                'entity' => $section,
                'level' => 'section',
                'children' => [],
            ];

            // Add Axe1 children if inheritance is enabled
            if ($axe1InheritsSection && isset($axes1BySection[$section->getId()])) {
                foreach ($axes1BySection[$section->getId()] as $axe1) {
                    // Skip independent placeholder axes
                    if (str_starts_with($axe1->getCode(), '_IND_')) {
                        continue;
                    }

                    $axe1Node = [
                        'entity' => $axe1,
                        'level' => 'axe1',
                        'children' => [],
                    ];

                    // Add Axe2 children if inheritance is enabled
                    if ($axe2InheritsAxe1 && isset($axes2ByAxe1[$axe1->getId()])) {
                        foreach ($axes2ByAxe1[$axe1->getId()] as $axe2) {
                            if (str_starts_with($axe2->getCode(), '_IND_')) {
                                continue;
                            }

                            $axe2Node = [
                                'entity' => $axe2,
                                'level' => 'axe2',
                                'children' => [],
                            ];

                            // Add Axe3 children if inheritance is enabled
                            if ($axe3InheritsAxe2 && isset($axes3ByAxe2[$axe2->getId()])) {
                                foreach ($axes3ByAxe2[$axe2->getId()] as $axe3) {
                                    if (str_starts_with($axe3->getCode(), '_IND_')) {
                                        continue;
                                    }

                                    $axe2Node['children'][] = [
                                        'entity' => $axe3,
                                        'level' => 'axe3',
                                        'children' => [],
                                    ];
                                }
                            }

                            $axe1Node['children'][] = $axe2Node;
                        }
                    }

                    $sectionNode['children'][] = $axe1Node;
                }
            }

            $tree[] = $sectionNode;
        }

        // Collect independent axes (those without proper parents or with _IND_ parents)
        $independentAxes1 = [];
        $independentAxes2 = [];
        $independentAxes3 = [];

        if (!$axe1InheritsSection) {
            // All Axe1 are independent, show them flat
            foreach ($allAxes1 as $axe1) {
                if (!str_starts_with($axe1->getCode(), '_IND_')) {
                    $axe1Node = [
                        'entity' => $axe1,
                        'level' => 'axe1',
                        'children' => [],
                    ];

                    // Still show Axe2 children if axe2 inherits axe1
                    if ($axe2InheritsAxe1 && isset($axes2ByAxe1[$axe1->getId()])) {
                        foreach ($axes2ByAxe1[$axe1->getId()] as $axe2) {
                            if (str_starts_with($axe2->getCode(), '_IND_')) {
                                continue;
                            }

                            $axe2Node = [
                                'entity' => $axe2,
                                'level' => 'axe2',
                                'children' => [],
                            ];

                            if ($axe3InheritsAxe2 && isset($axes3ByAxe2[$axe2->getId()])) {
                                foreach ($axes3ByAxe2[$axe2->getId()] as $axe3) {
                                    if (!str_starts_with($axe3->getCode(), '_IND_')) {
                                        $axe2Node['children'][] = [
                                            'entity' => $axe3,
                                            'level' => 'axe3',
                                            'children' => [],
                                        ];
                                    }
                                }
                            }

                            $axe1Node['children'][] = $axe2Node;
                        }
                    }

                    $independentAxes1[] = $axe1Node;
                }
            }
        }

        if (!$axe2InheritsAxe1) {
            // All Axe2 are independent
            foreach ($allAxes2 as $axe2) {
                if (!str_starts_with($axe2->getCode(), '_IND_')) {
                    $axe2Node = [
                        'entity' => $axe2,
                        'level' => 'axe2',
                        'children' => [],
                    ];

                    if ($axe3InheritsAxe2 && isset($axes3ByAxe2[$axe2->getId()])) {
                        foreach ($axes3ByAxe2[$axe2->getId()] as $axe3) {
                            if (!str_starts_with($axe3->getCode(), '_IND_')) {
                                $axe2Node['children'][] = [
                                    'entity' => $axe3,
                                    'level' => 'axe3',
                                    'children' => [],
                                ];
                            }
                        }
                    }

                    $independentAxes2[] = $axe2Node;
                }
            }
        }

        if (!$axe3InheritsAxe2) {
            // All Axe3 are independent
            foreach ($allAxes3 as $axe3) {
                if (!str_starts_with($axe3->getCode(), '_IND_')) {
                    $independentAxes3[] = [
                        'entity' => $axe3,
                        'level' => 'axe3',
                        'children' => [],
                    ];
                }
            }
        }

        return [
            'sections' => $tree,
            'independent' => [
                'axe1' => $independentAxes1,
                'axe2' => $independentAxes2,
                'axe3' => $independentAxes3,
            ],
            'inheritance' => [
                'axe1InheritsSection' => $axe1InheritsSection,
                'axe2InheritsAxe1' => $axe2InheritsAxe1,
                'axe3InheritsAxe2' => $axe3InheritsAxe2,
            ],
        ];
    }

    /**
     * Search the tree by code or libelle.
     * Returns a filtered tree keeping parents of matching items for context.
     */
    public function searchTree(string $query): array
    {
        $fullTree = $this->buildTree();
        $query = mb_strtolower(trim($query));

        if ($query === '') {
            return $fullTree;
        }

        $filteredSections = [];

        foreach ($fullTree['sections'] as $sectionNode) {
            $filteredSection = $this->filterNode($sectionNode, $query);
            if ($filteredSection !== null) {
                $filteredSections[] = $filteredSection;
            }
        }

        $filteredIndependent = [
            'axe1' => [],
            'axe2' => [],
            'axe3' => [],
        ];

        foreach ($fullTree['independent']['axe1'] as $axe1Node) {
            $filtered = $this->filterNode($axe1Node, $query);
            if ($filtered !== null) {
                $filteredIndependent['axe1'][] = $filtered;
            }
        }

        foreach ($fullTree['independent']['axe2'] as $axe2Node) {
            $filtered = $this->filterNode($axe2Node, $query);
            if ($filtered !== null) {
                $filteredIndependent['axe2'][] = $filtered;
            }
        }

        foreach ($fullTree['independent']['axe3'] as $axe3Node) {
            $filtered = $this->filterNode($axe3Node, $query);
            if ($filtered !== null) {
                $filteredIndependent['axe3'][] = $filtered;
            }
        }

        return [
            'sections' => $filteredSections,
            'independent' => $filteredIndependent,
            'inheritance' => $fullTree['inheritance'],
        ];
    }

    /**
     * Filter a node and its children recursively.
     * Returns the node if it matches or has matching children, null otherwise.
     */
    private function filterNode(array $node, string $query): ?array
    {
        $entity = $node['entity'];
        $code = mb_strtolower($entity->getCode());
        $libelle = mb_strtolower($entity->getLibelle());

        $matches = str_contains($code, $query) || str_contains($libelle, $query);

        // Filter children
        $filteredChildren = [];
        foreach ($node['children'] as $childNode) {
            $filteredChild = $this->filterNode($childNode, $query);
            if ($filteredChild !== null) {
                $filteredChildren[] = $filteredChild;
            }
        }

        // Include this node if it matches or has matching children
        if ($matches || !empty($filteredChildren)) {
            return [
                'entity' => $entity,
                'level' => $node['level'],
                'children' => $matches ? $node['children'] : $filteredChildren,
                'highlighted' => $matches,
            ];
        }

        return null;
    }

    /**
     * Get the next level name for adding children.
     */
    public function getNextLevel(string $level): ?string
    {
        return match ($level) {
            'section' => 'axe1',
            'axe1' => 'axe2',
            'axe2' => 'axe3',
            default => null,
        };
    }

    /**
     * Check if a level can have children.
     */
    public function canHaveChildren(string $level): bool
    {
        $inheritance = [
            'axe1InheritsSection' => $this->configurationRepository->getValue('axe1_inherits_section', '1') === '1',
            'axe2InheritsAxe1' => $this->configurationRepository->getValue('axe2_inherits_axe1', '1') === '1',
            'axe3InheritsAxe2' => $this->configurationRepository->getValue('axe3_inherits_axe2', '1') === '1',
        ];

        return match ($level) {
            'section' => $inheritance['axe1InheritsSection'],
            'axe1' => $inheritance['axe2InheritsAxe1'],
            'axe2' => $inheritance['axe3InheritsAxe2'],
            default => false,
        };
    }
}
