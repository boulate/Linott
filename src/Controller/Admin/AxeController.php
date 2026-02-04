<?php

namespace App\Controller\Admin;

use App\Entity\Axe1;
use App\Entity\Axe2;
use App\Entity\Axe3;
use App\Entity\Section;
use App\Repository\Axe1Repository;
use App\Repository\Axe2Repository;
use App\Repository\Axe3Repository;
use App\Repository\ConfigurationRepository;
use App\Repository\SectionRepository;
use App\Service\AxeTreeService;
use App\Service\LabelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/axes')]
class AxeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SectionRepository $sectionRepository,
        private Axe1Repository $axe1Repository,
        private Axe2Repository $axe2Repository,
        private Axe3Repository $axe3Repository,
        private ConfigurationRepository $configurationRepository,
        private LabelService $labelService,
        private AxeTreeService $axeTreeService
    ) {
    }

    #[Route('', name: 'app_admin_axes')]
    public function index(): Response
    {
        $tree = $this->axeTreeService->buildTree();

        return $this->render('admin/axes/index.html.twig', [
            'tree' => $tree,
        ]);
    }

    #[Route('/search', name: 'app_admin_axes_search')]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $tree = $this->axeTreeService->searchTree($query);

        return $this->render('admin/axes/_tree.html.twig', [
            'tree' => $tree,
            'searchQuery' => $query,
        ]);
    }

    #[Route('/panel/new/{level}', name: 'app_admin_axes_panel_new')]
    public function panelNew(string $level, Request $request): Response
    {
        $parentId = $request->query->get('parentId');
        $showParentField = false;
        $parents = [];

        // Get inheritance configuration
        $isIndependent = match ($level) {
            'axe1' => $this->configurationRepository->getValue('axe1_inherits_section', '1') !== '1',
            'axe2' => $this->configurationRepository->getValue('axe2_inherits_axe1', '1') !== '1',
            'axe3' => $this->configurationRepository->getValue('axe3_inherits_axe2', '1') !== '1',
            default => false,
        };

        // For axes, get parent options if not independent
        if ($level !== 'section' && !$isIndependent) {
            $showParentField = true;
            $parents = match ($level) {
                'axe1' => $this->sectionRepository->findBy(['actif' => true], ['ordre' => 'ASC']),
                'axe2' => $this->axe1Repository->findBy(['actif' => true], ['ordre' => 'ASC']),
                'axe3' => $this->axe2Repository->findBy(['actif' => true], ['ordre' => 'ASC']),
                default => [],
            };
        }

        return $this->render('admin/axes/_side_panel.html.twig', [
            'level' => $level,
            'entity' => null,
            'isNew' => true,
            'showParentField' => $showParentField,
            'parents' => $parents,
            'parentId' => $parentId,
            'couleurs' => Section::COULEURS,
        ]);
    }

    #[Route('/panel/edit/{level}/{id}', name: 'app_admin_axes_panel_edit')]
    public function panelEdit(string $level, int $id): Response
    {
        $entity = $this->getEntity($level, $id);

        if (!$entity) {
            throw $this->createNotFoundException('Element non trouve.');
        }

        $showParentField = false;
        $parents = [];
        $parentId = null;

        // Get inheritance configuration
        $isIndependent = match ($level) {
            'axe1' => $this->configurationRepository->getValue('axe1_inherits_section', '1') !== '1',
            'axe2' => $this->configurationRepository->getValue('axe2_inherits_axe1', '1') !== '1',
            'axe3' => $this->configurationRepository->getValue('axe3_inherits_axe2', '1') !== '1',
            default => false,
        };

        // For axes, get parent options if not independent
        if ($level !== 'section' && !$isIndependent) {
            $showParentField = true;
            $parents = match ($level) {
                'axe1' => $this->sectionRepository->findBy(['actif' => true], ['ordre' => 'ASC']),
                'axe2' => $this->axe1Repository->findBy(['actif' => true], ['ordre' => 'ASC']),
                'axe3' => $this->axe2Repository->findBy(['actif' => true], ['ordre' => 'ASC']),
                default => [],
            };

            $parentId = match ($level) {
                'axe1' => $entity->getSection()?->getId(),
                'axe2' => $entity->getAxe1()?->getId(),
                'axe3' => $entity->getAxe2()?->getId(),
                default => null,
            };
        }

        return $this->render('admin/axes/_side_panel.html.twig', [
            'level' => $level,
            'entity' => $entity,
            'isNew' => false,
            'showParentField' => $showParentField,
            'parents' => $parents,
            'parentId' => $parentId,
            'couleurs' => Section::COULEURS,
        ]);
    }

    #[Route('/save/{level}/{id?}', name: 'app_admin_axes_save', methods: ['POST'])]
    public function save(string $level, ?int $id, Request $request): Response
    {
        $isNew = $id === null;

        if ($isNew) {
            $entity = $this->createEntity($level);
        } else {
            $entity = $this->getEntity($level, $id);
            if (!$entity) {
                throw $this->createNotFoundException('Element non trouve.');
            }
        }

        // Update entity from form data
        $entity->setCode($request->request->get('code'));
        $entity->setLibelle($request->request->get('libelle'));
        $entity->setOrdre((int) $request->request->get('ordre', 0));
        $entity->setActif($request->request->has('actif'));

        // Handle color for sections
        if ($level === 'section') {
            $entity->setCouleur($request->request->get('couleur'));
        }

        // Handle parent for axes
        if ($level !== 'section') {
            $parentId = $request->request->get('parent');
            $isIndependent = match ($level) {
                'axe1' => $this->configurationRepository->getValue('axe1_inherits_section', '1') !== '1',
                'axe2' => $this->configurationRepository->getValue('axe2_inherits_axe1', '1') !== '1',
                'axe3' => $this->configurationRepository->getValue('axe3_inherits_axe2', '1') !== '1',
                default => false,
            };

            if ($isIndependent) {
                // Set to independent placeholder parent
                $parent = match ($level) {
                    'axe1' => $this->sectionRepository->findIndependentDefault(),
                    'axe2' => $this->axe1Repository->findIndependentDefault(),
                    'axe3' => $this->axe2Repository->findIndependentDefault(),
                    default => null,
                };
            } else {
                $parent = match ($level) {
                    'axe1' => $this->sectionRepository->find($parentId),
                    'axe2' => $this->axe1Repository->find($parentId),
                    'axe3' => $this->axe2Repository->find($parentId),
                    default => null,
                };
            }

            if ($parent) {
                match ($level) {
                    'axe1' => $entity->setSection($parent),
                    'axe2' => $entity->setAxe1($parent),
                    'axe3' => $entity->setAxe2($parent),
                    default => null,
                };
            }
        }

        if ($isNew) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        $levelLabel = $level === 'section' ? 'Section' : $this->labelService->getLabel($level);
        $this->addFlash('success', $levelLabel . ($isNew ? ' cree.' : ' modifie.'));

        // Return updated tree
        $tree = $this->axeTreeService->buildTree();

        return $this->render('admin/axes/_tree.html.twig', [
            'tree' => $tree,
        ]);
    }

    #[Route('/delete/{level}/{id}', name: 'app_admin_axes_delete', methods: ['POST'])]
    public function delete(string $level, int $id): Response
    {
        $entity = $this->getEntity($level, $id);

        if (!$entity) {
            throw $this->createNotFoundException('Element non trouve.');
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $levelLabel = $level === 'section' ? 'Section' : $this->labelService->getLabel($level);
        $this->addFlash('success', $levelLabel . ' supprime.');

        // Return updated tree
        $tree = $this->axeTreeService->buildTree();

        return $this->render('admin/axes/_tree.html.twig', [
            'tree' => $tree,
        ]);
    }

    #[Route('/toggle-inheritance/{level}', name: 'app_admin_axes_toggle_inheritance', methods: ['POST'])]
    public function toggleInheritance(string $level): Response
    {
        $configKeys = [
            'axe1' => 'axe1_inherits_section',
            'axe2' => 'axe2_inherits_axe1',
            'axe3' => 'axe3_inherits_axe2',
        ];

        if (!isset($configKeys[$level])) {
            throw $this->createNotFoundException('Niveau invalide.');
        }

        $key = $configKeys[$level];
        $currentValue = $this->configurationRepository->getValue($key, '1') === '1';
        $newValue = $currentValue ? '0' : '1';

        $parentLabels = [
            'axe1' => 'section',
            'axe2' => 'axe1',
            'axe3' => 'axe2',
        ];

        $description = sprintf(
            '%s herite de %s dans les filtres',
            $this->labelService->getLabel($level),
            $this->labelService->getLabel($parentLabels[$level])
        );

        $this->configurationRepository->setValue($key, $newValue, $description);

        if ($newValue === '1') {
            $this->addFlash('success', $this->labelService->getLabel($level) . ' herite maintenant de son parent.');
        } else {
            $this->addFlash('success', $this->labelService->getLabel($level) . ' est maintenant independant.');
        }

        return $this->redirectToRoute('app_admin_axes');
    }

    // Legacy routes for backwards compatibility (kept for existing forms)
    #[Route('/axe1/new', name: 'app_admin_axe1_new')]
    public function newAxe1(Request $request): Response
    {
        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe1/{id}/edit', name: 'app_admin_axe1_edit')]
    public function editAxe1(Axe1 $axe1): Response
    {
        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe1/{id}/delete', name: 'app_admin_axe1_delete', methods: ['POST'])]
    public function deleteAxe1(Axe1 $axe1, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $axe1->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($axe1);
            $this->entityManager->flush();
            $this->addFlash('success', $this->labelService->getLabel('axe1') . ' supprime.');
        }

        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe2/new', name: 'app_admin_axe2_new')]
    public function newAxe2(Request $request): Response
    {
        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe2/{id}/edit', name: 'app_admin_axe2_edit')]
    public function editAxe2(Axe2 $axe2): Response
    {
        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe2/{id}/delete', name: 'app_admin_axe2_delete', methods: ['POST'])]
    public function deleteAxe2(Axe2 $axe2, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $axe2->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($axe2);
            $this->entityManager->flush();
            $this->addFlash('success', $this->labelService->getLabel('axe2') . ' supprime.');
        }

        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe3/new', name: 'app_admin_axe3_new')]
    public function newAxe3(Request $request): Response
    {
        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe3/{id}/edit', name: 'app_admin_axe3_edit')]
    public function editAxe3(Axe3 $axe3): Response
    {
        return $this->redirectToRoute('app_admin_axes');
    }

    #[Route('/axe3/{id}/delete', name: 'app_admin_axe3_delete', methods: ['POST'])]
    public function deleteAxe3(Axe3 $axe3, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $axe3->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($axe3);
            $this->entityManager->flush();
            $this->addFlash('success', $this->labelService->getLabel('axe3') . ' supprime.');
        }

        return $this->redirectToRoute('app_admin_axes');
    }

    private function getEntity(string $level, int $id): Section|Axe1|Axe2|Axe3|null
    {
        return match ($level) {
            'section' => $this->sectionRepository->find($id),
            'axe1' => $this->axe1Repository->find($id),
            'axe2' => $this->axe2Repository->find($id),
            'axe3' => $this->axe3Repository->find($id),
            default => null,
        };
    }

    private function createEntity(string $level): Section|Axe1|Axe2|Axe3
    {
        return match ($level) {
            'section' => new Section(),
            'axe1' => new Axe1(),
            'axe2' => new Axe2(),
            'axe3' => new Axe3(),
            default => throw new \InvalidArgumentException('Invalid level'),
        };
    }
}
