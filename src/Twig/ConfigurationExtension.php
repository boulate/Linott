<?php

namespace App\Twig;

use App\Repository\ConfigurationRepository;
use App\Service\LabelService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigurationExtension extends AbstractExtension
{
    public function __construct(
        private ConfigurationRepository $configurationRepository,
        private LabelService $labelService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('config', [$this, 'getConfig']),
            new TwigFunction('config_bool', [$this, 'getConfigBool']),
            new TwigFunction('axe_label', [$this, 'getAxeLabel']),
            new TwigFunction('axe_labels', [$this, 'getAllAxeLabels']),
            new TwigFunction('axe_placeholder', [$this, 'getAxePlaceholder']),
        ];
    }

    public function getConfig(string $cle, ?string $default = null): ?string
    {
        return $this->configurationRepository->getValue($cle, $default);
    }

    public function getConfigBool(string $cle, bool $default = false): bool
    {
        $value = $this->configurationRepository->getValue($cle);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Récupère le label personnalisé pour un niveau d'axe.
     *
     * @param string $level 'section', 'axe1', 'axe2' ou 'axe3'
     */
    public function getAxeLabel(string $level): string
    {
        return $this->labelService->getLabel($level);
    }

    /**
     * Récupère tous les labels des axes.
     *
     * @return array{section: string, axe1: string, axe2: string, axe3: string}
     */
    public function getAllAxeLabels(): array
    {
        return $this->labelService->getAllLabels();
    }

    /**
     * Récupère le placeholder pour un sélecteur d'axe.
     *
     * @param string $level 'section', 'axe1', 'axe2' ou 'axe3'
     */
    public function getAxePlaceholder(string $level): string
    {
        return $this->labelService->getPlaceholder($level);
    }
}
