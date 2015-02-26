<?php

class Iphone_Api_Core extends Core_Api_Abstract
{
    function getTypes()
    {
        return Engine_Api::_()->getDbTable('types', 'iphone')->fetchAll();
    }
    
    function getUserToken( $userId = null )
    {
        if ( $userId ) {
            $row = Engine_Api::_()->getDbTable('token', 'iphone')->fetchRow("user_id = ".$userId);
            return $row;
        }
        return null;
    }
    
    function getDefaultLocation()
    {
        return explode(',', Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.defaultloc', '0, 0'));
    }
    
    function getProximityPoints($location, $radius)
    {
        $lat1 = $location['lat'];
        $lng1 = $location['lng'];
        $types = Engine_Api::_()->getDbTable('types', 'iphone')->info('name');
        $table = Engine_Api::_()->getDbTable('points', 'iphone');
        $points = $table->info('name');
        $select = $table->select()->from($table)
                                  ->setIntegrityCheck(false)
                                  ->join($types, $types.'.id = '.$points.'.type', array('type_id' => 'id', 'type_title' => 'title', 'email', 'icon') )
                                  ->order($points.'.id DESC');
        $rows = $table->fetchAll($select);
        
        $points = array();
        foreach ( $rows as $row ) {
            // DISTANCE CALCULATION FORMULA
            $lat2 = $row->lat;
            $lng2 = $row->lng;
            $earth_radius = 6371;
            
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lng2 - $lng1);
            
            $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * asin(sqrt($a));
            $d = $earth_radius * $c;
            
            if ( $d < $radius ) {
                $arr = array();
                $arr['id'] =  $row->id;
                $arr['type'] =  $row->type;
                $arr['type_title'] =  $row->type_title;
                $arr['user_id'] =  $row->user_id;
                $arr['lat'] =  $row->lat;
                $arr['lng'] =  $row->lng;
                $xml = simplexml_load_file('http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$row->lat.','.$row->lng.'&sensor=true');
                $arr['Address'] = '';
                foreach ( $xml->result[0]->address_component as $xmlResult ) {
                    $arr['address'] .= $xmlResult->long_name.', ';
                }
                $arr['address'] = substr($arr['address'], 0, -2);
                $arr['title'] =  $row->title;
                $arr['description'] =  $row->description;
                $arr['image'] =  $row->image;
                $arr['icon'] =  $row->icon;
                $arr['datecreated'] =  $row->datecreated;
                $arr['dateupdated'] =  $row->dateupdated;
                $arr['alert'] =  $row->alert;
                $arr['d'] =  $d;
                $points[] = $arr;
            }
        }
        return $points;
    }
  
    function getRandomPoints( $limit = 50 )
    {
        $lat1 = $location['lat'];
        $lng1 = $location['lng'];
        $types = Engine_Api::_()->getDbTable('types', 'iphone')->info('name');
        $table = Engine_Api::_()->getDbTable('points', 'iphone');
        $points = $table->info('name');
        $select = $table->select()->from($table)
                                  ->setIntegrityCheck(false)
                                  ->join($types, $types.'.id = '.$points.'.type', array('type_id' => 'id', 'type_title' => 'title', 'email', 'icon') )
                                  ->order($points.'.id DESC')
                                  ->limit($limit);
        $rows = $table->fetchAll($select);
        
        $points = array();
        foreach ( $rows as $row ) {
            $arr = array();
            $arr['id'] =  $row->id;
            $arr['type'] =  $row->type;
            $arr['type_title'] =  $row->type_title;
            $arr['user_id'] =  $row->user_id;
            $arr['lat'] =  $row->lat;
            $arr['lng'] =  $row->lng;
            $xml = simplexml_load_file('http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$row->lat.','.$row->lng.'&sensor=true');
            $arr['Address'] = '';
            foreach ( $xml->result[0]->address_component as $xmlResult ) {
                $arr['address'] .= $xmlResult->long_name.', ';
            }
            $arr['address'] = substr($arr['address'], 0, -2);
            $arr['title'] =  $row->title;
            $arr['description'] =  $row->description;
            $arr['image'] =  $row->image;
            $arr['icon'] =  $row->icon;
            $arr['datecreated'] =  $row->datecreated;
            $arr['dateupdated'] =  $row->dateupdated;
            $arr['alert'] =  $row->alert;
            $arr['d'] =  $d;
            $points[] = $arr;
        }
        return $points;
    }
  
    function getPoint($id)
    {
        if ( $id ) {
            $types = Engine_Api::_()->getDbTable('types', 'iphone')->info('name');
            $table = Engine_Api::_()->getDbTable('points', 'iphone');
            $points = $table->info('name');
            $select = $table->select()->from($table)
                                      ->setIntegrityCheck(false)
                                      ->join($types, $types.'.id = '.$points.'.type', array('type_id' => 'id', 'type_title' => 'title', 'email', 'icon') )
                                      ->where($points.'.id = '.$id)
                                      ->order($points.'.id DESC');
            $row = $table->fetchRow($select);
            
            $points = array();
            $arr = array();
            $arr['id'] =  $row->id;
            $arr['type'] =  $row->type;
            $arr['type_title'] =  $row->type_title;
            $arr['user_id'] =  $row->user_id;
            $arr['lat'] =  $row->lat;
            $arr['lng'] =  $row->lng;
            $xml = simplexml_load_file('http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$row->lat.','.$row->lng.'&sensor=true');
            $arr['Address'] = '';
            foreach ( $xml->result[0]->address_component as $xmlResult ) {
                $arr['address'] .= $xmlResult->long_name.', ';
            }
            $arr['address'] = substr($arr['address'], 0, -2);
            $arr['title'] =  $row->title;
            $arr['description'] =  $row->description;
            $arr['image'] =  $row->image;
            $arr['icon'] =  $row->icon;
            $arr['datecreated'] =  $row->datecreated;
            $arr['dateupdated'] =  $row->dateupdated;
            $arr['alert'] =  $row->alert;
            $points[] = $arr;
            return $points;
        }
    }  
    
    function getPointsByCategory($id)
    {
        if ( $id ) {
            $types = Engine_Api::_()->getDbTable('types', 'iphone')->info('name');
            $table = Engine_Api::_()->getDbTable('points', 'iphone');
            $points = $table->info('name');
            $select = $table->select()->from($table)
                                      ->setIntegrityCheck(false)
                                      ->join($types, $types.'.id = '.$points.'.type', array('type_id' => 'id', 'type_title' => 'title', 'email', 'icon') )
                                      ->where($points.'.type = '.$id)
                                      ->order($points.'.id DESC');
            $rows = $table->fetchAll($select);
            
            if ( count($rows) ) {
                $points = array();
                foreach ( $rows as $row ) {
                    $arr = array();
                    $arr['id'] =  $row->id;
                    $arr['type'] =  $row->type;
                    $arr['type_title'] =  $row->type_title;
                    $arr['user_id'] =  $row->user_id;
                    $arr['lat'] =  $row->lat;
                    $arr['lng'] =  $row->lng;
                    $xml = simplexml_load_file('http://maps.googleapis.com/maps/api/geocode/xml?latlng='.$row->lat.','.$row->lng.'&sensor=true');
                    $arr['Address'] = '';
                    foreach ( $xml->result[0]->address_component as $xmlResult ) {
                        $arr['address'] .= $xmlResult->long_name.', ';
                    }
                    $arr['address'] = substr($arr['address'], 0, -2);
                    $arr['title'] =  $row->title;
                    $arr['description'] =  $row->description;
                    $arr['image'] =  $row->image;
                    $arr['icon'] =  $row->icon;
                    $arr['datecreated'] =  $row->datecreated;
                    $arr['dateupdated'] =  $row->dateupdated;
                    $arr['alert'] =  $row->alert;
                    $points[] = $arr;
                }
                return $points;
            }
        }
    }

    function getPointsForFoursquare()
    {
        ob_start();
        require_once 'application/libraries/Foursquareapi/EpiCurl.php';
        require_once 'application/libraries/Foursquareapi/EpiFoursquare.php';
        $viewer = Engine_Api::_()->user()->getViewer();

        // Get Client_id and Client_secret code
        $clientId = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientid', 'DYFMCNMP3Y5GXHWH33TYN03RUW0BX2CO00G3WKV5TEYXVOZ3');
        $clientSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientsecret', 'SPD30HRILMYRE1CRTUJOIOMGRVOJP0JU3SJCQMW2PXVATTMS');

        // Get User Lat-Lng-Radius
        $tokenTable = Engine_Api::_()->getDbTable('token', 'iphone');
        $select = $tokenTable->select()
                             ->where('user_id = ?', $viewer->getIdentity());
        $userGeoInfo = $tokenTable->fetchRow($select);
        $ll = $userGeoInfo->lat.','.$userGeoInfo->lng;
        $radius = $userGeoInfo->radius;

        //$redirectUri = 'http://www.socialxperience.com/index.php/iphone/foursquare/index';
        //$accessToken = $this->access_token;

        //$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
        $fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
        //$fsObj->setAccessToken($accessToken);

        $venue = $fsObjUnAuth->get('/venues/search', array('ll' => $ll));
        //echo '<pre>'; print_r($venue->response->groups[0]->items); echo '</pre>';

        $points = array();
        foreach($venue->response->groups[0]->items as $item)
        {
            $arr = array();
            $arr['id'] =  $item->id;
            $arr['type'] =  $item->categories[0]->name;
            $arr['type_title'] =  $item->categories[0]->name;
            $arr['user_id'] =  $viewer->getIdentity();
            $arr['lat'] =  $item->location->lat;
            $arr['lng'] =  $item->location->lng;
            $arr['address'] = $item->location->address.', '.$item->location->city.', '.$item->location->state;
            $arr['title'] =  $item->name;
            $arr['description'] =  '';//$row->description;
            $arr['image'] =  $item->categories[0]->icon;
            $arr['icon'] =  $item->categories[0]->icon;
            $arr['datecreated'] =  '';//$row->datecreated;
            $arr['dateupdated'] =  '';//$row->dateupdated;
            $arr['alert'] =  '';//$row->alert;
            $points[] = $arr;
        }
        return $points;
    }

    function enableFoursquare($user_id = NULL)
    {
      $table = Engine_Api::_()->getDbTable('foursquare', 'iphone');
      $table->update(array('enable' => 1), 'user_id = '.$user_id);

      // Redirect to foursquare authentication
      header("Location: index");
      //return $this->_helper->redirector->gotoRoute(array('action' => 'index'), '');
    }
	
    function foursquareAuthentication($user_id, $foursquare_id, $access_token)
    {
      $table = Engine_Api::_()->getDbTable('foursquare', 'iphone');
      $data = array(
                    'user_id'		=>	$user_id,
                    'foursquare_id'	=>	$foursquare_id,
                    'access_token'	=>	$access_token
      );
      $table->insert($data);
    }

    function getLatLng($address = NULL)
    {
	// This code is to fetch lat and long of any specific location
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/geocode/xml?address='.urlencode($address).'&sensor=true');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$xml_response = curl_exec($ch);
	curl_close($ch);

	$xml = new SimpleXMLElement($xml_response);
        $data = array(
            'lat'   =>  $xml->result->geometry->location->lat,
            'lng'   =>  $xml->result->geometry->location->lng
        );
        return $data;
    }
    function getLatLng2($address = NULL)
    {
        $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');

        $output= json_decode($geocode);

        $lat = $output->results[0]->geometry->location->lat;
        $long = $output->results[0]->geometry->location->lng;
    }
}

