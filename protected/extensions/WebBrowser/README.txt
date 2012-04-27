DGWebBrowser Extension
===============

 The DGApiClient is a wrapper of sfWebBrowserPlugin.

Requirements
------------

- Yii 1.1.*
- sfWebBrowserPlugin (already included into the package)

Installation
------------

 - Unpack all files (including sfWebBrowser directory) under your project 'component' folder
 - Include your new extension into your project main.php configuration file:
 
      'components' => array(
        
        ...
        
	'webBrowser' => array( // DGWebBrowser
		'class' 	=> 'application.components.DGWebBrowser.DGWebBrowser',
		'adapter'	=> 'curl' 
	),
        
        ...
        
      )
      
 - Enjoy!
 
 
Usage:
-------

 For detailed description of methods and their parameters see documentation of the 
 extension's base -- sfWebBrowserPlugin -- on http://www.symfony-project.org/plugins/sfWebBrowserPlugin/1_1_2
    

Changelog:
-------

- 1.0   Initial release
