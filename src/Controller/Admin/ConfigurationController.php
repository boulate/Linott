<?php

namespace App\Controller\Admin;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/configuration')]
class ConfigurationController extends AbstractController
{
    public function __construct(
        private ConfigurationRepository $configurationRepository,
        private EntityManagerInterface $entityManager
    ) {
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
