<?php
/*!
 * DoubleGIS API extension.
 * See avalaible methods and their full description at http://api.2gis.ru/doc/main/
 *
 * Configuration properties:
 * -------------------------
 *      apiUrl		URL to DG catalog API
 *      apiKey		API key (check our website for instruction)
 *      apiVersion	API version
 *
 * Public methods (see detailed description near each method):
 * -----------------------------------------------------------
 *      search($params, $format = 'json')
 *      firmsByFilialId($firmId, $format = 'json')
 *      profile($filialId, $hash, $format = 'json')
 *      geoSearch($searchParams, $format = 'json')
 *      projectList($params, $format = 'json')
 *      cityList($params, $format = 'json')
 *      rubricator($params, $format = 'json')
 *      searchInRubric($params, $format = 'json')
 *
 * Requirements:
 * -------------
 *  - Yii 1.1.x or above
 *  - web browser component (e.g. DGWebBrowser or your own with get() and getResponseText() methods)
 */

/**
 * Forms queries to DG catalog API
 *
 * @class DGApiClient
 * @author Alexey Ashurok <a.ashurok@2gis.ru>
 * @author Alexander Biryukov <a.biryukov@2gis.ru>
 * @author Konstantin Likhter <k.likhter@2gis.ru>
 * @link http://www.2gis.ru
 * @copyright 2GIS
 * @version 1.0
 */
class DGApiClient extends CApplicationComponent
{
    /**
     * @var string url to DG catalog API
     */
    public $apiUrl = 'http://catalog.api.2gis.ru';
    /**
     * @var string api key
     */
    public $apiKey;
    /**
     * @var string api version
     */
    public $apiVersion = '1.3';
    /**
     * @var string api language
     */   
    public $apiLanguage = 'ru';
    /**
     * @var string web browser component name. You can create your own with get() and getResponseText() methods
     */
    public $webBrowser = 'webBrowser';
    /**
     * @var string last perfomed request
     */
    public $lastRequest;
    /**
     * @var DGWebBrowser Instance
     */
    protected $_webBrowser;
    /**
     * Init the extension
     */
    public function init()
    {
        Yii::trace('DGApiClient extension initializing', 'dg.ApiClient');

        $this->_webBrowser = Yii::app()->getComponent($this->webBrowser);

        if ($this->apiUrl !== null) {
            if (strpos($this->apiUrl, 'http://') === false) {
                $this->apiUrl = 'http://' . $this->apiUrl;
            }
        } else {
            throw new Exception('DGApiClient apiUrl parameter should be initialized');
        }

        if ($this->apiKey === null) {
            throw new Exception('DGApiClient apiKey parameter should be initialized');
        }

        parent::init();
        Yii::trace('DGApiClient extension is initialized', 'dg.ApiClient');
    }

    /**
     * Search filials list by point or "where string"
     *
     * @link http://api.2gis.ru/doc/firm-search/
     * @param array $params  search parameters as described on <a href="http://api.2gis.ru/doc/firm-search/">API doc</a>
     * @param string $format  result format. Allowed: 'xml', 'json', 'array', 'obj' ('obj' - array of objects)
     * @return mixed list of filials in appropriate format
     */
    public function search($params, $format = 'json')
    {
        Yii::trace('Get filials list :' . print_r($params, true), 'dg.ApiClient.search');

        if (isset($params['lat']) && is_numeric($params['lat']) && isset($params['lon']) && is_numeric($params['lon'])) {
            $params['point'] = $params['lon'] . ',' . $params['lat'];
            unset($params['lat']);
            unset($params['lon']);
        }

	return  $this->simpleRequest('search', $params, $format);
    }

    /**
     * Returns firm filials list
     *
     * @link http://api.2gis.ru/doc/firm-list-id/
     * @param array | int $params search parameters as described on <a href="http://api.2gis.ru/doc/firms/searches/firmsbyfilialid/">API doc</a> or firmId
     * @param string $format  result format. Allowed: 'xml', 'json', 'array', 'obj' ('obj' - array of objects)
     * @return bool|mixed mixed list of filials in appropriate format
     */
    public function firmsByFilialId($params, $format = 'json')
    {
        Yii::trace('Get filials list by firm ID ' . print_r($params, true), 'dg.ApiClient.firmsByFilialId');
        
        if (!is_array($params)) {
            $params = array('firmid' => $params); // for backward compability
        }
        return $this->simpleRequest('firmsByFilialId', $params, $format);
    }

    /**
     * Returns filial full information
     *
     * @link http://api.2gis.ru/doc/firm-profile-output/
     * @param integer $filialId   ID returned by filialsList
     * @param string $hash Hash value for search request
     * @param string $format  result format. Allowed: 'xml', 'json', 'array', 'obj'
     * @return mixed|bool filial info in appropriate format or FALSE in case of exception.
     */
    public function profile($filialId, $hash, $format = 'json')
    {
        Yii::trace('Get filial info for ' . $filialId, 'dg.ApiClient.profile');
        return $this->simpleRequest('profile', array('id' => $filialId, 'hash' => $hash), $format);
    }


    /**
     * Search geoms list by point
     *
     * @link http://api.2gis.ru/doc/geo-search/
     * @param array $searchParams  search parameters
     * @param string $format  result format. Allowed: 'xml', 'json', 'array', 'obj' ('obj' - array of objects)
     * @return bool|mixed hierarchy list of geoms in appropriate format or FALSE in case of exception.
     */
    public function geoSearch($searchParams, $format = 'json')
    {
        Yii::trace('Get geoobjects list :' . print_r($searchParams, true), 'dg.ApiClient.geoSearch');
        return $this->simpleRequest('geo/search', $searchParams, $format);
    }
    
    /**
     * Gets geoobject by id
     *
     * @link http://api.2gis.ru/doc/geo-get/
     * @param $id
     * @return bool|mixed goobject in appropriate format or FALSE in case of exception
     */
    public function geoGet($id, $format = 'json')
    {
        Yii::trace('Get geoobject :' . print_r($id, true), 'dg.ApiClient.geoGet');
        return $this->simpleRequest('geo/get', array('id' => $id), $format);
    }

    /**
     * Returns projects' list
     *
     * @link http://api.2gis.ru/doc/project-list/
     * @param mixed $params Search parameters as on <a href="http://api.2gis.ru/doc/project-list/">API doc</a>
     * @param string $format Output data format (json, xml)
     * @return bool|mixed Result in requested format or FALSE on exception while request.
     */
    public function projectList($params = array(), $format = 'json')
    {
        Yii::trace('Get projects list ' . print_r($params, true), 'dg.ApiClient.projectsList');
        return $this->simpleRequest('project/list', $params, $format);
    }

    /**
     * Returns cities which are in current project (specified by `project_id`).
     * `where` field is used if no `project_id` is provided.
     *
     * @link http://api.2gis.ru/doc/city-list/
     * @param mixed $params Search parameters as on <a href="http://api.2gis.ru/doc/city-list/">API doc</a>
     * @param string $format Output data format (json, xml)
     * @return bool|mixed Result in request format or FALSE on exception while request.
     */
    public function cityList($params, $format = 'json')
    {
        Yii::trace('Get cities list ' . print_r($params, true), 'dg.ApiClient.cityList');
        return $this->simpleRequest('city/list', $params, $format);
    }

    /**
     * Selects child rubrics for parent specified by `parent_id`.
     * Leave `parent_id` empty or undefined to select root rubrics.
     *
     * @link http://api.2gis.ru/doc/rubricator/
     * @param mixed $params Parameters as described on <a href="http://api.2gis.ru/doc/rubricator/">API website</a>
     * @param string $format Output data format (json, xml)
     * @return bool|mixed Result in requested format or FALSE on exception while request.
     */
    public function rubricator($params, $format = 'json')
    {
        Yii::trace('Get rubrics list ' . print_r($params, true), 'dg.ApiClient.rubricator');
        return $this->simpleRequest('rubricator', $params, $format);
    }

    /**
     * Searches for firms in specified rubric.
     *
     * @link http://api.2gis.ru/doc/firm-search-category/
     * @param mixed $params Search parameters are the same as for <a href="http://api.2gis.ru/doc/firm-search/">search</a> method, excepting field `what`: it should contain the rubric name
     * @param string $format Output data format (json, xml)
     * @return bool|mixed Result in requested format or FALSE on exception while request
     */
    public function searchInRubric($params, $format = 'json')
    {
        Yii::trace('Get firms in rubrics list ' . print_r($params, true), 'dg.ApiClient.searchInRubric');
        return $this->simpleRequest('searchinrubric', $params, $format);
    }


    /**
     *  Return response in specified format
     *
     * @param string $response  xml or json response
     * @param string $toFormat  format convert to
     * @throws Exception
     * @return mixed array or object
     */
    protected function reformat($response, $toFormat)
    {
        $toFormat = strtolower($toFormat);
        $result = false;
        if (in_array($toFormat, array('xml', 'json'), true)) {
            $result = $response;
        } else {
            switch ($toFormat) {
                case 'array':
                    $result = json_decode($response, true);
                    break;

                case 'object':
                case 'obj':
                    $result = json_decode($response);
                    break;
            }
        }
        if (!$result && $response) {
            if (YII_DEBUG) {
                echo $response;
                die();
            }
            throw new Exception('DGApiClient unable to reformat response.');
        }
        return $result;
    }


    /**
     * Performs simple request which is used in many API calls of the same type.
     * @param string $method Api method which should be called
     * @param mixed $params APi call parameters
     * @param string $format Output data format
     * @return bool|mixed Reformatted (in $format) data or FALSE on exception.
     */
    protected function simpleRequest($method, $params, $format = 'json') {
        try {
            $request = $this->buildRequest($method, $params, $format);
            Yii::trace('Perform request: ' . $request, 'dg.ApiClient.simpleRequest');

            $this->_webBrowser->get($request);
	        $response = $this->_webBrowser->getResponseText();

            return $this->reformat($response, $format);
        }
        catch (Exception $e) {
            return false;
        }
    }


    /**
     * Builds URL request to catalog API
     *
     * @param string $service service path
     * @param array $params query parameters
     * @param string $format output format
     * @throws CException if $format not in allowedFormats list
     * @return string - formed URL request to DG catalog API
     */
    protected function buildRequest($service, $params, $format)
    {
        Yii::trace('Building URL', 'dg.ApiClient.buildRequest');

        $params['key'] = $this->apiKey;
        $params['version'] = $this->apiVersion;
        $params['lang'] = $this->apiLanguage;
        $params['limit'] = Yii::app()->params['defaultLimit'];
        $params['output'] = ($format == 'xml') ? $format : 'json';

        $request = $this->lastRequest = $this->apiUrl . '/' . $service . '?' . http_build_query($params);

        Yii::trace($request, 'dg.ApiClient.buildRequest.Done');

        return $request;
    }

}
