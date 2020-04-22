<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['registeredDrivers']['fal_dropbox'] = [
        'class' => \SFroemken\FalDropbox\Driver\DropboxDriver::class,
        'shortName' => 'Dropbox',
        'flexFormDS' => 'FILE:EXT:fal_dropbox/Configuration/FlexForms/Dropbox.xml',
        'label' => 'Dropbox'
    ];

    // Create a temporary cache which stores the File Metadata within one request
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['fal_dropbox'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend::class,
        'options' => [],
        'groups' => []
    ];

    // Register a node in ext_localconf.php
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1587575410] = [
        'nodeName' => 'getRequestToken',
        'priority' => 40,
        'class' => SFroemken\FalDropbox\Form\Element\RequestTokenElement::class
    ];

    // Add wizard/control to access_token in XML structure
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1552228283] = [
        'nodeName' => 'accessToken',
        'priority' => '70',
        'class' => \SFroemken\FalDropbox\Form\Element\AccessTokenElement::class
    ];

    $extractorRegistry = \TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance();
    $extractorRegistry->registerExtractionService(\SFroemken\FalDropbox\Extractor\ImageExtractor::class);
});
