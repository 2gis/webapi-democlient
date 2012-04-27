<?php
/*!
 * DoubleGIS Web Browser (sfWebBrowserPlugin wrapper).
 * Visit http://www.symfony-project.org/plugins/sfWebBrowserPlugin/1_1_2 for full documentation of sfWebBrowserPlugin.
 * 
 * Configuration properties:
 * -------------------------
 * 	adapter		One of the supported adapters. Supported values: 
 * 			curl 		-- for sfCurlAdapter
 * 			sockets		-- for sfSocketsAdapter
 * 			fopen		-- for sfFopenAdapter		
 * 	adapterOptions	See sfWebBrowserPlugin documentation for options of avaliable adapters.
 *
 * Requirements:
 * -------------
 *	- Yii 1.1.x or above
 * 	- sfWebBrowserPlugin (already included into the extension package)
 */


include_once(dirname(__FILE__)."/sfWebBrowserPlugin/lib/sfWebBrowser.class.php");
include_once(dirname(__FILE__)."/sfException.class.php");


/**
 * @class DGWebBrowser
 * @uses sfWebBrowserPlugin - plugin for symfony framework
 * 
 * To see the documentation please visit
 * @see http://www.symfony-project.org/plugins/sfWebBrowserPlugin/1_1_2
 *
 * @link http://www.2gis.ru
 * @copyright 2GIS
 * @author Alexander Biryukov <alex.biryukoff@gmail.com>
 * @version 1.0
 */
class DGWebBrowser extends CApplicationComponent
{
    public $adapter        = null;
    public $adapterOptions = array();

    protected $client = null;
    
    private $adaptersMap = array('curl' => 'sfCurlAdapter', 'sockets' => 'sfSocketsAdapter', 'fopen' => 'sfFopenAdapter');


    public function  __call($name, $parameters)
    {
        if (method_exists($this->client, $name)) {
            return call_user_func_array(array($this->client, $name), $parameters);
        } else {
            return parent::__call($name, $parameters);
        }
    }

    public function init()
    {
        $adapterClass = null;
        if (array_key_exists($this->adapter, $this->adaptersMap)) {
            $adapterClass = $this->adaptersMap[$this->adapter];
        } else {
            $adapterClass = $this->adaptersMap['curl'];
        }

        include_once(dirname(__FILE__)."/sfWebBrowserPlugin/lib/".$adapterClass.".class.php");

        $this->client = new sfWebBrowser(array(), $adapterClass, $this->adapterOptions);
        
        parent::init();
    }
}

