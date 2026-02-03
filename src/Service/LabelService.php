<?php

namespace App\Service;

use App\Repository\ConfigurationRepository;

/**
 * Service pour gérer les labels personnalisables des axes analytiques.
 * Permet de remplacer "Section", "Axe 1", "Axe 2", "Axe 3" par des noms métier.
 */
class LabelService
{
    private const DEFAULTS = [
        'section' => 'Section',
        'axe1' => 'Axe 1',
        'axe2' => 'Axe 2',
        'axe3' => 'Axe 3',
    ];

    private const CONFIG_KEYS = [
        'section' => 'label_section',
        'axe1' => 'label_axe1',
        'axe2' => 'label_axe2',
        'axe3' => 'label_axe3',
    ];

    private ?array $cache = null;

    public function __construct(
        private ConfigurationRepository $configurationRepository
    ) {
    }

    /**
     * Récupère le label pour un niveau d'axe.
     *
     * @param string $level 'section', 'axe1', 'axe2' ou 'axe3'
     */
    public function getLabel(string $level): string
    {
        $labels = $this->getAllLabels();

        return $labels[$level] ?? self::DEFAULTS[$level] ?? $level;
    }

    /**
     * Récupère tous les labels.
     *
     * @return array{section: string, axe1: string, axe2: string, axe3: string}
     */
    public function getAllLabels(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $this->cache = [];
        foreach (self::CONFIG_KEYS as $level => $configKey) {
            $value = $this->configurationRepository->getValue($configKey);
            $this->cache[$level] = $value ?: self::DEFAULTS[$level];
        }

        return $this->cache;
    }

    /**
     * Récupère le placeholder pour un sélecteur.
     *
     * @param string $level 'section', 'axe1', 'axe2' ou 'axe3'
     */
    public function getPlaceholder(string $level): string
    {
        $label = $this->getLabel($level);

        return sprintf('-- Choisir %s --', $this->addArticle($label));
    }

    /**
     * Ajoute l'article approprié (un/une) devant un label.
     */
    private function addArticle(string $label): string
    {
        $lower = mb_strtolower($label);

        // Mots communs féminins ou détection basique
        $feminins = ['section', 'catégorie', 'activité', 'mission', 'tâche', 'action', 'affaire'];
        foreach ($feminins as $feminin) {
            if (str_contains($lower, $feminin)) {
                return 'une ' . $label;
            }
        }

        return 'un ' . $label;
    }

    /**
     * Invalide le cache (après modification des labels).
     */
    public function clearCache(): void
    {
        $this->cache = null;
    }

    /**
     * Récupère les clés de configuration.
     */
    public static function getConfigKeys(): array
    {
        return self::CONFIG_KEYS;
    }

    /**
     * Récupère les valeurs par défaut.
     */
    public static function getDefaults(): array
    {
        return self::DEFAULTS;
    }
}
