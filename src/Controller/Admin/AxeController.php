<?php

namespace App\Controller\Admin;

use App\Entity\Axe1;
use App\Entity\Axe2;
use App\Entity\Axe3;
use App\Form\Admin\Axe1Type;
use App\Form\Admin\Axe2Type;
use App\Form\Admin\Axe3Type;
use App\Repository\Axe1Repository;
use App\Repository\Axe2Repository;
use App\Repository\Axe3Repository;
use App\Repository\ConfigurationRepository;
use App\Repository\SectionRepository;
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
        private LabelService $labelService
    ) {
    }

    #[Route('', name: 'app_admin_axes')]
    public function index(Request $request): Response
    {
        $sectionId = $request->query->get('section');
        $axe1Id = $request->query->get('axe1');
        $axe2Id = $request->query->get('axe2');

        // Get inheritance configuration
        $axe1InheritsSection = $this->configurationRepository->getValue('axe1_inherits_section', '1') === '1';
        $axe2InheritsAxe1 = $this->configurationRepository->getValue('axe2_inherits_axe1', '1') === '1';
        $axe3InheritsAxe2 = $this->configurationRepository->getValue('axe3_inherits_axe2', '1') === '1';

        $sections = $this->sectionRepository->findBy(['actif' => true], ['ordre' => 'ASC']);
        $selectedSection = $sectionId ? $this->sectionRepository->find($sectionId) : ($sections[0] ?? null);

        // Axe1: show all if independent, otherwise filter by section
        if ($axe1InheritsSection) {
            $axes1 = $selectedSection
                ? $this->axe1Repository->findBy(['section' => $selectedSection], ['ordre' => 'ASC'])
                : [];
        } else {
            $axes1 = $this->axe1Repository->findBy([], ['ordre' => 'ASC']);
        }
        $selectedAxe1 = $axe1Id ? $this->axe1Repository->find($axe1Id) : null;

        // Axe2: show all if independent, otherwise filter by axe1
        if ($axe2InheritsAxe1) {
            $axes2 = $selectedAxe1
                ? $this->axe2Repository->findBy(['axe1' => $selectedAxe1], ['ordre' => 'ASC'])
                : [];
        } else {
            $axes2 = $this->axe2Repository->findBy([], ['ordre' => 'ASC']);
        }
        $selectedAxe2 = $axe2Id ? $this->axe2Repository->find($axe2Id) : null;

        // Axe3: show all if independent, otherwise filter by axe2
        if ($axe3InheritsAxe2) {
            $axes3 = $selectedAxe2
                ? $this->axe3Repository->findBy(['axe2' => $selectedAxe2], ['ordre' => 'ASC'])
                : [];
        } else {
            $axes3 = $this->axe3Repository->findBy([], ['ordre' => 'ASC']);
        }

        return $this->render('admin/axes/index.html.twig', [
            'sections' => $sections,
            'selectedSection' => $selectedSection,
            'axes1' => $axes1,
            'selectedAxe1' => $selectedAxe1,
            'axes2' => $axes2,
            'selectedAxe2' => $selectedAxe2,
            'axes3' => $axes3,
            'axe1InheritsSection' => $axe1InheritsSection,
            'axe2InheritsAxe1' => $axe2InheritsAxe1,
            'axe3InheritsAxe2' => $axe3InheritsAxe2,
        ]);
    }

    #[Route('/toggle-inheritance/{level}', name: 'app_admin_axes_toggle_inheritance', methods: ['POST'])]
    public function toggleInheritance(string $level, Request $request): Response
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
            '%s hérite de %s dans les filtres',
            $this->labelService->getLabel($level),
            $this->labelService->getLabel($parentLabels[$level])
        );

        $this->configurationRepository->setValue($key, $newValue, $description);

        if ($newValue === '1') {
            $this->addFlash('success', $this->labelService->getLabel($level) . ' hérite maintenant de son parent.');
        } else {
            $this->addFlash('success', $this->labelService->getLabel($level) . ' est maintenant indépendant.');
        }

        return $this->redirectToRoute('app_admin_axes');
    }

    // Axe1 CRUD
    #[Route('/axe1/new', name: 'app_admin_axe1_new')]
    public function newAxe1(Request $request): Response
    {
        $axe1 = new Axe1();
        $isIndependent = $this->configurationRepository->getValue('axe1_inherits_section', '1') !== '1';

        // Only pre-fill parent if NOT independent
        if (!$isIndependent && $sectionId = $request->query->get('section')) {
            $axe1->setSection($this->sectionRepository->find($sectionId));
        }

        $form = $this->createForm(Axe1Type::class, $axe1, ['is_independent' => $isIndependent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // In independent mode, ALWAYS use _IND_ parent
            if ($isIndependent) {
                $defaultSection = $this->sectionRepository->findIndependentDefault();
                if ($defaultSection) {
                    $axe1->setSection($defaultSection);
                }
            }

            $this->entityManager->persist($axe1);
            $this->entityManager->flush();

            $this->addFlash('success', $this->labelService->getLabel('axe1') . ' créé.');
            return $this->redirectToRoute('app_admin_axes');
        }

        return $this->render('admin/axes/form_axe1.html.twig', [
            'form' => $form,
            'axe1' => $axe1,
            'isNew' => true,
            'label' => $this->labelService->getLabel('axe1'),
        ]);
    }

    #[Route('/axe1/{id}/edit', name: 'app_admin_axe1_edit')]
    public function editAxe1(Axe1 $axe1, Request $request): Response
    {
        $isIndependent = $this->configurationRepository->getValue('axe1_inherits_section', '1') !== '1';
        $form = $this->createForm(Axe1Type::class, $axe1, ['is_independent' => $isIndependent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', $this->labelService->getLabel('axe1') . ' modifié.');
            return $this->redirectToRoute('app_admin_axes');
        }

        return $this->render('admin/axes/form_axe1.html.twig', [
            'form' => $form,
            'axe1' => $axe1,
            'isNew' => false,
            'label' => $this->labelService->getLabel('axe1'),
        ]);
    }

    #[Route('/axe1/{id}/delete', name: 'app_admin_axe1_delete', methods: ['POST'])]
    public function deleteAxe1(Axe1 $axe1, Request $request): Response
    {
        $sectionId = $axe1->getSection()->getId();
        if ($this->isCsrfTokenValid('delete' . $axe1->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($axe1);
            $this->entityManager->flush();
            $this->addFlash('success', $this->labelService->getLabel('axe1') . ' supprimé.');
        }

        return $this->redirectToRoute('app_admin_axes', ['section' => $sectionId]);
    }

    // Axe2 CRUD
    #[Route('/axe2/new', name: 'app_admin_axe2_new')]
    public function newAxe2(Request $request): Response
    {
        $axe2 = new Axe2();
        $isIndependent = $this->configurationRepository->getValue('axe2_inherits_axe1', '1') !== '1';

        // Only pre-fill parent if NOT independent
        if (!$isIndependent && $axe1Id = $request->query->get('axe1')) {
            $axe2->setAxe1($this->axe1Repository->find($axe1Id));
        }

        $form = $this->createForm(Axe2Type::class, $axe2, ['is_independent' => $isIndependent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // In independent mode, ALWAYS use _IND_ parent
            if ($isIndependent) {
                $defaultAxe1 = $this->axe1Repository->findIndependentDefault();
                if ($defaultAxe1) {
                    $axe2->setAxe1($defaultAxe1);
                }
            }

            $this->entityManager->persist($axe2);
            $this->entityManager->flush();

            $this->addFlash('success', $this->labelService->getLabel('axe2') . ' créé.');
            return $this->redirectToRoute('app_admin_axes');
        }

        return $this->render('admin/axes/form_axe2.html.twig', [
            'form' => $form,
            'axe2' => $axe2,
            'isNew' => true,
            'label' => $this->labelService->getLabel('axe2'),
        ]);
    }

    #[Route('/axe2/{id}/edit', name: 'app_admin_axe2_edit')]
    public function editAxe2(Axe2 $axe2, Request $request): Response
    {
        $isIndependent = $this->configurationRepository->getValue('axe2_inherits_axe1', '1') !== '1';
        $form = $this->createForm(Axe2Type::class, $axe2, ['is_independent' => $isIndependent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', $this->labelService->getLabel('axe2') . ' modifié.');
            return $this->redirectToRoute('app_admin_axes');
        }

        return $this->render('admin/axes/form_axe2.html.twig', [
            'form' => $form,
            'axe2' => $axe2,
            'isNew' => false,
            'label' => $this->labelService->getLabel('axe2'),
        ]);
    }

    #[Route('/axe2/{id}/delete', name: 'app_admin_axe2_delete', methods: ['POST'])]
    public function deleteAxe2(Axe2 $axe2, Request $request): Response
    {
        $sectionId = $axe2->getAxe1()->getSection()->getId();
        $axe1Id = $axe2->getAxe1()->getId();
        if ($this->isCsrfTokenValid('delete' . $axe2->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($axe2);
            $this->entityManager->flush();
            $this->addFlash('success', $this->labelService->getLabel('axe2') . ' supprimé.');
        }

        return $this->redirectToRoute('app_admin_axes', ['section' => $sectionId, 'axe1' => $axe1Id]);
    }

    // Axe3 CRUD
    #[Route('/axe3/new', name: 'app_admin_axe3_new')]
    public function newAxe3(Request $request): Response
    {
        $axe3 = new Axe3();
        $isIndependent = $this->configurationRepository->getValue('axe3_inherits_axe2', '1') !== '1';

        // Only pre-fill parent if NOT independent
        if (!$isIndependent && $axe2Id = $request->query->get('axe2')) {
            $axe3->setAxe2($this->axe2Repository->find($axe2Id));
        }

        $form = $this->createForm(Axe3Type::class, $axe3, ['is_independent' => $isIndependent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // In independent mode, ALWAYS use _IND_ parent
            if ($isIndependent) {
                $defaultAxe2 = $this->axe2Repository->findIndependentDefault();
                if ($defaultAxe2) {
                    $axe3->setAxe2($defaultAxe2);
                }
            }

            $this->entityManager->persist($axe3);
            $this->entityManager->flush();

            $this->addFlash('success', $this->labelService->getLabel('axe3') . ' créé.');
            return $this->redirectToRoute('app_admin_axes');
        }

        return $this->render('admin/axes/form_axe3.html.twig', [
            'form' => $form,
            'axe3' => $axe3,
            'isNew' => true,
            'label' => $this->labelService->getLabel('axe3'),
        ]);
    }

    #[Route('/axe3/{id}/edit', name: 'app_admin_axe3_edit')]
    public function editAxe3(Axe3 $axe3, Request $request): Response
    {
        $isIndependent = $this->configurationRepository->getValue('axe3_inherits_axe2', '1') !== '1';
        $form = $this->createForm(Axe3Type::class, $axe3, ['is_independent' => $isIndependent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', $this->labelService->getLabel('axe3') . ' modifié.');
            return $this->redirectToRoute('app_admin_axes');
        }

        return $this->render('admin/axes/form_axe3.html.twig', [
            'form' => $form,
            'axe3' => $axe3,
            'isNew' => false,
            'label' => $this->labelService->getLabel('axe3'),
        ]);
    }

    #[Route('/axe3/{id}/delete', name: 'app_admin_axe3_delete', methods: ['POST'])]
    public function deleteAxe3(Axe3 $axe3, Request $request): Response
    {
        $axe2 = $axe3->getAxe2();
        $sectionId = $axe2->getAxe1()->getSection()->getId();
        $axe1Id = $axe2->getAxe1()->getId();
        $axe2Id = $axe2->getId();

        if ($this->isCsrfTokenValid('delete' . $axe3->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($axe3);
            $this->entityManager->flush();
            $this->addFlash('success', $this->labelService->getLabel('axe3') . ' supprimé.');
        }

        return $this->redirectToRoute('app_admin_axes', ['section' => $sectionId, 'axe1' => $axe1Id, 'axe2' => $axe2Id]);
    }
}
