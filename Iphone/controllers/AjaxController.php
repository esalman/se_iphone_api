<?php

class Iphone_AjaxController extends Core_Controller_Action_Standard
{
  public function init() {
    // DISABLE DEFAULT LAYOUT RENDERING
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
  }
  
  public function getnextAction()
  {
    // CHECK IF SIGHTING ID SET IN SESSION
    $max_id = isset($_SESSION['max_sighting_ajax_id']) ? $_SESSION['max_sighting_ajax_id'] : 0;
    $id = isset($_GET['id']) ? $_GET['id'] : ( isset($_SESSION['sighting_ajax_id']) ? $_SESSION['sighting_ajax_id'] : 0 );
    // BUILD SQL
    $types = Engine_Api::_()->getDbTable('types', 'iphone')->info('name');
    $table = Engine_Api::_()->getDbTable('points', 'iphone');
    $points = $table->info('name');
    $select = $table->select()->from($table)
                              ->setIntegrityCheck(false)
                              ->join($types, $types.'.id = '.$points.'.type', array('type_id' => 'id', 'type_title' => 'title', 'email', 'icon') )
                              ->order($points.'.id DESC')
                              ->limit(1);
    if ( $id ) {
      if ( isset($_GET['id']) )
        $select->where($points.'.id = '.$_GET['id']);
      else 
        $select->where($points.'.id < '.$id.' OR '.$points.'.id > '.$max_id);
    }
    $rows = $table->fetchAll($select);
    
    $temp = array();
    if ( $rows->count() ) {
      foreach ( $rows as $row ) {
        if ( $row->id > $_SESSION['max_sighting_ajax_id'] ) $_SESSION['max_sighting_ajax_id'] = $row->id;
        $_SESSION['sighting_ajax_id'] = $row->id;
        $temp['sight_info']['sight_id'] = $row->id;
        $temp['sight_info']['type'] = $row->type;
        $temp['sight_info']['user_id'] = $row->user_id;
        $temp['sight_info']['lat'] = $row->lat;
        $temp['sight_info']['lng'] = $row->lng;
        $temp['sight_info']['title'] = $row->title;
        $temp['sight_info']['description'] = $row->description;
        if ( $row->image ) {
          $thumb = Engine_Api::_()->getItem('storage_file', $row->image+1);
          $row->image = "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$thumb->storage_path;
        }
        else {
          $row->image = "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/admin/logo-jpg3.jpg";
        }
        $temp['sight_info']['image'] = $row->image;
        $temp['sight_info']['datecreated'] = $row->datecreated;
        $temp['sight_info']['type_title'] = $row->type_title;
        $temp['sight_info']['icon'] = $row->icon;
      }
    }
    else {
      $_SESSION['max_sighting_ajax_id'] = null;
    }
    echo json_encode($temp);
  }
  
  public function getallAction() {
    $types = Engine_Api::_()->getDbTable('types', 'iphone')->info('name');
    $table = Engine_Api::_()->getDbTable('points', 'iphone');
    $points = $table->info('name');
    $select = $table->select()->from($table)
                              ->setIntegrityCheck(false)
                              ->join($types, $types.'.id = '.$points.'.type', array('type_id' => 'id', 'type_title' => 'title', 'email', 'icon') )
                              ->order($points.'.id DESC')
                              ->limit(50);
    $rows = $table->fetchAll($select);
    $arr = array();
    foreach ( $rows as $row ) {
      $temp = array();
      $temp['sight_info']['sight_id'] = $row->id;
      $temp['sight_info']['type'] = $row->type;
      $temp['sight_info']['user_id'] = $row->user_id;
      $temp['sight_info']['lat'] = $row->lat;
      $temp['sight_info']['lng'] = $row->lng;
      $temp['sight_info']['title'] = $row->title;
      $temp['sight_info']['description'] = $row->description;
      $thumb = Engine_Api::_()->getItem('storage_file', $row->image+1);
      $temp['sight_info']['image'] = "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$thumb->storage_path;
      $temp['sight_info']['datecreated'] = $row->datecreated;
      $temp['sight_info']['type_title'] = $row->type_title;
      $temp['sight_info']['icon'] = $row->icon;
      $arr[] = $temp;
    }
    echo json_encode($arr);
  }
}
