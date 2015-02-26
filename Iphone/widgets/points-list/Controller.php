<?php

class Iphone_Widget_PointsListController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    switch ( $request->getActionName() ) {
      case 'category':
        $this->view->points = $points = Engine_Api::_()->getApi('core', 'iphone')->getPointsByCategory($request->get('id'));
        break;
      case 'view':
        $this->view->points = $points = Engine_Api::_()->getApi('core', 'iphone')->getPoint($request->get('id'));
        break;
      case 'index':
        $radius = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.defaultrad', '500');
        
        $request->get('searchNear');
        if ( $request->get('searchNear') ) {
          $xml = simplexml_load_file('http://maps.googleapis.com/maps/api/geocode/xml?address='.urlencode($request->get('searchNear')).'&sensor=true');
          $lat = $xml->result->geometry->location->lat;
          $lng = $xml->result->geometry->location->lng;
        }
        else {
          // GET USERS CURRENT LOCATION
          $viewer = Engine_Api::_()->user()->getViewer();
          $token = null;
          if ( $viewer->getIdentity() ) {
            $token = Engine_Api::_()->getApi('core', 'iphone')->getUserToken($viewer->getIdentity());
            $lat = $token->lat;
            $lng = $token->lng;
          }
          
          // GET BUSINESS LISTING IN CURRECT LOCATION
          if ( !$token ) {
            $defaultLocation = Engine_Api::_()->getApi('core', 'iphone')->getDefaultLocation();
            $lat = $defaultLocation[0];
            $lng = $defaultLocation[1];
          }
        }
        
        $this->view->location = $location = array('lat' => $lat, 'lng' => $lng);
        $this->view->points = $points = Engine_Api::_()->getApi('core', 'iphone')->getProximityPoints($location, $radius);
        
        if ( !count($points) ) {
          $this->view->points = $points = Engine_Api::_()->getApi('core', 'iphone')->getRandomPoints();
        }
        break;
      default:
        break;
    }
  }
}
