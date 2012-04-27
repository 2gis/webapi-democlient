<?php

class LonLatBehavior extends CBehavior { 
    /**
     * @param string $name - name of geometry
     * @return stf object with lon and lat (false if not found)
     */
    public function getLonLatByName($name)
    {
        $ret = false;
        $json = $this->owner->geoSearch(array(
                    'q' => $name,
                ));

        $json = json_decode($json);

        if (isset($json->result)) {
            foreach ($json->result as $jsonObj) {
                if (isset($jsonObj->centroid) && $jsonObj->centroid != null) {
                    $geom = $jsonObj->centroid;
                } else {
                    $geom = $jsonObj->selection; // if centroid not set in db
                }

                preg_match('/[0-9. ]+/', $geom, $matches);
                if (count($matches)) {
                    $matches = explode(' ', $matches[0]);
                    $ret = new stdClass();
                    $ret->lon = $matches[0];
                    $ret->lat = $matches[1];
                    break;
                }
            }
        }
        return $ret;
    }

}
