<?php
/** 
 * Demo controller
 * 
 * @category   DoubleGIS
 * @package    Demo
 * @subpackage Controllers
 * @copyright  2012 DoubleGIS
 * @version    1.0
 * @link       http://api.2gis.ru/doc/
 */
class DemoController extends CController
{
    /**
     * @var string
     */
    public $defaultAction = 'index';
    
    /**
     * @var bool
     */
    public $showSearchForm = true;
    
    /**
     * @var array
     */
    public $params;
    
    /**
     * Initializes controller
     */
    public function init() {
        $this->pageTitle = 'Демо-версия API 2ГИС';
        
        $request = Yii::app()->request;
        $this->params['what']               = $request->getParam('what', '');
        $this->params['rubric']             = $request->getParam('rubric', '');
        $this->params['where']              = $request->getParam('where', '');
        $this->params['q']                  = $request->getParam('q');
        $this->params['sort']               = $request->getParam('sort', 'relevance');
        $this->params['search_condition']   = $request->getParam('search_condition', 'where');
        $this->params['lat']                = $request->getParam('lat', '54.991984');
        $this->params['lon']                = $request->getParam('lon', '82.901886');
        $this->params['radius']             = $request->getParam('rad', '1000');
        $this->params['workingNow']         = $request->getParam('workingNow', false);
        
        if ($rubric = $request->getParam('rubric', '')) $this->params['what'] = $rubric;
    }

    /**
     * Action for demo site main page
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    /**
     * Action for search page
     * Requests 'search' method of DG API
     */
    public function actionSearch()
    {
        $request         = Yii::app()->request;
        $whatField       = 'what';
        $what            = $request->getParam('what', '');
        $rubric          = $request->getParam('rubric', '');
        $where           = $request->getParam('where', '');
        $page            = $request->getParam('page', 1);
        $pagesize        = $request->getParam('pagesize', 20);
        $workingNow      = $request->getParam('workingNow', false);
        $sort            = $request->getParam('sort', 'relevance');
        $searchCondition = $request->getParam('search_condition', 'where');
        $lat             = $request->getParam('lat', '');
        $lon             = $request->getParam('lon', '');
        $radius          = $request->getParam('rad', '1000');
        
        $filters = array();
        if ($workingNow) {
            $worktime = strtolower(date('D,H:i'));
            $filters = array('worktime' => $worktime);
        }

        if ($rubric && mb_strlen($rubric, 'UTF-8')) {
            $what = $rubric;
            $callMethod = 'searchInRubric';
            $searchParam = 'rubric';
        } else {
            $callMethod = 'search';
            $searchParam = 'what';
        }

        $centroid = null;

        if ($searchCondition == 'point') {
            $centroid = array('lon' => $lon, 'lat' => $lat);
        }

        $paramsApiClient = array(
            $whatField => $what,
            'where' => $where,
            'page' => $page,
            'pagesize' => $pagesize,
            'sort' => $sort,
        );

        if ($searchCondition == 'point') {
            $paramsApiClient['lat'] = $lat;
            $paramsApiClient['lon'] = $lon;
            $paramsApiClient['radius'] = $radius;
        }
        
        if ($filters) {
            $paramsApiClient['filters'] = $filters;
        }

        $jst = microtime(true);
        $jsonRequestUrl = '';
        try {
            $json = Yii::app()->apiClient->$callMethod($paramsApiClient);
            $jsonRequestUrl = Yii::app()->apiClient->lastRequest;
        } catch (Exception $e) {
            $json = '{}';
        }

        $jet = microtime(true);

        Yii::trace(print_r($json, true), 'demo.search.result');
        if (!$centroid) {
            $centroid = new stdClass();
            if ($json) {
                $obj = json_decode($json);
                if (isset($obj->result)) {
                    $selectionFinded = false;
                    for ($i = 0; $count = count($obj->result), $i < $count; $i++) {
                        if (isset($obj->result[$i]) && isset($obj->result[$i]->lon) && isset($obj->result[$i]->lat)) {
                            $lon = $obj->result[$i]->lon;
                            $lat = $obj->result[$i]->lat;
                            $selectionFinded = true;
                            break;
                        }
                    }
                    if (!$selectionFinded) { // WAPI-1556
                        for ($i = 0; $count = count($obj->result), $i < $count; $i++) {
                            if (isset ($obj->result[$i]->city_name)) {
                                if ($lonlat = Yii::app()->apiClient->getLonLatByName($obj->result[$i]->city_name)) {
                                    $lon = $lonlat->lon;
                                    $lat = $lonlat->lat;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            $centroid = array('lon' => $lon, 'lat' => $lat);
        }

        $xst = microtime(true);
        $xmlRequestUrl = '';
        try {
            $xml = Yii::app()->apiClient->$callMethod($paramsApiClient, 'xml');
            $xmlRequestUrl = Yii::app()->apiClient->lastRequest;
        } catch (Exception $e) {
            $xml = '';
        }
        $xet = microtime(true);

        $this->render('filial_list', array(
            'filials' => json_decode($json),
            'what' => $what,
            'where' => $where,
            'searchParam' => $searchParam,
            'page' => $page,
            'limit' => $pagesize,
            'sort' => $sort,
            'search_condition' => $searchCondition,
            'lat' => $lat,
            'lon' => $lon,
            'rad' => $radius,
            'url' => $jsonRequestUrl,
            'centroid' => $centroid,
            'rawJson' => Helper::jsonPrettify($json),
            'rawXml' => $xml,
            'rawRe' => $jsonRequestUrl,
            'jsonRespTime' => $jet - $jst,
            'xmlRespTime' => $xet - $xst,
            'isSearchResult' => true,
            'workingNow' => $workingNow
        ));
    }

    /**
     * Action for filials page. Shows all filials by firm id
     * Requests 'firmsByFilialId' method of DG API
     */
    public function actionFilials()
    {
        $request = Yii::app()->request;
        $firmId  = $request->getParam('firm_id', 0);
        $lat     = $request->getParam('lat', '54.991984');
        $lon     = $request->getParam('lon', '82.901886');
        $radius  = $request->getParam('rad', '1000');
        $page    = $request->getParam('page', 1);

        $params = array('firmid' => $firmId, 'page' => $page, 'pagesize' => 20);
        
        $jst = microtime(true);
        try {
            $json = Yii::app()->apiClient->firmsByFilialId($params);
        } catch (Exception $e) {
            $json = '{}';
        }

        $jet = microtime(true);

        $xst = microtime(true);
        try {
            $xml = Yii::app()->apiClient->firmsByFilialId($params, 'xml');
        } catch (Exception $e) {
            $xml = '';
        }
        $xet = microtime(true);

        $centroid = new stdClass();

        if ($json) {
            $obj = json_decode($json);
            if (isset($obj->result)) {
                for ($i = 0; $count = count($obj->result), $i < $count; $i++) {
                    if (isset($obj->result[$i]) && isset($obj->result[$i]->lon) && isset($obj->result[$i]->lat)) {
                        $lon = $obj->result[$i]->lon;
                        $lat = $obj->result[$i]->lat;
                        break;
                    }
                }
            }
        }
        $centroid = array('lon' => $lon, 'lat' => $lat);
        
        $this->render('filial_list', array(
            'filials' => json_decode($json),
            'lat' => $lat,
            'lon' => $lon,
            'rad' => $radius,
            'page' => $page,
            'limit' => 20,
            'url' => Yii::app()->apiClient->lastRequest,
            'centroid' => $centroid,
            'workingNow' => false,
            'rawJson' => Helper::jsonPrettify($json),
            'rawXml' => $xml,
            'rawRe' => Yii::app()->apiClient->lastRequest,
            'jsonRespTime' => $jet - $jst,
            'xmlRespTime' => $xet - $xst,
            'isSearchResult' => false,
        ));
    }

    /**
     * Action for firm profile page
     * Requests 'getFirmById' method of DG API
     */
    public function actionProfile()
    {
        $request = Yii::app()->request;
        $what   = $request->getParam('what', '');
        $rubric = $request->getParam('rubric', '');
        $what   = ($rubric && mb_strlen($rubric, 'UTF-8')) ? $rubric : $what;
        $where  = $request->getParam('where', '');
        $workingNow  = $request->getParam('workingNow', false);

        $sort             = $request->getParam('sort', 'relevance');
        $search_condition = $request->getParam('search_condition', 'where');
        $lat              = $request->getParam('lat', '54.991984');
        $lon              = $request->getParam('lon', '82.901886');
        $radius           = $request->getParam('rad', '1000');

        $filialId = $request->getParam('filial_id', null);
        $hash     = $request->getParam('hash', null);

        // Make a request
        $jst = microtime(true);
        $json = Yii::app()->apiClient->profile($filialId, $hash);
        $jet = microtime(true);

        $xst = microtime(true);
        $xml = Yii::app()->apiClient->profile($filialId, $hash, 'xml');
        $xet = microtime(true);

        $filial = json_decode($json);

        Yii::trace(print_r($json, true), 'demo.profile.result');

        $reviews = array();
        $this->render('filial_info', array(
            'filial' => $filial,
            'reviews' => $reviews,
            'what' => $what,
            'where' => $where,
            'sort' => $sort,
            'search_condition' => $search_condition,
            'lat' => $lat,
            'lon' => $lon,
            'rad' => $radius,
            'workingNow' => $workingNow,
            'rawJson' => Helper::jsonPrettify($json),
            'rawXml' => $xml,
            'rawRe' => Yii::app()->apiClient->lastRequest,
            'jsonRespTime' => $jet - $jst,
            'xmlRespTime' => $xet - $xst,
        ));
    }

    /**
     * Returns coordinates of geo object center by its name
     * Requests 'geoSearch' method of DG API
     */
    public function actionGeoCoord()
    {
        $request = Yii::app()->request;
        $where   = $request->getParam('where', 'Новосибирск');
        $project = $request->getParam('project');
        $types   = $request->getParam('types');
        $limit   = $request->getParam('limit', 1);
        
        $jst = microtime(true);

        $point = '';
        try {
            $json = Yii::app()->apiClient->geoSearch(array(
                        'q' => $where,
                        'project' => $project,
                        'types' => $types,
                        'limit' => $limit,
                    ));
            $geo = json_decode($json);
            if ($geo->response_code == 200) {
                $point = $geo->result[0]->centroid;
            }
        } catch (Exception $e) {
            $json = '{}';
        }

        if (!empty($point)) {
            $point = explode(' ', str_ireplace(array('POINT(', ')'), '', $point));
            echo json_encode(array('lon' => $point[0], 'lat' => $point[1]));
        }

        Yii::app()->end();
    }
    
    /**
     * Action for geo objects search page
     * Requests 'geoSearch' method of DG API
     */
    public function actionGeoSearch()
    {
        $request = Yii::app()->request;
        
        $searchType = $request->getParam('searchType', 'byName');
        
        switch ($searchType) {
            case 'byName':
                $where = $request->getParam('q', 'Новосибирск');
                $searchParams = array('q' => $where);
                break;
            case 'byPoint':
                $lat = $request->getParam('lat', '54.991984');
                $lon = $request->getParam('lon', '82.901886');
                $searchParams = array('q' => $lon . ',' . $lat);
                break;
        }
        $searchParams['format'] = 'full';
        //$searchParams['limit'] = 10;

        $jst = microtime(true);
        
        try {
            $json = Yii::app()->apiClient->geoSearch($searchParams);
        } catch (Exception $e) {
            $json = '{}';
        }

        $jet = microtime(true);
        Yii::trace(print_r($json, true), 'demo.geoSearch.result');
        $xst = microtime(true);
        
        try {
            $xml = Yii::app()->apiClient->geoSearch($searchParams, 'xml');
        } catch (Exception $e) {
            $xml = '';
        }
        
        $xet = microtime(true);

        $geoms = json_decode($json);
        switch ($searchType) {
            case 'byName':
                $center = null;
                if (isset($geoms->result) && isset($geoms->result[count($geoms->result) - 1]->centroid)) {
                    $center = $geoms->result[count($geoms->result) - 1]->centroid;
                }
                break;
            case 'byPoint':
                $center = 'POINT(' . $lon . ' ' . $lat . ')';
                break;
        }

        $this->render('geom_list', array(
            'geoms' => $geoms,
            'where' => isset($where) ? $where : null,
            'lat' => isset($lat) ? $lat : null,
            'lon' => isset($lon) ? $lon : null,
            'workingNow' => false,
            'searchType' => $searchType,
            'center' => $center,
            'rawJson' => Helper::jsonPrettify($json),
            'rawXml' => $xml,
            'rawRe' => Yii::app()->apiClient->lastRequest,
            'jsonRespTime' => $jet - $jst,
            'xmlRespTime' => $xet - $xst,
        ));
    }
    
    /**
     * Action for handling errors
     */
    public function actionError()
    {
        $this->pageTitle = 'Страница не найдена';
        $this->showSearchForm = false;
        $this->render('notfound');
    }
}
