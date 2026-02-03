<?php

namespace App\Controller\Api;

use App\Repository\Axe1Repository;
use App\Repository\Axe2Repository;
use App\Repository\Axe3Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class AxeController extends AbstractController
{
    public function __construct(
        private Axe1Repository $axe1Repository,
        private Axe2Repository $axe2Repository,
        private Axe3Repository $axe3Repository
    ) {
    }

    #[Route('/axes1', name: 'api_axes1')]
    public function getAxes1(Request $request): Response
    {
        $periode = $request->query->all('periode');
        $sectionId = $request->query->get('section') ?? ($periode['section'] ?? null);

        if (!$sectionId) {
            return $this->render('compta/_select_placeholder.html.twig', [
                'name' => 'periode[axe1]',
                'label' => 'Axe 1',
                'placeholder' => '-- Choisir d\'abord une section --',
                'disabled' => true,
                'dataAttribute' => 'axe1',
            ]);
        }

        $axes1 = $this->axe1Repository->findBySectionId((int) $sectionId);

        return $this->render('compta/_select_options.html.twig', [
            'name' => 'periode[axe1]',
            'label' => 'Axe 1',
            'placeholder' => '-- Choisir un axe 1 --',
            'options' => $axes1,
            'dataAttribute' => 'axe1',
            'htmxTarget' => '#axe2-container',
            'htmxUrl' => $this->generateUrl('api_axes2'),
            'htmxInclude' => 'this',
        ]);
    }

    #[Route('/axes2', name: 'api_axes2')]
    public function getAxes2(Request $request): Response
    {
        $periode = $request->query->all('periode');
        $axe1Id = $request->query->get('axe1') ?? ($periode['axe1'] ?? null);

        if (!$axe1Id) {
            return $this->render('compta/_select_placeholder.html.twig', [
                'name' => 'periode[axe2]',
                'label' => 'Axe 2',
                'placeholder' => '-- Choisir d\'abord un axe 1 --',
                'disabled' => true,
                'dataAttribute' => 'axe2',
            ]);
        }

        $axes2 = $this->axe2Repository->findByAxe1Id((int) $axe1Id);

        return $this->render('compta/_select_options.html.twig', [
            'name' => 'periode[axe2]',
            'label' => 'Axe 2',
            'placeholder' => '-- Choisir un axe 2 --',
            'options' => $axes2,
            'dataAttribute' => 'axe2',
            'htmxTarget' => '#axe3-container',
            'htmxUrl' => $this->generateUrl('api_axes3'),
            'htmxInclude' => 'this',
        ]);
    }

    #[Route('/axes3', name: 'api_axes3')]
    public function getAxes3(Request $request): Response
    {
        $periode = $request->query->all('periode');
        $axe2Id = $request->query->get('axe2') ?? ($periode['axe2'] ?? null);

        if (!$axe2Id) {
            return $this->render('compta/_select_placeholder.html.twig', [
                'name' => 'periode[axe3]',
                'label' => 'Axe 3',
                'placeholder' => '-- Choisir d\'abord un axe 2 --',
                'disabled' => true,
                'dataAttribute' => 'axe3',
            ]);
        }

        $axes3 = $this->axe3Repository->findByAxe2Id((int) $axe2Id);

        return $this->render('compta/_select_options.html.twig', [
            'name' => 'periode[axe3]',
            'label' => 'Axe 3',
            'placeholder' => '-- Choisir un axe 3 --',
            'options' => $axes3,
            'dataAttribute' => 'axe3',
        ]);
    }
}
