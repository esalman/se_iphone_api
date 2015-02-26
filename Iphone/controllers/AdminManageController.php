<?php

class Iphone_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('iphone_admin_main', array(), 'iphone_admin_main_points');
    
    $table = Engine_Api::_()->getDbTable('points', 'Iphone');
    $points = $table->info('name');
    $select = $table->select()->from($table)
                              ->order($points.'.id DESC');
    $rows = $table->fetchAll($select);
    
    $points = Zend_Paginator::factory($rows);
    //$points->setCurrentPageNumber($page);
    $points->setItemCountPerPage(30);
    $this->view->points = $points;
  }
  
  
  public function editAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('iphone_admin_main', array(), 'iphone_admin_main_points');
    
    if ( $this->getRequest()->isPost() ) {
      extract($_POST);
      //echo '<pre>'; print_r($_POST); echo '</pre>';
      //echo '<pre>'; print_r($_FILES); echo '</pre>';
      
      $photo = $_FILES['new_image']['name'];
      if ( $photo ) {
        $photo_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;
        move_uploaded_file($_FILES['new_image']['tmp_name'], $photo_path);
        $photo_id = $this->setPhoto($photo);
      }
      
      $insert_values = array(
        'type' => $type,
        'lat' => $lat,
        'lng' => $lng,
        'title' => $title,
        'description' => $description
        );
      if ( $photo_id ) {
        $insert_values['image'] = $photo_id;
      }
      else if ( $delete_image == 'yes' ) {
        $insert_values['image'] = '';
      }
      $where = $table->getAdapter()->quoteInto('id = ?', $id);
      $table->update($insert_values, $where);
      $this->view->pointSuccessMessage = 'The changes have been saved.';
    }
    
    $this->view->point = Engine_Api::_()->getDbTable('points', 'iphone')->fetchRow('id = '.$this->_getParam('id'));
    $this->view->types = Engine_Api::_()->getDbTable('types', 'iphone')->fetchAll();  
  }
  
  
  public function deleteAction()
  {
    if ( $this->getRequest()->isPost() ) {
      $table = Engine_Api::_()->getDbTable('points', 'iphone');
      $where = $table->getAdapter()->quoteInto('id=?', $this->_getParam('id'));
      $table->delete($where);
      
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format'=> 'smoothbox',
        'messages' => array('Point deleted.')
        ));
    }
    else {
      $this->view->point = Engine_Api::_()->getDbTable('points', 'iphone')->fetchRow('id = '.$this->_getParam('id'));
    }
  }
  
  public function typeAction ()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('iphone_admin_main', array(), 'iphone_admin_main_type');
    
    $this->view->types = Engine_Api::_()->getDbTable('types', 'iphone')->fetchAll();
  }
  
  public function settingsAction ()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('iphone_admin_main', array(), 'iphone_admin_main_settings');
    
    if ( $this->_getParam('gmap_api_key') != '' ) {
      $table = Engine_Api::_()->getDbTable('settings', 'core');
      $insert_values = array( 'value' => $this->_getParam('gmap_api_key') );
      $where = $table->getAdapter()->quoteInto('name = ?', 'iphone.gmap_api_key');
      $table->update($insert_values, $where);
      //$this->_helper->redirector->gotoRoute(array('module'=>'iphone','controller'=>'manage','action' => 'index'));
    }
    
    if ( $this->_getParam('latlng') != '' ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('iphone.defaultloc', $this->_getParam('latlng'));
      //$this->_helper->redirector->gotoRoute(array('module'=>'iphone','controller'=>'manage','action' => 'index'));
    }
    if ( $this->_getParam('radius') != '' ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('iphone.defaultrad', $this->_getParam('radius'));
      //$this->_helper->redirector->gotoRoute(array('module'=>'iphone','controller'=>'manage','action' => 'index'));
    }
    if($this->_getParam('foursquare_settings') != '')
    {
        $this->foursquareSettings();
    }
  }
  
  
  public function setPhoto($photo)
  {     
    if($photo){
      $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;
    }
    else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $userid= $viewer->getIdentity();
   
    $params = array(     
      'parent_type' => 'iphone',
      'parent_id' => $userid     
    ); 
   
    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
     // ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(300, 230)
      ->write($path.'/in_'.$name)
      ->destroy();
    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(40, 30)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    // $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    //$iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Update row
    $id = $this->photo_id = $iMain->file_id;
    // $this->save();

    return $id;
  }

  public function foursquareSettings()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('iphone_admin_main', array(), 'iphone_admin_main_fssettings');
    $clientId = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientid', 'DYFMCNMP3Y5GXHWH33TYN03RUW0BX2CO00G3WKV5TEYXVOZ3');
    $clientSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientsecret', 'SPD30HRILMYRE1CRTUJOIOMGRVOJP0JU3SJCQMW2PXVATTMS');
    $this->view->clientId = $clientId;
    $this->view->clientSecret = $clientSecret;

    if(!$this->getRequest()->isPost()){
        return;
    }
    elseif($this->getRequest()->isPost())
    {
        $table = Engine_Api::_()->getDbTable('settings', 'core');
        $table->update(array('value' => $_POST['client_id']), "name = 'iphone.foursquareclientid'");
        $table->update(array('value' => $_POST['client_secret']), "name = 'iphone.foursquareclientsecret'");
        $this->view->statusMessage = 'FourSquare Settings Updated Successfully! !!';
    }   
  }
}
