<?php
namespace In2code\Powermail\ViewHelpers\String;

use In2code\Powermail\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper combines Raw and RemoveXss Methods
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class RawAndRemoveXssViewHelper extends AbstractViewHelper
{

    /**
     * Disable the escaping because otherwise the child nodes would be escaped before
     * can decode the text's entities.
     *
     * @var boolean
     */
    protected $escapingInterceptorEnabled = false;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * ViewHelper combines Raw and RemoveXss Methods
     *
     * @return string
     */
    public function render()
    {
        $string = $this->renderChildren();
        if ($this->isHtmlEnabled()) {
            $string = GeneralUtility::removeXSS($string);
        } else {
            $string = htmlspecialchars($string);
        }
        return $string;
    }

    /**
     * @return bool
     */
    protected function isHtmlEnabled()
    {
        $settings = $this->getSettings();
        return $settings['misc']['htmlForLabels'] === '1';
    }

    /**
     * @return array
     */
    protected function getSettings()
    {
        $typoScriptSetup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $typoScriptService = ObjectUtility::getObjectManager()->get(TypoScriptService::class);
        $configuration = $typoScriptService->convertTypoScriptArrayToPlainArray($typoScriptSetup);
        return (array)$configuration['plugin']['tx_powermail']['settings']['setup'];
    }
}
