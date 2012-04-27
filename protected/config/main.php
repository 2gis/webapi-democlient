<?php
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '2GIS demo application',
    'defaultController' => 'demo',
    'preload' => array('log'),
    'import' => array(
        'application.components.*',
        'application.helpers.*',
        'application.extensions.*',
    ),
    'components' => array(
        'errorHandler' => array(
            'errorAction' => 'demo/error',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                'search' => 'demo/search',
                'profile' => 'demo/profile',
                'filials' => 'demo/filials',
                'geoSearch' => 'demo/geoSearch',
                'geoCoord' => 'demo/geoCoord',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
        'apiClient' => array(
            'class' => 'application.extensions.DGApiClient.DGApiClient',
            'behaviors' => array(
                'LonLatBehavior' => array(
                    'class' => 'application.extensions.DGApiClient.LonLatBehavior',
                )
            ),
            'apiUrl' => 'http://catalog.api.2gis.ru',
            'apiKey' => '1',  // paste you API key here
            'apiVersion' => '1.3',
            'apiLanguage' => 'ru',
            'webBrowser' => 'webBrowser'
        ),
        'webBrowser' => array(
            'class' => 'application.extensions.WebBrowser.DGWebBrowser',
            'adapter' => 'curl'
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'enabled' => false,
                ),
            ),
        ),
    ),
    'params' => array(
        'searchMethod' => 'search',
        'mapsApiUrl' => 'http://maps.api.2gis.ru/1.0',
    ),
);
