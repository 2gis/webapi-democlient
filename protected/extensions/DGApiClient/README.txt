DGApiClient Extension
===============


 The DGApiClient is a Yii Framework plugin that supply methods allowing you easily communicate with
 DoubleGIS API. Contains all methods described on http://api.2gis.ru/doc/main.


Requirements
------------

- Yii 1.1.*
- webBrowser component (e.g. DGWebBrowser or your own implementing get() and getResponse() methods)


Installation
------------

 - If you have no personal API key, register on http://partner.api.2gis.ru/ to recieve it
 - Install webBrowser component 
 - Unpack all files under your project 'component' folder
 - Include your new extension into your project main.php configuration file:
 
      'components' => array(
        
        ...
        
        'apiClient'  => array(
            'class'      => 'application.components.DGApiClient.DGApiClient',
            'apiKey'     => 'YOUR_PERSONAL_KEY_HERE',
            'webBrowser' => 'webBrowser' // component name
        ),
        'webBrowser' => array( // DGWebBrowser
            'class'      => 'application.components.DGWebBrowser.DGWebBrowser',
            'adapter'    => 'curl' 
        ),
        
        ...
        
      )
      
 - Enjoy!
 
 
Usage:
-------

    $apiClient = Yii::app()->getComponent('apiClient');
    
 E.g. to recieve limited (to 10 records) list of geo-objects in radius one kilometer away 
 from the point (82.901886, 54.991984) in XML format:

   $list = $apiClient->geoSearch(array(
    'q'         => '82.901886,54.991984',
    'radius'    => 1000,
    'limit'     => 10,
    'output'    => 'xml'
   ));

 For detailed description of methods and their parameters see http://api.2gis.ru/doc/main/


Changelog:
-------

- 1.0   Initial release
