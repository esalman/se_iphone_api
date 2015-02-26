<?php

class Iphone_Model_Iphone extends Core_Model_Item_Abstract
{
 // add photo function
   public function setPhoto2($photo)
  {    
   if($photo){
    $file =APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'; //D:\xampp\htdocs\sev4\temporary

    $params = array(
     // 'parent_type' => $this->getType(),
      'parent_type' => 'iphone',
      'parent_id' => $this->getIdentity()
     // 'parent_id' => 100
    ); //Array ( [parent_type] => test [parent_id] => 20 )

   print_r($params);
   exit;
    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
/*    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();
*/
    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
/*    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();
*/
    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
//    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
//   $iSquare = $storage->create($path.'/is_'.$name, $params);

//    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
//    $iMain->bridge($iSquare, 'thumb.icon');

    // Update row
  echo  $this->photo_id = $iMain->file_id;
  exit;
   // $this->save();

    return $this;
  }
  
}