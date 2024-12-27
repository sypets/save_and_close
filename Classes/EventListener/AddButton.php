<?php

namespace WapplerSystems\SaveAndClose\EventListener;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Template\Components\Buttons\InputButton;

class AddButton
{
    /**
     * @param ModifyButtonBarEvent $event
     */
    public function __invoke(ModifyButtonBarEvent $event): void {

        $showSaveAndView = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('save_and_close', 'saveAndView');
        $showSaveAndClose = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('save_and_close', 'saveAndClose');

        $buttons = $event->getButtons();
        $buttonBar = $event->getButtonBar();
        $saveButton = $buttons[ButtonBar::BUTTON_POSITION_LEFT][2][0] ?? null;

        /** @var Typo3Version $typoVersion */
        $typoVersion = GeneralUtility::makeInstance(Typo3Version::class);
        /**
         * Due to deprecation in TYPO3 v13
         * @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.0/Deprecation-101475-IconSizeStringConstants.html
         *
         * @todo Switch can be removed when support for v12 is dropped
         */
        if ($typoVersion->getMajorVersion() < 13) {
            $iconSize = Icon::SIZE_SMALL;
        } else {
            $iconSize = IconSize::SMALL;
        }

        if ($saveButton instanceof InputButton) {
            /** @var IconFactory $iconFactory */
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

            if ($showSaveAndClose === '1') {
                $saveCloseButton = $buttonBar->makeInputButton()
                    ->setName('_saveandclosedok')
                    ->setValue('1')
                    ->setForm($saveButton->getForm())
                    ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:rm.saveCloseDoc'))
                    ->setIcon($iconFactory->getIcon('actions-document-save-close', $iconSize))
                    ->setShowLabelText(true);
                $buttons[ButtonBar::BUTTON_POSITION_LEFT][2][] = $saveCloseButton;
            }

            if ($showSaveAndView === '1') {
                $saveViewButton = $buttonBar->makeInputButton()
                    ->setName('_savedokview')
                    ->setValue('1')
                    ->setForm($saveButton->getForm())
                    ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:rm.saveDocShow'))
                    ->setIcon($iconFactory->getIcon('actions-document-save-view', $iconSize))
                    ->setShowLabelText(true);
                $buttons[ButtonBar::BUTTON_POSITION_LEFT][2][] = $saveViewButton;
            }

        }
        $event->setButtons($buttons);

    }

    /**
     * Returns the language service
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
