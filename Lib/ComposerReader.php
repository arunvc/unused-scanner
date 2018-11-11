<?php
declare(strict_types=1);

namespace insolita\Scanner\Lib;

use function array_filter;
use function mb_strpos;

final class ComposerReader
{
    /**
     * @var Config
     */
    private $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    public function fetchDependencies(): array
    {
        $composerData = $this->readComposerJson();
        $packages = $composerData['require'];
        if ($this->config->getRequireDev()===true) {
            $packages = array_merge($packages, $composerData['require-dev'] ?? []);
        }
        $packages = array_keys($packages);
        return array_filter($packages, function ($package) {
            return mb_strpos($package, '/') !== false;
        });
    }
    
    private function readComposerJson(): array
    {
        $file = file_get_contents($this->config->getComposerJsonPath());
        return $file ? json_decode($file, true) : [];
    }
}
