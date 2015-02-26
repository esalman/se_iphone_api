<?php

class Iphone_GalleryController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $points = Engine_Api::_()->getDbTable('points', 'iphone')->fetchAll();
    $paginator = Zend_Paginator::factory($points);
    //$friends->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(24);
    $this->view->points = $paginator;
  }
  
  public function browseAction()
  {
    $points = Engine_Api::_()->getDbTable('points', 'iphone')->fetchAll();
    $paginator = Zend_Paginator::factory($points);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(24);
    $this->view->points = $paginator;
  }
  
  public function viewAction()
  {
    $this->view->point = Engine_Api::_()->getDbTable('points', 'iphone')->fetchRow('id = '.$this->_getParam('id'));
  }
}
