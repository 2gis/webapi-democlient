<?php
/**
 * Demo helper
 * 
 * @category   DoubleGIS
 * @package    Demo
 * @subpackage Helpers
 * @copyright  2012 DoubleGIS
 * @link       http://api.2gis.ru/doc/
 */
class Helper
{
    /**
     * Gets array of working hours
     *
     * @param stdClass $whs result of json_decode() for schedule
     * @param array $daysMap
     * @return array
     */
    public static function getFullWorkingHours($whs, $daysMap = array('Mon' => 'Пн', 'Tue' => 'Вт', 'Wed' => 'Ср', 'Thu' => 'Чт', 'Fri' => 'Пт', 'Sat' => 'Сб', 'Sun' => 'Вс'))
    {
        $result = array('days' => array(), 'from' => array(), 'to' => array());

        foreach ($whs as $dayName => $day) {
            if (!is_object($day)) {
                continue;
            }
            foreach (get_object_vars($day) as $time) {
                $result['days'][] = $daysMap[$dayName];
                $result['from'][] = $time->from;
                $result['to'][] = $time->to;
            }
        }

        return $result;
    }
    
    /**
     * Gets russian payoption name
     *
     * @param string $type
     * @return string
     */
    public static function getPayoptionName($type) {
        $payoptionName = '';
        switch(strtolower($type)) {
            case 'cash':
                $payoptionName = 'Наличный расчёт';
                break;
            case 'dinersclub':
                $payoptionName = 'Diners Club';
                break;
            case 'goldcrown':
                $payoptionName = 'Золотая Корона';
                break;
            case 'internet':
                $payoptionName = 'Оплата через Интернет';
                break;
            case 'non-cash':
                $payoptionName = 'Безналичный расчет для юридических лиц';
                break;
            default:
                $payoptionName = $type;
                break;
        }
        return $payoptionName;
    }

    /**
     * Gets payoption name
     *
     * @param stdClass $filial
     * @return string | bool
     */
    public static function getFullAddress($filial) {
         if (isset($filial->address) || isset($filial->city_name)) {
             if (isset($filial->city_name) && isset($filial->address)) {
                 return  $filial->city_name . ', ' . $filial->address;
             }  elseif (isset($filial->city_name)) {
                 return $filial->city_name;
             }  else {
                 return $filial->address;
             }
         } else {
             return false;
         }
    }
    
    /**
     * Gets geo type icon name
     *
     * @param string $type
     * @return string
     */
    public static function getGeoTypeIcon($type) {
        switch($type) {
            case 'city': 
            case 'settlement': 
                $icon = 'city-icon';
            break;

            case 'street':
                $icon = 'street-icon';
            break;

            case 'district':
            case 'living_area':
            case 'place':
                $icon = 'place-icon';
            break;

            case 'house':
                $icon = 'building-icon';
            break;

            case 'sight':
                $icon = 'sight-icon';
            break;

            case 'station_platform':
            case 'station':
                $icon = 'busstation-icon';
            break;
            default:
                $icon = '';
        }
        return $icon;
    }

    /**
     * Gets geo type russian name
     *
     * @param string $type
     * @return string
     */
    public static function getGeoTypeRussianName($type) {
        switch($type) {
            case 'project': 
                $typeName = 'проект';
                break;
            case 'district':
                $typeName = 'район';
                break;
            case 'city':
                $typeName = 'город';
                break;
            case 'settlement':
                $typeName = 'населенный пункт';
                break;
            case 'living_area':
                $typeName = 'микрорайон';
                break;
            case 'place':
                $typeName = 'место';
                break;
            case 'street':
                $typeName = 'улица';
                break;
            case 'house':
                $typeName = 'дом';
                break;
            case 'sight':
                $typeName = 'достопримечательность';
                break;
            case 'station_platform':
                $typeName = 'остановочная платформа';
                break;
            case 'station':
                $typeName = 'станция';
                break;
            case 'railway_platform':
                $typeName = 'остановочная платформа ж/д транспорта';
                break;
            case 'route':
                $typeName = 'маршрут';
                break;
            default:
                $typeName = $type;
        }
        
        return $typeName;
    }
}

