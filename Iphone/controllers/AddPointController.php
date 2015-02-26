<?php

class Iphone_AddPointController extends Core_Controller_Action_User
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $typeTable = Engine_Api::_()->getDbTable('types', 'iphone');
    $this->view->types = $typeTable->fetchAll($typeTable->select());

    if(!$this->getRequest()->isPost())
    {
       return;
    }
    elseif($this->getRequest()->isPost())
    {
      $errorMessage = '';
      $empty = new Zend_Validate_NotEmpty();

      if(!$empty->isValid($_POST['address'])){$errorMessage .= '<li>Address can not be blank!</li>';}
      if(!$empty->isValid($_POST['name'])){$errorMessage .= '<li>Name can not be blank!</li>';}
      if(!$empty->isValid($_POST['description'])){$errorMessage .= '<li>Description can not be blank!</li>';}
      if(!$empty->isValid($_POST['type_id'])){$errorMessage .= '<li>Select Type ID!</li>';}
      $this->view->assign('errorMessage', $errorMessage);

      if(empty($errorMessage))
      {
        if(!empty($_FILES['photo']['name']))
        {
            $photo = $_FILES['photo']['name'];
            $photo_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;

            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
            $photo_id = $photo ? $this->photoUpload($photo) : '';
        }else{$photo_id = 0;}

        $llData = Engine_Api::_()->iphone()->getLatLng($_POST['address']);
        $insert_values = array(
            'type'          => $_POST['type_id'],
            'user_id'       => $viewer->getIdentity(),
            'lat'           => $llData['lat'],
            'lng'           => $llData['lng'],
            'title'         => $_POST['name'],
            'description'   => $_POST['description'],
            'image'         => $photo_id,
            'datecreated'   => time()
        );

        $table = Engine_Api::_()->getDbTable('points', 'iphone');
        $table->insert($insert_values);

        $this->view->statusMessage = 'Point Added Successfully! !!';
      }
    }
  }

  public function photoUpload($photo)
  {
    if($photo){
      $file =APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;
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
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Update row
    $id = $this->photo_id = $iMain->file_id;
    // $this->save();

    return $id;
  }
}
