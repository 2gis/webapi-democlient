<?php
/**
 * Widget for search form
 * 
 * @category   DoubleGIS
 * @package    Demo
 * @subpackage Widgets
 * @copyright  2012 DoubleGIS
 * @link       http://api.2gis.ru/doc/
 */
class SearchForm extends CWidget
{
    /**
     * @var array
     */
    public $params;
    
    /**
     * Registers JS and CSS files
     * 
     * @param string $assetsUrl
     */
    protected function registerClientScript($assetsUrl)
    {
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile($assetsUrl.'/form.js');
    }
    
    /**
     * Initializes widget
     */
    public function init()
    {
        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.components.widgets.searchForm.assets'));
        $this->registerClientScript($assetsUrl);
        parent::init();
    }
    
    /**
     * Renders widget
     */
    public function run()
    {
        $this->render('searchForm', array(
            'what' => $this->params['what'], 
            'where' => $this->params['where'], 
            'q' => $this->params['q'],
            'sort' => $this->params['sort'], 
            'search_condition' => $this->params['search_condition'], 
            'lat' => $this->params['lat'], 
            'lon' => $this->params['lon'], 
            'rad' => $this->params['radius'],
            'workingNow' => $this->params['workingNow']
             ));
    }
}
?>
