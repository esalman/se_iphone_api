<?php

class Iphone_Widget_FoursquarePointMapController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->points = $points = Engine_Api::_()->getApi('core', 'iphone')->getPointsForFoursquare();
    //echo '<pre>'; print_r($points); echo '</pre>'; exit();
    
    if(count($points)){
      $this->view->pointsEncoded = json_encode($points);
    }
  }
}