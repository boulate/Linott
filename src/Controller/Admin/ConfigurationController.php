<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use App\Service\LabelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/configuration')]
class ConfigurationController extends AbstractController
{
    private const MODULES = [
        'module_dashboard' => ['label' => 'Tableau de bord', 'description' => 'Page d\'accueil avec statistiques'],
        'module_calendrier' => ['label' => 'Calendrier', 'description' => 'Gestion des conges et absences'],
        'module_stats' => ['label' => 'Statistiques', 'description' => 'Rapports et visualisations'],
    ];

    public function __construct(
        private ConfigurationRepository $configurationRepository,
        private EntityManagerInterface $entityManager,
        private LabelService $labelService
    ) {
    }

    #[Route('/modules', name: 'app_admin_modules')]
    public function modules(): Response
    {
        $modules = [];
        foreach (self::MODULES as $cle => $info) {
            $config = $this->configurationRepository->findByCle($cle);
            $modules[$cle] = [
                'label' => $info['label'],
                'description' => $info['description'],
                'actif' => $config ? $config->getValeurAsBool() : false,
            ];
        }

        return $this->render('admin/configuration/modules.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/modules/edit', name: 'app_admin_modules_edit', methods: ['POST'])]
    public function modulesEdit(Request $request): Response
    {
        if ($this->isCsrfTokenValid('modules_edit', $request->request->get('_token'))) {
            $activeModules = $request->request->all('modules');

            foreach (self::MODULES as $cle => $info) {
                $valeur = isset($activeModules[$cle]) ? '1' : '0';
                $this->configurationRepository->setValue($cle, $valeur, $info['description']);
            }

            $this->addFlash('success', 'Modules mis a jour.');
        }

        return $this->redirectToRoute('app_admin_modules');
    }

    #[Route('/labels', name: 'app_admin_labels')]
    public function labels(): Response
    {
        $configKeys = LabelService::getConfigKeys();
        $defaults = LabelService::getDefaults();

        $labels = [];
        foreach ($configKeys as $level => $configKey) {
            $config = $this->configurationRepository->findByCle($configKey);
            $labels[$level] = [
                'key' => $configKey,
                'default' => $defaults[$level],
                'value' => $config?->getValeur() ?? '',
                'current' => $this->labelService->getLabel($level),
            ];
        }

        return $this->render('admin/configuration/labels.html.twig', [
            'labels' => $labels,
        ]);
    }

    #[Route('/labels/edit', name: 'app_admin_labels_edit', methods: ['POST'])]
    public function labelsEdit(Request $request): Response
    {
        if ($this->isCsrfTokenValid('labels_edit', $request->request->get('_token'))) {
            $values = $request->request->all('labels');
            $configKeys = LabelService::getConfigKeys();
            $descriptions = [
                'section' => 'Label personnalise pour le niveau Section',
                'axe1' => 'Label personnalise pour le niveau Axe 1',
                'axe2' => 'Label personnalise pour le niveau Axe 2',
                'axe3' => 'Label personnalise pour le niveau Axe 3',
            ];

            foreach ($configKeys as $level => $configKey) {
                $value = trim($values[$level] ?? '');
                // On stocke vide si c'est la valeur par défaut ou vide
                $this->configurationRepository->setValue(
                    $configKey,
                    $value !== '' ? $value : null,
                    $descriptions[$level]
                );
            }

            $this->labelService->clearCache();
            $this->addFlash('success', 'Noms des axes mis a jour.');
        }

        return $this->redirectToRoute('app_admin_labels');
    }

    #[Route('', name: 'app_admin_configuration')]
    public function index(): Response
    {
        return $this->render('admin/configuration/index.html.twig', [
            'configurations' => $this->configurationRepository->findBy([], ['cle' => 'ASC']),
        ]);
    }

    #[Route('/edit', name: 'app_admin_configuration_edit', methods: ['POST'])]
    public function edit(Request $request): Response
    {
        if ($this->isCsrfTokenValid('config_edit', $request->request->get('_token'))) {
            $values = $request->request->all('config');

            foreach ($values as $cle => $valeur) {
                $config = $this->configurationRepository->findOneBy(['cle' => $cle]);
                if ($config) {
                    $config->setValeur($valeur);
                }
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Configuration enregistrée.');
        }

        return $this->redirectToRoute('app_admin_configuration');
    }

    #[Route('/new', name: 'app_admin_configuration_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('config_new', $request->request->get('_token'))) {
                $config = new Configuration();
                $config->setCle($request->request->get('cle'));
                $config->setValeur($request->request->get('valeur'));
                $config->setDescription($request->request->get('description'));

                $this->entityManager->persist($config);
                $this->entityManager->flush();

                $this->addFlash('success', 'Paramètre ajouté.');
                return $this->redirectToRoute('app_admin_configuration');
            }
        }

        return $this->render('admin/configuration/new.html.twig');
    }

    #[Route('/{id}/delete', name: 'app_admin_configuration_delete', methods: ['POST'])]
    public function delete(Configuration $configuration, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $configuration->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($configuration);
            $this->entityManager->flush();
            $this->addFlash('success', 'Paramètre supprimé.');
        }

        return $this->redirectToRoute('app_admin_configuration');
    }
}
