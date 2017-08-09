<?php
namespace PackageFactory\Hyphenate\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Utility\Environment;
use Neos\Flow\I18n\Service as LocalizationService;
use Neos\Flow\Package\PackageManagerInterface;
use Neos\Utility\Files;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class HyphenateImplementation extends AbstractFusionObject
{
    /**
     * @Flow\Inject
     * @var Environment
     */
    protected $environment;

    /**
     * @Flow\Inject
     * @var PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @Flow\inject
     * @var LocalizationService
     */
    protected $localizationService;

    public function getContent()
    {
        return $this->fusionValue('content');
    }

    public function getLocale()
    {
        if ($locale = $this->fusionValue('locale')) {
            return $locale;
        }

        return (string) $this->localizationService->getConfiguration()->getCurrentLocale();
    }

    public function getThreshold()
    {
        return $this->fusionValue('threshold');
    }

    public function evaluate()
    {
        $package = $this->packageManager->getPackage('vanderlee.syllable');
        $languagesDirectory = Files::concatenatePaths([
            $package->getPackagePath(),
            'languages'
        ]);
        $cacheDirectory = Files::concatenatePaths([
            $this->environment->getPathToTemporaryDirectory(),
            (string) $this->environment->getContext(),
            'PackageFactory_Hyphenate_Language_Cache'
        ]);

        Files::createDirectoryRecursively($cacheDirectory);

        $syllable = new \Syllable($this->getLocale());

	    $syllable->getSource()->setPath($languagesDirectory);
        $syllable->getCache()->setPath($cacheDirectory);

        $syllable->setHyphen(new \Syllable_Hyphen_Soft);
        $syllable->setMinWordLength($this->getThreshold());

        return $syllable->hyphenateText($this->getContent());
    }
}
