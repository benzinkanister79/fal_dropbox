<?php
declare(strict_types = 1);
namespace SFroemken\FalDropbox\Form\Element;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * This class retrieves and shows Dropbox Account information
 */
class RequestTokenElement extends AbstractFormElement
{
    /**
     * Form Element to request a Dropbox Token
     */
    public function render()
    {
        try {
            $flexFormFields = ArrayUtility::getValueByPath(
                $this->data,
                'databaseRow/configuration/data/sDEF/lDEF'
            );
        } catch (MissingArrayPathException $e) {
            return 'Path databaseRow/configuration/data/sDEF/lDEF can not be extracted from TCA array';
        }

        $config = [];
        foreach ($flexFormFields as $key => $value) {
            $config[$key] = $value['vDEF'];
        }
        return [
            'html' => $this->getHtmlForConnected($config['accessToken'])
        ];
    }

    /**
     * Get HTML to show the user, that he is connected with its dropbox account
     *
     * @param string $accessToken
     * @return string
     */
    public function getHtmlForConnected(string $accessToken): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:fal_dropbox/Resources/Private/Templates/ShowAccountInfo.html'
            )
        );

        try {
            $dropboxApp = GeneralUtility::makeInstance(DropboxApp::class, '', '',  $accessToken);
            $dropbox = GeneralUtility::makeInstance(Dropbox::class, $dropboxApp);
            $view->assign('account', $dropbox->getCurrentAccount());
            $view->assign('quota', $dropbox->getSpaceUsage());
            $content = $view->render();
        } catch (\Exception $e) {
            $content = 'Please setup access token first to see your account info here.';
        }

        return $content;
    }
}
