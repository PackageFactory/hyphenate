<?php
namespace PackageFactory\Hyphenate\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Utility\Environment;
use Neos\Flow\I18n\Service as LocalizationService;
use Neos\Flow\Package\PackageManager;
use Neos\Utility\Files;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Vanderlee\Syllable\Hyphen;
use Vanderlee\Syllable\Syllable;

class HyphenateImplementation extends AbstractFusionObject
{
    /**
     * @Flow\Inject
     * @var Environment
     */
    protected $environment;

    /**
     * @Flow\Inject
     * @var PackageManager
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

    public function getType()
    {
        return strtolower($this->fusionValue('type'));
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

        $syllable = new Syllable($this->getLocale());

	    $syllable->getSource()->setPath($languagesDirectory);
        $syllable->getCache()->setPath($cacheDirectory);

        $syllable->setHyphen(new Hyphen\Soft());
        $syllable->setMinWordLength($this->getThreshold());

        switch ($this->getType()) {
            case 'html':
                $html = mb_convert_encoding($this->getContent(), 'HTML-ENTITIES', "UTF-8");
                $result = $syllable->hyphenateHtml($html);
                break;
            case 'test':
                // Break missing intendedly
            default:
                $result = $syllable->hyphenateText($this->getContent());
                break;
        }
        return $result;
    }
}
