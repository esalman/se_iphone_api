<?php

class Iphone_ApiController extends Core_Controller_Action_Standard
{
  private $_str; // OUTPUT XML
  private $_authResult; // LOGIN AUTHENTICATION OBJECT
  
  public function init() {
    // DISABLE DEFAULT LAYOUT RENDERING
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    // OUTPUT XML
    header("Content-Type: text/xml");
    header("Expires: 0");    
    $this->_str = '<?xml version="1.0" encoding="utf-8"?><response>';
    
    // LOGIN CHECK
    $email = '';
    extract($_POST);
    if ( $this->getRequest()->getActionName() == 'loadbadges' ) {
      // SKIP
    }
    // DELETE POINTS
    else if ( $this->getRequest()->getActionName() == 'deletepoints' ) {
      $table = Engine_Api::_()->getDbTable('points', 'iphone');
      $where = $table->getAdapter()->quoteInto('1=?', 1);
      $table->delete($where);
      $this->_str .= '<success>All points deleted.</success>';
      $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
      $this->_str = str_replace('&', '&amp;', $this->_str);
      echo $this->_str .= '</response>';
      exit();
    }
    // EMPTY EMAIL
    else if ( !$email ) {
      // IF EMAIL EMPTY
      $this->_str .= '<error>Authentication error: A record with the supplied identity could not be found.</error>';
      $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
      $this->_str = str_replace('&', '&amp;', $this->_str);
      echo $this->_str .= '</response>';
      exit();
    }
    // NOT POST DATA
    else if ( !$this->getRequest()->isPost() ) {
      // IF FORM METHOD IS NOT POST
      $this->_str .= '<error>No action taken.</error>';
      $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
      $this->_str = str_replace('&', '&amp;', $this->_str);
      echo $this->_str .= '</response>';
      exit();
    }
    // CHECK AUTHENTICATION
    else {
      $table = Engine_Api::_()->getItemTable('user');
      $row = $table->fetchRow($table->select()->where('email = ?', $email)->limit(1));
      if ( $row !== null ) {
        $this->_authResult = Engine_Api::_()->user()->authenticate($email, $password);
        if ( !$this->_authResult->isValid() ) {
          // INVALID AUTHENTICATION
          $this->_str .= '<error>Authentication error: '.implode('', $this->_authResult->getMessages()).'</error>';
          $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
          $this->_str = str_replace('&', '&amp;', $this->_str);
          echo $this->_str .= '</response>';
          exit();
        }
      }
      else if ( $this->getRequest()->getActionName() != 'register' ) {
        $this->_str .= '<error>Invalid user.</error>';
        $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
        $this->_str = str_replace('&', '&amp;', $this->_str);
        echo $this->_str .= '</response>';
        exit();
      }
    }
  }
  
  // INDEX
  public function indexAction() {
    // NOTHING DOING
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '<error>No action specified.</error></response>';
  }
  
  // LOGIN
  public function loginAction() {
    extract($_POST);
    
    // THINGS ARE DONE IN INIT() ALREADY
    $this->_str .= '<success>'.implode('', $this->_authResult->getMessages()).'</success>';
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOGOUT
  public function logoutAction() {
    extract($_POST);
    
    // Check if already logged out
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    if( !$viewer->getIdentity() ) {
      $this->_str .= "<error>You are already logged out.</error>";
    }
    else {
      // Test activity @todo remove
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $viewer, 'logout');
  
      $table = $this->_helper->api()->getItemTable('user');
      $onlineTable = $this->_helper->api()->getDbtable('online', 'user')
      ->delete(array(
          'user_id = ?' => $viewer->getIdentity(),
        ));
  
      // Facebook
      if ('none' != Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
        $fb_id = Engine_Api::_()->getDbtable('facebook', 'user')->find($viewer->getIdentity())->current();
        if ($fb_id && $fb_id->facebook_uid) {
          $facebook = User_Model_DbTable_Facebook::getFBInstance();
          if ($facebook->getSession()) {
            Engine_Api::_()->user()->getAuth()->clearIdentity();
            $this->_helper->redirector->gotoUrlAndExit($facebook->getLogoutUrl());
            exit;
          }
        }
      }
      
      // Logout
      Engine_Api::_()->user()->getAuth()->clearIdentity();
      $this->_str .= "<success>You are now logged out.</success>";
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // REGISTER
  public function registerAction() {
    extract($_POST);
    
    $status = 1;
    // Sent both or neither username/email
    if( strlen($password) < 6 || strlen($password) > 32 )
    {
      $this->_str .= '<error>Password must be 6-32 characters long.</error>';
      $status = 0;
    }
    
    // Username must be alnum
    if( $status )
    {
      $validators = array(
        'username' => array(
          'NotEmpty' => true,
          'Alnum' => true,
          'StringLength' => array(4, 64),
          'Regex' => array('/^[A-Za-z][A-Za-z0-9]+$/'),
        )
      );
      
      $input = new Zend_Filter_Input(null, $validators, array('username' => $username));
      if ( !$input->isValid() ) {
        $this->_str .= '<error>Username must be alphanumeric, minimum 4 letters and must start with a letter.</error>';
        $status- 0;
      }
      else {
        $table = Engine_Api::_()->getItemTable('user');
        $row = $table->fetchRow($table->select()->where('username = ?', $username)->limit(1));
        if ( $row !== null ) {
          $this->_str .= '<error>Username is empty or already taken.</error>';
          $status = 0;
        }
      }
    }

    if( $email && $status )
    {
      $validator = new Zend_Validate_EmailAddress();
      if( !$validator->isValid($email) )
      {
        $this->_str .= '<error>Email address is invalid.</error>';
        $status = 0;
      }
      else {
        $table = Engine_Api::_()->getItemTable('user');
        $row = $table->fetchRow($table->select()->where('email = ?', $email)->limit(1));
  
        if ( $row !== null ) {
          $this->_str .= '<error>Email address already taken.</error>';
          $status = 0;
        }
      }
    }
    
    /***
    if ( ( !is_numeric($profile_type) || !is_numeric($badge_id) ) && $status ) {
      $this->_str .= '<error>Missing profile type or badge id.</error>';
      $status = 0;
    }
    ***/
    
    if ( $status ) {
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $random =  ($settings->getSetting('user.signup.random', 0) == 1);
      $data = array();
      $data['email'] = $email;
      $data['password'] = $password;
      $data['passconf'] = $password;
      $data['username'] = $username;
      $data['profile_type'] = '1';
      $data['timezone'] = 'US/Pacific';
      $data['language'] = 'en';
      $data['terms'] = '1';
      $data['profile_photo'] = $_FILES['profile_photo'];
      $coordinates = '';
      if ($random)
      {
        $data['password'] = Engine_Api::_()->user()->randomPass(10);
      }
      $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
      $user->setFromArray($data);
      $user->save();
      Engine_Api::_()->user()->setViewer($user);
      $this->_str .= '<success>User successfully registered.</success>';
  
      // Increment signup counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');
  
  
      //if ($user->verified && $user->enabled) 
      //{
        // Create activity for them
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
        // Set user as logged in if not have to verify email
        Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
      //}
  
      $email_params = array(
        'displayname' => $user->getTitle(),
        'email' => $user->email,
        'link' => 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login'),
      );
      if ($random)
      {
        $email_params['password'] = $data['password'];
      }
      switch ($settings->getSetting('user.signup.verifyemail', 0)) {
        case 0:
          // only override admin setting if random passwords are being created
          if ($random) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $user,
              'core_welcome_password',
              $email_params
            );
          }
          break;
  
        case 1:
          // send welcome email
          Engine_Api::_()->getApi('mail', 'core')->sendSystem(
            $user,
            $random ? 'core_welcome_password':'core_welcome',
            $email_params
          );
          break;
  
        case 2:
          // verify email before enabling account
          $verify_table = Engine_Api::_()->getDbtable('verify', 'user');
          $verify_row = $verify_table->createRow();
          $verify_row->user_id = $user->getIdentity();
          $verify_row->code = md5($user->email . $user->creation_date . $settings->getSetting('core.secret', 'staticSalt') . (string) rand(1000000, 9999999));
          $verify_row->date = $user->creation_date;
          $verify_row->save();
          $email_params['link'] =  'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'verify', 'email'=>$user->email, 'verify'=>$verify_row->code), 'user_signup');
          Engine_Api::_()->getApi('mail', 'core')->sendSystem(
            $user,
            $random ? 'core_verification_password' : 'core_verification',
            $email_params
          );
          break;
  
        default:
          // do nothing
      }
      
      $viewer = Engine_Api::_()->user()->getViewer();
      $params = array(
        'parent_type' => 'viewer',
        'parent_id' => $viewer->user_id
      );
      if( $data['profile_photo']['error'] == 0 ) {
        $photo_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$data['profile_photo']['name'];
        move_uploaded_file($data['profile_photo']['tmp_name'], $photo_path);
        $file = APPLICATION_PATH.'/temporary/'.$data['profile_photo']['name'];
        $path = dirname($file);
        $name = basename($file);
  
        $this->resizeImages($file);
  
        $_SESSION['TemporaryProfileImg'] = $name;
        
        // Store
        $storage = Engine_Api::_()->storage();
        $iMain = $storage->create($path.'/m_'.$name, $params);
        $iProfile = $storage->create($path.'/p_'.$name, $params);
        $iIconNormal = $storage->create($path.'/in_'.$name, $params);
        $iSquare = $storage->create($path.'/is_'.$name, $params);
  
        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');
  
        // Update row
        $viewer->photo_id = $iMain->file_id;
        $viewer->save();
  
        if ($coordinates){
          $this->resizeThumbnail($viewer, $coordinates);
        }
      }
      
      // SAVE FIRST/LAST NAME
      $config = include 'application/settings/database.php';
      $connect = mysql_connect($config['params']['host'], $config['params']['username'], $config['params']['password']);
      mysql_select_db($config['params']['dbname'], $connect);
      $field_insert_query = "
        INSERT INTO engine4_user_fields_values
          (item_id, field_id, `index`, value)
        VALUES
          (".$viewer->user_id.", '3', '', '".$first_name."'),
          (".$viewer->user_id.", '4', '', '".$last_name."')";
      mysql_query($field_insert_query);
      if ( $first_name.$last_name != '' ) $displayname = $first_name." ".$last_name;
      else $displayname = $username;
      mysql_query("UPDATE engine4_users SET displayname = '".$displayname."' WHERE user_id = ".$viewer->user_id);
      mysql_close($connect);
      
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOAD POINTS
  public function loadpointsAction() {
    extract($_POST);
    $types_array = split(';', $types);
    
    // LOAD POINTS
    //$rows = Engine_Api::_()->getDbTable('points', 'iphone')->fetchAll();
    $table = Engine_Api::_()->getDbTable('points', 'iphone');
    $rows = $table->fetchAll($table->select()->order('id DESC'));
    $this->_str .= "<points>";
    foreach ( $rows as $row ) {
      if ( array_search($row->type, $types_array) !== false || !strlen($types) ) {
        $this->_str .= "<point>";
        $this->_str .= "<id>".$row->id."</id>";
        $this->_str .= "<type_id>".$row->type."</type_id>";
        $this->_str .= "<lat>".$row->lat."</lat>";
        $this->_str .= "<lng>".$row->lng."</lng>";
        $this->_str .= "<name>".$row->title."</name>";
        $this->_str .= "<description>".$row->description."</description>";
        $this->_str .= "<is_expired>".( (time() > $row->datecreated + (90 * 24 * 60 * 60)) ? 1 : 0 )."</is_expired>";
        $time_ago = simplexml_load_string($this->view->timestamp($row->datecreated));
        $this->_str .= "<time_ago>".$time_ago[0]."</time_ago>";
        $this->_str .= "<is_newest>"."</is_newest>";
        $this->_str .= "<post_date>".$row->datecreated."</post_date>";
        
        if ( $row->image ) {
          $files = Engine_Api::_()->getItem('storage_file', $row->image);
          $files2 = Engine_Api::_()->getItem('storage_file', $row->image+1);
          $files3 = Engine_Api::_()->getItem('storage_file', $row->image+2);
          
          $this->_str .= "<photo_url>".(@$files->storage_path ? "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$files->storage_path : "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/iphone/icons/NoPhotoAvailable.jpg" )."</photo_url>";
          $this->_str .= "<thumbnail_url>".(@$files3->storage_path ? "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$files3->storage_path : "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/iphone/icons/NoPhotoAvailable_2.jpg" )."</thumbnail_url>";
          $this->_str .= "<thumbnail_detailed_url>".(@$files2->storage_path ? "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$files2->storage_path : "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/iphone/icons/NoPhotoAvailable_1.jpg" )."</thumbnail_detailed_url>";
        }
        else {
          $this->_str .= "<photo_url>http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/iphone/icons/NoPhotoAvailable.jpg</photo_url>";
          $this->_str .= "<thumbnail_url>http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/iphone/icons/NoPhotoAvailable_2.jpg</thumbnail_url>";
          $this->_str .= "<thumbnail_detailed_url>http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/public/iphone/icons/NoPhotoAvailable_1.jpg</thumbnail_detailed_url>";
        }
        $user = Engine_Api::_()->user()->getUser($row->user_id);
        $this->_str .= "<tagged_by_id>".$user->getIdentity()."</tagged_by_id>";
        $this->_str .= "<tagged_by_username>".$user->getTitle()."</tagged_by_username>";
        $this->_str .= "</point>";        
      }
    }
    $this->_str .= "</points>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // DELETE POINTS
  public function deletepointsAction() {
    extract($_POST);
    
    $table = Engine_Api::_()->getDbTable('points', 'iphone');
    $where = $table->getAdapter()->quoteInto('1');
    $table->delete($where);
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // ADD POINT
  public function addpointAction() {
    extract($_POST);
    
    // GET LOGGED IN USER
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $error = 0;
    
    if ( $type_id == 0 ) {
      $this->_str .= "<error>No type specified.</error>";
      $error = 1;
    }
    else if ( $lat == '' || $lng == '' ) {
      $this->_str .= "<error>No latitude/longitude specified.</error>";
      $error = 1;
    }
    else if ( $name == '' ) {
      $this->_str .= "<error>No title specified.</error>";
      $error = 1;
    }
    
    if ( !$error ) {
      $photo = $_FILES['point_photo']['name'];
      $photo_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;
      
      move_uploaded_file($_FILES['point_photo']['tmp_name'], $photo_path);
      $photo_id = $photo ? $this->setPhoto($photo) : '';
      // INSERT QUERY
      $table = Engine_Api::_()->getDbTable('points', 'Iphone');
      $insert_values = array(
        'type' => $type_id,
        'user_id' => $viewer->getIdentity(),
        'lat' => $lat,
        'lng' => $lng,
        'title' => $name,
        'description' => $description,
        'image' => $photo_id,
        'datecreated' => time()
      );
      $id = $table->insert($insert_values);
      $files = Engine_Api::_()->getItem('storage_file', $photo_id);
      $this->_str .= "<id>".$id."</id>";
      $this->_str .= "<photo_url>".(@$files->storage_path ? "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$files->storage_path : " ")."</photo_url>";      
      
      // SEND EMAIL MESSAGE TO THE ADMIN
      $enable_mail = 0;
      $rows = Engine_Api::_()->getDbTable('types', 'Iphone')->fetchAll("id = ".$type_id);
      foreach ( $rows as $row ) {
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($row->email) && $enable_mail) {
          $message = "A new ".ucfirst($row->title)." Sighting point has been added.\n";
    
          $message .= "\nType: ".$row->title."\n";
          $message .= "Latitude: ".$lat."\n";
          $message .= "Longitude: ".$lng."\n";
          $message .= "Title: ".$name."\n";
          $message .= "Description: ".$description."\n";
          $message .= "Image: \n";
          $message .= "Uploaded by: ".$viewer->getTitle()."\n";
    
          $message .= "\nLog in to admin panel: http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/admin";
          $from  = "From: ".$viewer->getTitle()."<".$email.">"."\r\n";
          mail($row->email, "New ".ucfirst($row->title)." Sighting", $message, $from);
        }
      }
      
      
      // SEND PUSH NOTIFICATION
      $enable_push = 0;
      $lat1 = $lat;
      $lng1 = $lng;
      $message = urlencode("New Sighting Alert: Do you want to view it now?");

      $rows = Engine_Api::_()->getDbTable('token', 'Iphone')->fetchAll();
      foreach ( $rows as $row ) {
        $types = explode(';', $row->types);
        if ( in_array($type_id, $types) && $viewer->getIdentity() != $row->user_id && $enable_push ) {
          // DISTANCE CALCULATION FORMULA
          $lat2 = $row->lat;
          $lng2 = $row->lng;
          $earth_radius = 6371;

          $dLat = deg2rad($lat2 - $lat1);
          $dLon = deg2rad($lng2 - $lng1);

          $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
          $c = 2 * asin(sqrt($a));
          $d = $earth_radius * $c;
          //$this->_str .= "<debug>D: ".$d."</debug>";
          //$this->_str .= "<debug>E: ".$row->radius."</debug>";

          if ( $row->token != '' && ($d < $row->radius || $row->radius == 'world') ) {
            $curl_url = $_SERVER['HTTP_HOST'].$this->view->baseUrl().'/push/sendnotification-prod.php?sound=default&badge=1&message='.$message.'&dev_token='.$row->token;
            //$this->_str .= "<debug>F: ".$curl_url."</debug>";
            $ch = curl_init($curl_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
          }
        }
      }
      
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOAD FRIENDS
  public function loadfriendsAction() {
    extract($_POST);
    
    $page = $page ? $page : 1;
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $select = $viewer->membership()->getMembersSelect();
    $friends = Zend_Paginator::factory($select);
    $total_count = $friends->getTotalItemCount();
    $limit = $limit ? $limit : $total_count;
    
    $friends->setCurrentPageNumber($page);
    $friends->setItemCountPerPage($limit);

    // Get stuff
    $ids = array();
    foreach( $friends as $friend )
    {
      $ids[] = $friend->user_id;
    }

    // Get the items
    $friendUsers = array();
    foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser )
    {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    
    $this->_str .= "<friends>";
    if ( $friends->count() >= $page ) {
      foreach ( $friendUsers as $friendUser ) {
        $this->_str .= "<friend>";
        $this->_str .= '<id>'.$friendUser->user_id.'</id>';
        $this->_str .= '<name>'.$friendUser->displayname.'</name>';
        $this->_str .= '<status_id>'.( $viewer->membership()->isMember($friendUser) ? $viewer->membership()->isMember($friendUser) : 0 ).'</status_id>';
        $this->_str .= '<status_description>'.( $viewer->membership()->isMember($friendUser) ? "Is friend" : "Not friend" ).'</status_description>';
        $thumbnail_detailed_url = simplexml_load_string($this->view->itemPhoto($friendUser, 'thumb.profile'));
        $this->_str .= '<photo_url>'.( (strpos($thumbnail_detailed_url['src'], 'http') !== false) ?  $thumbnail_detailed_url['src'] : "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".$thumbnail_detailed_url['src'] ).'</photo_url>';
        $thumbnail_url = simplexml_load_string($this->view->itemPhoto($friendUser, 'thumb.icon'));
        $this->_str .= '<thumbnail_url>'.( (strpos($thumbnail_url['src'], 'http') !== false) ?  $thumbnail_url['src'] : "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".$thumbnail_url['src'] ).'</thumbnail_url>';
        $this->_str .= "<status_update>".$friendUser->status."</status_update>";
        $this->_str .= "</friend>";
      }
    }
    $this->_str .= "</friends>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // ADD FRIEND
  public function addfriendAction() {
    extract($_POST);
    
    $viewer = $this->_helper->api()->user()->getViewer();
    $user = $this->_helper->api()->user()->getUser($user_id);
    
    // Get id of friend to add
    if ( null == $user_id ) {
      $this->_str .= "<error>No member specified</error>";
    }
    else if ( $viewer->isSelf($user) ) {
      $this->_str .= "<error>You cannot befriend yourself.</error>";
    }    
    else if ( $viewer->membership()->isMember($user)) {
      $this->_str .= "<error>You are already friends with this member.</error>";
    }
    else if ( $viewer->isBlocked($user)) {
      $this->_str .= "<error>Friendship request was not sent because you blocked this member.</error>";
    }
    else {
      // Process
      $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
      $db->beginTransaction();
      
      try
      {
        $user->membership()->addMember($viewer)->setUserApproved($viewer);
        if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');
          $message = "You are now following this member.";
        }
        else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
          Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
          $message = "You are now friends with this member.";
        }
        else if(!$user->membership()->isReciprocal()){
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
          $message = "Your friend request has been sent.";
        }
        else if($user->membership()->isReciprocal())
        {
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
          $message = "Your friend request has been sent.";
        }
        $db->commit();
        
        $this->_str .= "<success>".$message."</success>";
      }
      catch( Exception $e )
      {
        $db->rollBack();
        $this->_str .= "<error>".$e->getMessage()."</error>";
      }
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // SEND MESSSAGE
  public function sendmessageAction() {
    extract($_POST);
    
    // Process
    $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();
    
    try {
      $viewer = $this->_helper->api()->user()->getViewer();
      
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        array($recipient_id),
        $subject,
        $message
      );
      
      $user = $this->_helper->api()->user()->getUser($recipient_id);
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
        $user,
        $viewer,
        $conversation,
        'message_new'
      );
      
      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
      
      $db->commit();
      $this->_str .= "<success>Message sent successfully.</success>";
    }
    catch( Exception $e ) {
      $db->rollBack();
      $this->_str .= "<error>".$e->getMessage()."</error>";
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOAD MESSAGES NUMBER
  public function loadmessagesnumberAction() {
    extract($_POST);
    
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $total_count = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer)->getTotalItemCount();
    $unread_count = $this->_helper->api()->messages()->getUnreadMessageCount($viewer);
    $this->_str .= '<read>'.( $total_count - $unread_count ).'</read>';
    $this->_str .= '<unread>'.$unread_count.'</unread>';
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOAD MESSAGES
  public function loadmessagesAction() {
    extract($_POST);
    
    $page = $page ? $page : 1;
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $total_count = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer)->getTotalItemCount();
    $limit = $limit ? $limit : $total_count;
    
    $this->_str .= "<messages>";
    $conversations = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer);
    $conversations->setCurrentPageNumber($page);
    $conversations->setItemCountPerPage($limit);
    if ( $conversations->count() >= $page ) {
      foreach ( $conversations as $conversation ) {
        $message = $conversation->getInboxMessage($viewer);
        $recipient = $conversation->getRecipientInfo($viewer);
        if( $conversation->recipients > 1 ) {
          $user = $viewer;
        } else {
          foreach( $conversation->getRecipients() as $tmpUser ) {
            if( $tmpUser->getIdentity() != $viewer->getIdentity() ) {
              $user = $tmpUser;
            }
          }
        }
        if( !isset($user) || !$user ) {
          $user = $this->viewer();
        }
        
        $this->_str .= "<message>";
        $this->_str .= "<id>".$conversation->conversation_id."</id>";
        $this->_str .= "<sender_id>".$user->user_id."</sender_id>";
        $this->_str .= "<sender_name>".$user->displayname."</sender_name>";
        $sender_thumbnail = simplexml_load_string($this->view->itemPhoto($user, 'thumb.profile'));
        $this->_str .= '<sender_thumbnail>'.( (strpos($sender_thumbnail['src'], 'http') !== false) ?  $sender_thumbnail['src'] : "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".$sender_thumbnail['src'] ).'</sender_thumbnail>';
        $this->_str .= "<receive_date>".$message->date."</receive_date>";
        $this->_str .= "<is_unread>".( $recipient->inbox_read ? 0 : 1 )."</is_unread>";
        $this->_str .= "<subject>".$message->title."</subject>";
        $this->_str .= "<body>".html_entity_decode($message->body)."</body>";
        $this->_str .= "</message>";
      }      
    }
    $this->_str .= "</messages>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // READ MESSAGE
  public function readmessageAction() {
    extract($_POST);
    
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);
    $conversation->setAsRead($viewer);
    $this->_str .= '<success>Message marked read.</success>';
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // DELETE MESSAGE
  public function deletemessageAction() {
    extract($_POST);
    
    $viewer_id = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity())->getIdentity();
    
    $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();
    try {
      $recipients = Engine_Api::_()->getItem('messages_conversation', $id)->getRecipientsInfo();
      foreach ($recipients as $r) {
        if ($viewer_id == $r->user_id) {
          $r->inbox_deleted  = true;
          $r->outbox_deleted = true;
          $r->save();
        }
      }
      $db->commit();
      $this->_str .= '<success>Message deleted.</success>';
    }
    catch (Exception $e) {
      $db->rollback();
      $this->_str .= "<error>".$e->getMessage()."</error>";
    }      
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // REPLY MESSAGE
  public function replymessageAction() {
    extract($_POST);
    
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);
    $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try
    {
      $conversation->reply(
        $viewer,
        $body,
        null
      );
      
      // Send notifications
      $recipients = $conversation->getRecipients();
      foreach( $recipients as $user )
      {
        if( $user->getIdentity() == $viewer->getIdentity() )
        {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $viewer,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit();
      $this->_str .= "<success>Message successfully replied.</success>";
    }
    catch( Exception $e )
    {
      $db->rollBack();
      $this->_str .= "<error>".$e->getMessage()."</error>";
    }
        
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // SAVE SETTINGS
  public function savesettingsAction() {
    extract($_POST);
    
    $radius = $radius ? $radius : 'world';
    
    // GET LOGGED IN USER
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    
    // CHECK IF ROW EXISTS
    $table = Engine_Api::_()->getDbTable('token', 'Iphone');
    $rows = $table->fetchAll("user_id = ".$viewer->getIdentity());
    if ( count($rows) ) {
      // USER FOUND IN TOKEN TABE, UPDATE
      $insert_values = array( 'radius' => $radius );
      if ( $types ) {
        $insert_values['types'] = $types;
      }
      $where = $table->getAdapter()->quoteInto('user_id = ?', $viewer->getIdentity());
      $table->update($insert_values, $where);
      $this->_str .= "<success>Settings successfully updated.</success>";
    }
    else {
      // USER NOT FOUND IN TOKEN TABLE, INSERT
      $insert_values = array( 'radius' => $radius );
      $insert_values['user_id'] = $viewer->getIdentity();
      $insert_values['types'] = $types;
      $table->insert($insert_values);
      $this->_str .= "<success>Settings successfully added.</success>";
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOAD SETTINGS
  public function loadsettingsAction() {
    extract($_POST);
    
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $rows = Engine_Api::_()->getDbTable('token', 'Iphone')->fetchAll('user_id = '.$viewer->getIdentity());
    $type_rows = Engine_Api::_()->getDbTable('types', 'Iphone')->fetchAll();
    
    if ( count($rows) ) {
      $radius = $rows[0]->radius ? $rows[0]->radius : 'world';
      $type_array = split(';', $rows[0]->types);
    }
    else {
      $radius = 'world';
      $type_array = '';
      foreach ( $type_rows as $r ) {
        $type_array .= $r->id.';';
      }
      $type_array = split(';', $type_array);
    }
    
    $this->_str .= "<radius>".( $radius ? $radius : 'world' )."</radius>";
    $this->_str .= "<types>";
    foreach ( $type_rows as $row ) {
      $this->_str .= "<type>";
      $this->_str .= "<type_id>".$row->id."</type_id>";
      $this->_str .= "<value>".( ( in_array($row->id, $type_array) || $rows[0]->types == '' ) ? '1' : '0' )."</value>";
      $this->_str .= "</type>";
    }
    $this->_str .= "</types>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // REGISTER TOKEN
  public function registertokenAction() {
    extract($_POST);
    
    if ( strlen($token) != 64 || !ctype_alnum($token) ) {
      $this->_str .= "<error>Token incorrect.</error>";
    }
    else {
      // GET LOGGED IN USER
      $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
      
      // CHECK IF ROW EXISTS
      $table = Engine_Api::_()->getDbTable('token', 'Iphone');
      $rows = $table->fetchAll("user_id = ".$viewer->getIdentity());
      if ( count($rows) ) {
        // USER FOUND IN TOKEN TABE, UPDATE
        $insert_values = array(
          'token' => $token
        );
        $where = $table->getAdapter()->quoteInto('user_id = ?', $viewer->getIdentity());
        $table->update($insert_values, $where);
        $this->_str .= "<success>Device token successfully updated.</success>";
      }
      else {
        // USER NOT FOUND IN TOKEN TABLE, INSERT
        $insert_values = array(
          'user_id' => $viewer->getIdentity(),
          'token' => $token
        );
        $table->insert($insert_values);
        $this->_str .= "<success>Device token successfully updated.</success>";
      }
    }    
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // SAVE LOCATION
  public function savelocationAction() {
    extract($_POST);
    
    // GET LOGGED IN USER
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    
    // CHECK IF ROW EXISTS
    $table = Engine_Api::_()->getDbTable('token', 'Iphone');
    $rows = $table->fetchAll("user_id = ".$viewer->getIdentity());
    if ( count($rows) ) {
      // USER FOUND IN TOKEN TABE, UPDATE
      $insert_values = array(
        'lat' => $lat,
        'lng' => $lng,
        'update' => time()
      );
      $where = $table->getAdapter()->quoteInto('user_id = ?', $viewer->getIdentity());
      $table->update($insert_values, $where);
      $this->_str .= "<success>Location successfully updated.</success>";
    }
    else {
      // USER NOT FOUND IN TOKEN TABLE, INSERT
      $insert_values = array(
        'user_id' => $viewer->getIdentity(),
        'lat' => $lat,
        'lng' => $lng,
        'update' => time()
      );
      $table->insert($insert_values);
      $this->_str .= "<success>Location successfully updated.</success>";
    }
   
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // LOAD USERS
  public function loadusersAction() {
    extract($_POST);
    
    $page = $page ? $page : 1;
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $total_count = Engine_Api::_()->getDbTable('token', 'Iphone')->fetchAll()->count();
    $limit = $limit ? $limit : $total_count;
    
    $rows = Engine_Api::_()->getDbTable('token', 'Iphone')->fetchAll('user_id != '.$viewer->getIdentity());
    $rows = Zend_Paginator::factory($rows);
    $rows->setCurrentPageNumber($page);
    $rows->setItemCountPerPage($limit);
    
    $this->_str .= "<users>";
    foreach ( $rows as $row ) {
      if ( $row->user_id ) {
        if ( $is_online == 1 ) {
          $show = ( (time() - $row->update > 5 * 60) ? '0' : '1' );
        }
        else $show = 1;
        
        if ( $show ) {
          $user = Engine_Api::_()->user()->getUser($row->user_id);
          
          if ( $user->getIdentity() ) {
            $this->_str .= "<user>";
            $this->_str .= "<id>".$user->user_id."</id>";
            $this->_str .= "<name>".$user->displayname."</name>";
            $this->_str .= "<lat>".$row->lat."</lat>";
            $this->_str .= "<lng>".$row->lng."</lng>";
            $this->_str .= "<token>".$row->token."</token>";
            $this->_str .= "<is_online>".( (time() - $row->update > 5 * 60) ? '0' : '1' )."</is_online>";
            
            $config = include 'application/settings/database.php';
            $connect = mysql_connect($config['params']['host'], $config['params']['username'], $config['params']['password']);
            mysql_select_db($config['params']['dbname'], $connect);
            $res = mysql_query("SELECT value FROM engine4_user_fields_values WHERE item_id = ".$user->user_id." AND field_id = 5 LIMIT 1", $connect);
            $arr = mysql_fetch_assoc($res);
            switch ( $arr['value'] ) {
              case '2':
                $result = 1;
                break;
              case '3':
                $result = 2;
                break;
              default:
                $result = -1;
                break;
            }
            $this->_str .= "<gender>".$result."</gender>";
            mysql_close($connect);
            
            $thumbnail_url = simplexml_load_string($this->view->itemPhoto($user, 'thumb.icon'));
            $this->_str .= "<thumbnail_url>".( strpos($thumbnail_url['src'], 'http') !== false ? $thumbnail_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$thumbnail_url['src'] )."</thumbnail_url>";
            $thumbnail_detailed_url = simplexml_load_string($this->view->itemPhoto($user, 'thumb.profile'));
            $this->_str .= "<thumbnail_detailed_url>".( strpos($thumbnail_detailed_url['src'], 'http') !== false ? $thumbnail_detailed_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$thumbnail_detailed_url['src'] )."</thumbnail_detailed_url>";
            $this->_str .= "<status_id>".( $viewer->membership()->isMember($user) ? $viewer->membership()->isMember($user) : 0 )."</status_id>";
            $this->_str .= "<status_description>".( $viewer->membership()->isMember($user) ? "Is friend" : "Not friend" )."</status_description>";
            $this->_str .= "<status_update>".$user->status."</status_update>";
            $this->_str .= "</user>";
          }
        }
      }
    }
    $this->_str .= "</users>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // SHARE POINT
  public function sharepointAction() {
    extract($_POST);
    
    $row = Engine_Api::_()->getDbTable('points', 'iphone')->fetchRow('id = '.$id);
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    
    if ( $row ) {
      // DO VALIDATION
      $validator = new Zend_Validate_EmailAddress();
      if ( $validator->isValid($recipient_email) ) {
        // CLOSE XML TAG
        $this->_str .= "<success>You have successfully shared a point.</success>";
    
        $message = "Hello,<br /><br />";
        $message .= $viewer->getTitle()." wants to share a point with you.<br /><br />";
        $message .= $viewer->getTitle()." also says,<br /><br />";
        $message .= "<blockquote>".$body."</blockquote><br /><br />";
        $message .= "Please click the following link to view it:<br /><br />";
        $message .= "<a href='http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/iphone/pid/".$id."'>";
        $thumb_path = Engine_Api::_()->getItem('storage_file', $row->image+1);
        $message .= "<img src='http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/".@$thumb_path->storage_path."' /><br /><br />";
        $message .= "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."/iphone/pid/".$id."</a><br /><br />";
        $message .= "Best regards,<br />";
        $message .= "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."<br />";
    
        $subject = $viewer->getTitle()." Wants To Share A Point";
    
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    
        // Additional headers
        //$headers .= "From: ".$setting['setting_email_fromname']."<".$setting['setting_email_fromemail'].">"."\r\n";
        
        mail($recipient_email, $subject, $message, $headers);
      }      
      else {
        $this->_str .= "<error>The recipient email address is not valid.</error>";
      }    
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // SHARE VIDEO
  public function sharevideoAction() {
    extract($_POST);
    
    $video_code = explode('/', $video_url);
    $video_code = substr($video_code[3], 1+strpos($video_code[3], '='));
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    
    // DO VALIDATION
    $validator = new Zend_Validate_EmailAddress();
    if ( $validator->isValid($recipient_email) ) {
      // CLOSE XML TAG
      $this->_str .= "<success>You have successfully shared the video.</success>";
  
      $message = "Hello,<br /><br />";
      $message .= $viewer->getTitle()." has sent you a youtube video to watch.<br /><br />";
      $message .= $viewer->getTitle()." also says,<br /><br />";
      $message .= "<blockquote>".$body."</blockquote><br /><br />";
      $message .= "Please click the following link to view it:<br /><br />";
      $message .= "<a href='".$video_url."'>";
      $message .= $video_url."</a><br /><br />";
      $message .= "Best regards,<br />";
      $message .= "http://".$_SERVER['HTTP_HOST'].$this->view->baseUrl()."<br />";
  
      $subject = $viewer->getTitle()." Wants To Share A Youtube Video";
  
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  
      // Additional headers
      //$headers .= "From: ".$setting['setting_email_fromname']."<".$setting['setting_email_fromemail'].">"."\r\n";
  
      mail($recipient_email, $subject, $message, $headers);
    }
    else {
      $this->_str .= "<error>The recipient email address is not valid.</error>";
    }
        
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  
  // LOAD USER
  public function loaduserAction() {
    extract($_POST);
    
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $user = Engine_Api::_()->user()->getUser($user_id);
    
    if ( $user->getIdentity() ) {
      $this->_str .= "<user>";
      $this->_str .= "<id>".$user->user_id."</id>";
      $this->_str .= "<name>".$user->displayname."</name>";
      //$this->_str .= "<lat>".$user->lat."</lat>";
      //$this->_str .= "<lng>".$user->lng."</lng>";
      //$this->_str .= "<token>".$user->token."</token>";
      //$this->_str .= "<is_online>".( (time() - $user->update > 5 * 60) ? '0' : '1' )."</is_online>";
      
      $thumbnail_url = simplexml_load_string($this->view->itemPhoto($user, 'thumb.icon'));
      $this->_str .= "<thumbnail_url>".( strpos($thumbnail_url['src'], 'http') !== false ? $thumbnail_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$thumbnail_url['src'] )."</thumbnail_url>";
      $thumbnail_detailed_url = simplexml_load_string($this->view->itemPhoto($user, 'thumb.profile'));
      $this->_str .= "<thumbnail_detailed_url>".( strpos($thumbnail_detailed_url['src'], 'http') !== false ? $thumbnail_detailed_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$thumbnail_detailed_url['src'] )."</thumbnail_detailed_url>";
      $this->_str .= "<status_id>".( $viewer->membership()->isMember($user) ? $viewer->membership()->isMember($user) : 0 )."</status_id>";
      $this->_str .= "<status_description>".( $viewer->membership()->isMember($user) ? "Is friend" : "Not friend" )."</status_description>";
      $this->_str .= "<status_update>".$user->status."</status_update>";
      
      // GET PROFILE FIELD VALUES
      $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
      $gender = $fieldsByAlias['gender']->getValue($user)->value ? ( ($fieldsByAlias['gender']->getValue($user)->value == 2) ? 1 : 2 ) : -1;
      $this->_str .= "<gender>".$gender."</gender>";
      $this->_str .= "</user>";
    }
    else {
      $this->_str .= "<error>Invalid user.</error>";
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';    
  }
  
  // LOAD NEWS FEED
  public function loadactivityfeedAction() {
    extract($_POST);
    
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }
    }
    
    $limit = $limit ? $limit : 15;
    // Get config options for activity
    $config = array(
      'limit'     => $limit
    );
    
    // Get viewer and subject
    if( !empty($subject) )
    {
      $actions = Engine_Api::_()->getDbtable('actions', 'activity')->getActivityAbout($subject, $viewer, $config);
    }
    else
    {
      $actions = Engine_Api::_()->getDbtable('actions', 'activity')->getActivity($viewer, $config);
    }
    
    $this->_str .= "<actions>";
    foreach ( $actions as $action ) {
      $type = 0;
      switch ( $action->type ) {
        case 'signup':
          $type = 1;
        case 'status':
          $type = ( $type != 0 ) ? $type : 3;
        case 'post_self':
          $type = ( $type != 0 ) ? $type : ( $action->body ? 5 : 4);
        case 'post':
          $type = ( $type != 0 ) ? $type : ( $action->body ? 5 : 4);
        default:
          if ( $type != 0 && $limit > 0 ) {
            $limit--;
            $this->_str .= "<action>";
            $this->_str .= "<id>".$action->action_id."</id>";
            $this->_str .= "<type>".$type."</type>";
            $this->_str .= "<type_description>".$action->type."</type_description>";
            $user_thumbnail_url = simplexml_load_string($this->view->itemPhoto($this->view->item($action->subject_type, $action->subject_id), 'thumb.icon', $action->getSubject()->getTitle()));
            $this->_str .= "<user_thumbnail_url>".( strpos($user_thumbnail_url['src'], 'http') !== false ? $user_thumbnail_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$user_thumbnail_url['src'] )."</user_thumbnail_url>";
            $this->_str .= "<user_id>".$action->subject_id."</user_id>";
            $this->_str .= "<user_name>".$action->getSubject()->getTitle()."</user_name>";
            $this->_str .= "<time_ago>".strip_tags($this->view->timestamp($action->getTimeValue()))."</time_ago>";
            $photo_url = '';
            $photo_thumbnail_url = '';
            if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ) {
              if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ) {
                foreach( $action->getAttachments() as $attachment ) {
                  if( $attachment->meta->mode == 1 ) {
                    if( $attachment->item->getPhotoUrl() ) {
                      if ( $photo_url == '' ) {
                        $photo_url = simplexml_load_string($this->view->itemPhoto($attachment->item, 'thumb.icon', $attachment->item->getTitle()));
                        $photo_url = ( strpos($photo_url['src'], 'http') !== false ? $photo_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$photo_url['src'] );
                        $photo_thumbnail_url = simplexml_load_string($this->view->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()));
                        $photo_thumbnail_url = ( strpos($photo_thumbnail_url['src'], 'http') !== false ? $photo_thumbnail_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$photo_thumbnail_url['src'] );
                      }
                    }
                  }
                }
              }
            }
            $this->_str .= "<photo_url>".$photo_url."</photo_url>";
            $this->_str .= "<photo_thumbnail_url>".$photo_thumbnail_url."</photo_thumbnail_url>";
            $this->_str .= "<status_update>".$action->body."</status_update>";
            $this->_str .= "<friend_id></friend_id>";
            $this->_str .= "<friend_name></friend_name>";
            $this->_str .= "<comment_count>".$action->comment_count."</comment_count>";
            $this->_str .= "<like_count>".$action->like_count."</like_count>";
            
            $this->_str .= "</action>";
            
          }
          
          break;
      }
      
    }
    $this->_str .= "</actions>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  
  // LOAD COMMENT
  public function loadcommentsAction() {
    extract($_POST);
    
    // LOAD ACTIONS FIRST
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }
    }

    // Get config options for activity
    $config = array(
      'action_id' => (int) $post_id,
      'limit'     => 1,
    );
    
    // Get viewer and subject
    if( !empty($subject) )
    {
      $actions = Engine_Api::_()->getDbtable('actions', 'activity')->getActivityAbout($subject, $viewer, $config);
      $this->view->subjectGuid = $subject->getGuid(false);
    }
    else
    {
      $actions = Engine_Api::_()->getDbtable('actions', 'activity')->getActivity($viewer, $config);
      $this->view->subjectGuid = null;
    }
    
    $this->_str .= "<comments>";
    foreach ( $actions as $action ) {
      if ( $action->comments()->getCommentCount() > 0 ) {
        foreach ( $action->getComments(true) as $comment ) {
          $this->_str .= "<comment>";
          $this->_str .= "<id>".$comment->comment_id."</id>";
          $this->_str .= "<body>".$comment->body."</body>";
          $this->_str .= "<time_ago>".strip_tags($this->view->timestamp($comment->creation_date))."</time_ago>";
          $this->_str .= "<user_id>".$comment->poster_id."</user_id>";
          $this->_str .= "<user_name>".$this->view->item($comment->poster_type, $comment->poster_id)->getTitle()."</user_name>";
          $user_thumbnail_url = simplexml_load_string($this->view->itemPhoto($this->view->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle()));
          $this->_str .= "<user_thumbnail_url>".( strpos($user_thumbnail_url['src'], 'http') !== false ? $user_thumbnail_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$user_thumbnail_url['src'] )."</user_thumbnail_url>";
          
          //$this->_str .= "<action_id>".$comment->resource_id."</action_id>";
          //$this->_str .= "<poster_type>".$comment->poster_type."</poster_type>";
          //$this->_str .= "<creation_date>".$comment->creation_date."</creation_date>";
          $this->_str .= "</comment>";
        }
      }      
    }
    $this->_str .= "</comments>";
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  
  // COMMENT
  public function commentAction() {
    extract($_POST);
    
    // Not post
    if( !$this->getRequest()->isPost() )
    {
      $this->_str .= "<error>Not a post.</error>";
    }
    else {
      // Start transaction
      $db = $this->_helper->api()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();
  
      try
      {
        $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
        $action = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($post_id);
        $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
  
        // Add the comment
        $action->comments()->addComment($viewer, $comment);
  
        // Notifications
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
  
        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() )
        {
          $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
            'label' => 'post'
          ));
        }
        
        // Add a notification for all users that commented or like except the viewer and poster
        // @todo we should probably limit this
        foreach( $action->comments()->getAllCommentsUsers() as $notifyUser )
        {
          if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
          {
            $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
              'label' => 'post'
            ));
          }
        }
        
        // Add a notification for all users that commented or like except the viewer and poster
        // @todo we should probably limit this
        foreach( $action->likes()->getAllLikesUsers() as $notifyUser )
        {
          if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
          {
            $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
              'label' => 'post'
            ));
          }
        }
        
        $db->commit();
        $this->_str .= "<success>Comment posted.</success>";
      }
  
      catch( Exception $e )
      {
        $db->rollBack();
        //throw $e;
        $this->_str .= "<error>".$e->getMessage()."</error>";
      }
      
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  // FEED LIKE
  public function likeAction() {
    extract($_POST);
    
	// Collect params
	$viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());

	// Start transaction
	$db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
	$db->beginTransaction();

	try
	{
		$action = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($post_id);
		$action->likes()->addLike($viewer);
		  
		// Add notification for owner of activity (if user and not viewer)
		if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() )
		{
		  $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
		  
		  Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
			'label' => 'post'
		  ));
		}

		$db->commit();
		$this->_str .= "<success>You now like this action.</success>";
	}

	catch( Exception $e )
	{
		$db->rollBack();
		//throw $e;
		$this->_str .= "<error>".$e->getMessage()."</error>";
	}      

    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  
  // POST
  public function postAction() {
    extract($_POST);
    
    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $subject = null;
    $subject_guid = $this->_getParam('subject', null);
    if( $subject_guid )
    {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if( null === $subject )
    {
      $subject = $viewer;
    }

    // Check auth
    if( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
      $this->_str .= "<error>Not allowed to post.</error>";
    }
    else {
      $body = html_entity_decode($status, ENT_QUOTES, 'UTF-8');
      $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
      
      // Check one more thing
      if( $status === '' && $_FILES['photo']['name'] === '' )
      {
        $this->_str .= "<error>Invalid data.</error>";
      }
      else {
        if( $_FILES['photo']['name'] != '' ) {
          
          // Get album
          $table = Engine_Api::_()->getDbtable('albums', 'album');
          $db = $table->getAdapter();
          $db->beginTransaction();
  
          try
          {
            $type = 'wall';
            $album = $table->getSpecialAlbum($viewer, $type);
            $photo = Engine_Api::_()->album()->createPhoto(
              array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
              ),
              $_FILES['photo']
            );
            
            if($type === 'message'){
              $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
            }
            $photo->collection_id = $album->album_id;
            $photo->save();
  
            if( !$album->photo_id )
            {
              $album->photo_id = $photo->getIdentity();
              $album->save();
            }
  
            if ($type != 'message'){
              // Authorizations
              $auth = Engine_Api::_()->authorization()->context;
              $auth->setAllowed($photo, 'everyone', 'view',    true);
              $auth->setAllowed($photo, 'everyone', 'comment', true);
              $auth->setAllowed($album, 'everyone', 'view',    true);
              $auth->setAllowed($album, 'everyone', 'comment', true);
            }
            
            $db->commit();
  
            $this->view->photo_id = $photo->photo_id;
            $this->view->album_id = $album->album_id;
            $this->view->src = $photo->getPhotoUrl();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
          }
  
          catch( Exception $e )
          {
            $db->rollBack();
            //throw $e;
            $this->_str .= "<error>".$e->getMessage()."</error>";
          }
        }
              
        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();
  
        try {
          //Try attachment getting stuff
          $attachment = null;
          if ( isset($photo) ) {
            $attachmentData['type'] = 'photo';
            $attachmentData['photo_id'] = $photo->photo_id;
          }
          if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
            $type = $attachmentData['type'];
            $config = null;
            foreach( Zend_Registry::get('Engine_Manifest') as $data )
            {
              if( !empty($data['composer'][$type]) )
              {
                $config = $data['composer'][$type];
              }
            }
            if( $config ) {
              $plugin = Engine_Api::_()->loadClass($config['plugin']);
              $method = 'onAttach'.ucfirst($type);
              $attachment = $plugin->$method($attachmentData);
              //$this->_str .= "<success>".$type."</success>";
            }
          }


          // Get body
          $body = $status;
          $body = preg_replace('/<br[^<>]*>/', "\n", $body);

          // Is double encoded because of design mode
          //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
          //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
          //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
          
          // Special case: status
          if( !$attachment && $viewer->isSelf($subject) )
          //if( $viewer->isSelf($subject) )
          {
            if( $body != '' )
            {
              $viewer->status = $body;
              $viewer->status_date = date('Y-m-d H:i:s');
              $viewer->save();

              $viewer->status()->setStatus($body);
            }

            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
            //$action = $this->_helper->api()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
          }

          // General post
          else
          {
            $type = 'post';
            if( $viewer->isSelf($subject) )
            {
                  $type = 'post_self';
            }
            
            // Add notification for <del>owner</del> user
            $subjectOwner = $subject->getOwner();
            //if( !$viewer->isSelf($subjectOwner) )
            if( !$viewer->isSelf($subject) && $subject instanceof User_Model_User )
            {
              $notificationType = 'post_'.$subject->getType();
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                'url1' => $subject->getHref(),
              ));
            }

            if( !$viewer->isSelf($subject) )
            {
              //if( $subject instanceof User_Model_User )
            }

            // Add activity
            //$action = $this->_helper->api()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'post_self', $body);
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'post', $body);
            
            // Try to attach if necessary
            if( $action && $attachment )
            {
              $this->_helper->api()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
            }
          }

          $db->commit();
          // If we're here, we're done
          $this->_str .= "<success>Success.</success>";
        } // end "try"
        catch( Exception $e )
        {
          $db->rollBack();
          //throw $e; // This should be caught by error handler
          $this->_str .= "<error>".$e->getMessage()."</error>";
        }
      }		
    }
  

    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  
  // LOAD USER FROM LOCATION
  public function loadusersfromlocationAction ()
  {
    extract($_POST);
    
    $page = $page ? $page : 1;
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    $total_count = Engine_Api::_()->getDbTable('checkin', 'Iphone')->fetchAll()->count();
    //$this->_str .= "<debug>".$viewer->getIdentity()."</debug>";
    $limit = $limit ? $limit : $total_count;
    
    $table = Engine_Api::_()->getDbTable('checkin', 'Iphone');
    $select = $table->select();

    if($location_id == '' && $latitude == '' && $longitude == '' ){
        $this->_str .= "<users></users>";
    }
    else{
      if ( $location_id != '' || ( $latitude != '' && $longitude != '' ) ) {
        if ( $location_id != '' ) {
         // $wher = $table->select()->where("location_id = ?", $location_id);
          $select->where("location_id = '".$location_id."'");
        }
        else if ( $latitude != '' && $longitude != '' ) {
         // $wher = $table->select()->where("latitude = '".$latitude."' AND longitude = '".$longitude."'");
          $select->where("latitude = '".$latitude."' AND longitude = '".$longitude."'");
        }
        else if ( $location_id != '' && $latitude != '' && $longitude != '' ) {
         // $wher = $table->select()->where("latitude = '".$latitude."' AND longitude = '".$longitude."'");
          $select->where("(latitude = '".$latitude."' AND longitude = '".$longitude."') OR location_id = '".$location_id."'");
        }

        // $select = $table->select()->where("user_id != ".$viewer->getIdentity());
        $rows = Zend_Paginator::factory($table->fetchAll($select));
        $rows->setCurrentPageNumber($page);
        $rows->setItemCountPerPage($limit);

        $this->_str .= "<users>";
        foreach ( $rows as $row ) {
          if ( $row->user_id ) {
            $user = Engine_Api::_()->user()->getUser($row->user_id);

            if ( $user->getIdentity() ) {
              $this->_str .= "<user>";
              $this->_str .= "<id>".$user->user_id."</id>";
              $this->_str .= "<name>".$user->displayname."</name>";
              $this->_str .= "<lat>".$row->latitude."</lat>";
              $this->_str .= "<lng>".$row->longitude."</lng>";
              $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
              $sex = $fieldsByAlias['gender']->getValue($user)->value - 1;
              $this->_str .= "<gender>".$sex."</gender>";
              $thumbnail_url = simplexml_load_string($this->view->itemPhoto($user, 'thumb.icon'));
              $this->_str .= "<thumbnail_url>".( strpos($thumbnail_url['src'], 'http') !== false ? $thumbnail_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$thumbnail_url['src'] )."</thumbnail_url>";
              $thumbnail_detailed_url = simplexml_load_string($this->view->itemPhoto($user, 'thumb.profile'));
              $this->_str .= "<thumbnail_detailed_url>".( strpos($thumbnail_detailed_url['src'], 'http') !== false ? $thumbnail_detailed_url['src'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.$thumbnail_detailed_url['src'] )."</thumbnail_detailed_url>";
              $this->_str .= "<status_id>".( $viewer->membership()->isMember($user) ? $viewer->membership()->isMember($user) : 0 )."</status_id>";
              $this->_str .= "<status_description>".( $viewer->membership()->isMember($user) ? "Is friend" : "Not friend" )."</status_description>";
              $this->_str .= "<status_update>".$user->status."</status_update>";
              $this->_str .= "<checked_in_date>".$row->checkin_date."</checked_in_date>";
              $checked_in_date = simplexml_load_string($this->view->timestamp($row->checkin_date));
              $this->_str .= "<checked_id_display_date>".$checked_in_date[0]."</checked_id_display_date>";
              $this->_str .= "</user>";
            }
          }
        }
        $this->_str .= "</users>";
      }
    }
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  //
  // CHECKIN
  public function checkinAction ()
  {
    extract($_POST);
    
    // GET LOGGED IN USER
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
    
    if ( $location_id == '' && $latitude == '' && $longitude == '' ) {
      $this->_str .= "<error>Invalid parameters.</error>";
    }
    else if ( $location_id == '' && ( $latitude == '' || $longitude == '' ) ) {
      $this->_str .= "<error>Invalid parameters.</error>";
    }
    else {
      // CHECK IF ROW EXISTS
      $table = Engine_Api::_()->getDbTable('checkin', 'Iphone');
      $rows = $table->fetchAll("user_id = ".$viewer->getIdentity());
      if ( count($rows) ) {
        // USER FOUND IN TOKEN TABE, UPDATE
        $insert_values = array(
          'latitude' => $latitude,
          'longitude' => $longitude,
          'location_id' => $location_id,
          'checkin_date' => time()
        );
        $where = $table->getAdapter()->quoteInto('user_id = ?', $viewer->getIdentity());
        $table->update($insert_values, $where);
      }
      else {
        // USER NOT FOUND IN TOKEN TABLE, INSERT
        $insert_values = array(
          'user_id' => $viewer->getIdentity(),
          'latitude' => $latitude,
          'longitude' => $longitude,
          'location_id' => $location_id,
          'checkin_date' => time()
        );
        $table->insert($insert_values);
      }
      $this->_str .= "<success>User successfully checked-in.</success>";
    }
    
    $this->_str = str_replace('&amp;', '&', str_replace('<br>', '<br />', $this->_str));
    $this->_str = str_replace('&', '&amp;', $this->_str);
    echo $this->_str .= '</response>';
  }
  
  
  // EXTRA FUNCTIONS
  // SAVE POINT PHOTO - ADDPOINT
  public function setPhoto($photo) {     
    if($photo){
      $file =APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.$photo;
    }
    else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
    $viewer = Engine_Api::_()->user()->getUser($this->_authResult->getIdentity());
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
  
  // RESIZE IMAGES - REGISTER
  public function resizeImages($file)
  {
    $name = basename($file);
    $path = dirname($file);

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (icon.normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(48, 120)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon.square)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();
   }
   
  // RESIZE THUMBNAIL - REGISTER
  public function resizeThumbnail($user, $coordinates)
  {
     $storage = Engine_Api::_()->storage();

     $iProfile = $storage->get($user->photo_id, 'thumb.profile');
     $iSquare = $storage->get($user->photo_id, 'thumb.icon');

     // Read into tmp file
     $pName = $iProfile->getStorageService()->temporary($iProfile);
     $iName = dirname($pName) . '/nis_' . basename($pName);

     list($x, $y, $w, $h) = explode(':', $coordinates);

     $image = Engine_Image::factory();
     $image->open($pName)
       ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
       ->write($iName)
       ->destroy();

     $iSquare->store($iName);
  }
  
}
