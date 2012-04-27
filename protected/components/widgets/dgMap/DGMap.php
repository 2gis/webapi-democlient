<?php
/**
 * DGMap widget
 * 
 * @category   DoubleGIS
 * @package    Demo
 * @subpackage Widgets
 * @copyright  2012 DoubleGIS
 * @link       http://api.2gis.ru/doc/
 */
class DGMap extends CWidget
{
    /**
     * @var array | null
     */
    public $markers = null;
    
    /**
     * @var array | null
     */
    public $geometries = null;
    
    /**
     * @var array
     */
    public $centroid;
    
    /**
     * @var string
     */
    public $mapsApiUrl = 'http://maps.api.2gis.ru/1.0';
    
    /**
     * @var string
     */
    protected $assetsUrl;
    
    /**
     * Renders widget
     */
    public function run()
    {
        $this->render('map', array('markers' => $this->markers, 'centroid' => $this->centroid, 'geometries' => $this->geometries, 'assetsUrl' => $this->assetsUrl));
    }
    
    /**
     * Initializes widget
     */
    public function init()
    {
        $this->assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.components.widgets.dgMap.assets'));
        $this->registerClientScript();
        
        if (is_string($this->centroid)) { // WKT
            $tmp = explode(' ', $this->centroid);
            $centroid = array('lon' => substr($tmp[0], 6), 'lat' => substr($tmp[1], 0, -1));
            $this->centroid = $centroid;
        }
        
        parent::init();
    }
    
    /**
     * Registers JS and CSS files
     */
    protected function registerClientScript()
    {
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile($this->mapsApiUrl);
        $cs->registerScriptFile($this->assetsUrl.'/map.js');
    }
}
?>
