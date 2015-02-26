<?php

class Iphone_Widget_FoursquarePointListController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->points = Engine_Api::_()->getApi('core', 'iphone')->getPointsForFoursquare();
    //echo '<pre>'; print_r($this->view->points); echo '</pre>'; exit();
  }
}
