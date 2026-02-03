<?php

namespace App\Twig;

use App\Repository\ConfigurationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigurationExtension extends AbstractExtension
{
    public function __construct(
        private ConfigurationRepository $configurationRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('config', [$this, 'getConfig']),
            new TwigFunction('config_bool', [$this, 'getConfigBool']),
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
}
