<?php

// INCLUDES
require_once($_SERVER['DOCUMENT_ROOT']."/WS/API/core.php");
if($_SERVER['DEV']) error_reporting(E_ALL);

// FUNCTIONS


// SERVICE
$jsonService = "SERVICE"; class SERVICE {

    public static function addScore($eventID, $score, $comment="") {
        
        // DATE
        $date = date("Y-m-d H:i:s"); 

        $query = mysql_query(
            "INSERT INTO scores (EVENT_ID, SCORE, DATE, COMMENT) VALUES (".
                toSQL($eventID).','.
                toSQL($score).','.
                toSQL($date).','.
                toSQL($comment).
            ")"
        );

        if($query === FALSE) {
            cThrow(ERR_SQL);
        }

        return NULL;

    }

    public static function getScores($eventID) {

        // ...
        $query = mysql_query(
            "SELECT AVG(SCORE) FROM scores ".
            "WHERE EVENT_ID=".toSQL($eventID)
        );

        if($query==FALSE) {
            cThrow(ERR_SQL);
        }

        $meanScore = mysql_fetch_row($query);
        $meanScore = $meanScore[0];
        @mysql_free_result($query);
        
        // ...        
        $query = mysql_query(
            "SELECT SCORE, COMMENT FROM scores ".
            "WHERE EVENT_ID=".toSQL($eventID)
        );

        if($query==FALSE) {
            cThrow(ERR_SQL);
        }

        // ...
        $results = array();
        while(($row=mysql_fetch_array($query)) !== FALSE) {
            $results[] = array(
                "score" => $row['SCORE'],
                "comment" => $row['COMMENT']
            );
        }

        return array(
            "mean" => $meanScore,
            "all" => $results
        );
    }

    public static function getDist($centerLat, $centerLong, $lat, $long){
        $olat = $lat;
        $olon = $long;
        $R = 6371; // earth's radius in km
        $dLat = deg2rad($centerLat - $olat);
        $dLon = deg2rad($centerLong - $olon);
        $rolat = deg2rad($olat);
        $rlat = deg2rad($centerLat);
        $a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($rolat) * cos($rlat); 
        $c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
        $distance = $R * $c;
        return $distance;
    }

    public static function getBestEvent($lat, $long, $date=-1) {
        $datas = SERVICE::getEvents($date);
        $bestEvent = NULL;
        $bestDistance = 3000;

        for($i=0; $i<count($datas); $i++) {
            
            $data = $datas[$i];
            $cDist = SERVICE::getDist($lat,$long,$data['latitude'],$data['longitude']);
            if($cDist < $bestDistance) { $bestEvent = $data; $bestDistance = $cDist; }

        }

        return $bestEvent;

    }

    public static function getBestEvents($lat, $long, $date=-1) {
        $datas = SERVICE::getEvents($date);

        for($i=0; $i<count($datas); $i++) {
            
            $data = $datas[$i];
            $data['distance'] = SERVICE::getDist($lat,$long,$data['latitude'],$data['longitude']);
            $datas[$i] = $data;

        }

        usort($datas, function($a,$b) {
            return $a['distance']>$b['distance'];
        });

        $finalDatas = array();
        for($i=0; $i<count($datas) && $i<50; $i++) {
            $finalDatas[] = $datas[$i];
        }

        return $finalDatas;

    }


    public static function getEvents($date=-1) {

        if ($date==-1) $date="20120718"; //$date=date("Ymd");
        if(strstr($date,'-')!=FALSE) {
            $date = explode('-', $date);
            $date[0] = str_pad($date[0], 4, '0', STR_PAD_LEFT);
            $date[1] = str_pad($date[1], 2, '0', STR_PAD_LEFT);
            $date[2] = str_pad($date[2], 2, '0', STR_PAD_LEFT);
            $date = implode("", $date);
        }
        $date = preg_replace("#[^0-9]#", "", $date);
        $date = substr($date, 0, 8);

        // DOWNLOAD or use cache
        if($_SERVER['DEV']) {
            $cachePath = "$date.json";
        } else {
            $cachePath = "/tmp/$date.json";
        }

        if(file_exists($cachePath) && (time()-filemtime($cachePath))<60*60*2 && filemtime($cachePath)>filemtime(__FILE__)) {
            $path = $cachePath;
            $cache = FALSE;
        } else {
            $path = "http://data.appsforghent.be/gentsefeesten/$date.json";
            $cache=$cachePath;
        }
        $datas_str = file_get_contents($path);
        if($cache!==FALSE) { file_put_contents($cache, $datas_str); }

        // DECODE
        $datas = json_decode($datas_str, TRUE);

        // FILTER
        $datas = $datas[$date];
        $newData = array();
        
        for($i=0; $i<count($datas); $i++) {

            $data = $datas[$i];

            $newData[] = array(
                'id' => hash('sha256', $data['Titel'].$data['Datum'].$data['Begin'].$data['latitude'].$data['longitude']),
                'twitter' => 'GF_'.strtoupper(hash('crc32b', $data['Titel'])),
                'title' => $data['Titel'],
                'description' => $data['Omschrijving'],
                'latitude' => (double)$data['latitude'],
                'longitude' => (double)$data['longitude'],
                'day' => $data['Datum'],
                'start'=>$data['Begin'],
                'end' => $data['Einde'],
                'location' => $data['Plaats'],
                'amountPeople' => $data['Aantal Deelnemers'],
                'street' => $data['Straat'],
                'streetNbr' => $data['Huisnr'],
                'city' => $data['Gemeente']
            );
        }

        return $newData;
    }

    public static function getEvent($eventID, $date=-1) {
        $datas = SERVICE::getEvents($date);

        for($i=0; $i<count($datas); $i++) {
            
            $data = $datas[$i];
            if($data['id'] == $eventID) { 
                
                // ...
                $query = mysql_query(
                    "SELECT SUM(SCORE) FROM scores ".
                    "WHERE EVENT_ID=".toSQL($eventID)
                );

                if($query==FALSE) {
                    cThrow(ERR_SQL);
                }

                $meanScore = mysql_fetch_row($query);
                $meanScore = $meanScore[0];
                @mysql_free_result($query);

                $data['score'] = $meanScore;

                return $data;
            }

        }

        cThrow(404);

    }

}

// IF NOT INCLUDE
if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) {

    // ACTIVATE WEBSERVICE
	include(pathto('/API/json.php'));

}

	
?>