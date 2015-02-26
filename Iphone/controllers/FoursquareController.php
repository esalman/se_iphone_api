<?php
class Iphone_FoursquareController extends Core_Controller_Action_User
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check request
    if($this->getRequest()->isPost())
    {
        if(isset($_POST['enable']))
        {
            Engine_Api::_()->iphone()->enableFoursquare($viewer->getIdentity());
        }
        elseif(isset($_POST['latlng']))
        {
            $llData = Engine_Api::_()->iphone()->getLatLng($_POST['address']);
            //$llData['radius'] = $_POST['radius'];

            $table = Engine_Api::_()->getDbTable('token', 'iphone');
            $select = $table->select()
                            ->where('user_id = ?', $viewer->getIdentity());
            $isUserExist = count($table->fetchRow($select));

            if($isUserExist)
            {
                $table->update($llData, 'user_id = '.$viewer->getIdentity());
            }else{
                $llData['user_id'] = $viewer->getIdentity();
                $llData['update'] = time();
                $table->insert($llData);
            }                        
        }      
    }

    $table = Engine_Api::_()->getDbTable('foursquare', 'iphone');
    $select = $table->select()
                    ->where('user_id = ?', $viewer->getIdentity());
    $result = $table->fetchRow($select);

    if($result->enable)
    {
        $errors = '';
        $tokenTable = Engine_Api::_()->getDbTable('token', 'iphone');
        $select = $tokenTable->select()
                             ->where('user_id = ?', $viewer->getIdentity());
        $userGeoInfo = $tokenTable->fetchRow($select);

        if(empty($userGeoInfo->lat))
        {
           $errors .= Zend_Registry::get('Zend_Translate')->_('You have no latitude specified! !!<br />');
        }
        if(empty($userGeoInfo->lng))
        {
           $errors .= Zend_Registry::get('Zend_Translate')->_('You have no longitude specified! !!<br />');
        }
//        if(empty($userGeoInfo->radius))
//        {
//           $errors .= Zend_Registry::get('Zend_Translate')->_('You have no radius specified! !!<br />');
//        }

	if(empty($errors) && $this->_getParam('update') != 'true')
        {
            $this->view->lat = $userGeoInfo->lat;
            $this->view->lng = $userGeoInfo->lng;
            $this->view->radius = $userGeoInfo->radius;
            $this->view->access_token = $result->access_token;
        }else{
            $this->view->errors = $errors;
            $this->view->getLatLng = true;
        }
    }
    else
    {
        if(!empty($result->foursquare_id))
        {
           // Show enable form
           $this->view->setEnable = true;
        }
        else
        {
           // Redirect to foursquare authentication
           return $this->_helper->redirector->gotoRoute(array('action' => 'auth'), '');
        }
    }
  }

  public function authAction()
  {
	ob_start();
	$code = $this->_getParam('code');
	require_once 'application/libraries/Foursquareapi/EpiCurl.php';
	require_once 'application/libraries/Foursquareapi/EpiFoursquare.php';

	$viewer = Engine_Api::_()->user()->getViewer();
	
	// Get Client_id and Client_secret code
	$clientId = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientid', 'DYFMCNMP3Y5GXHWH33TYN03RUW0BX2CO00G3WKV5TEYXVOZ3');
	$clientSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientsecret', 'SPD30HRILMYRE1CRTUJOIOMGRVOJP0JU3SJCQMW2PXVATTMS');
		  
	//$clientId = $this->client_id;//'DYFMCNMP3Y5GXHWH33TYN03RUW0BX2CO00G3WKV5TEYXVOZ3';
	//$clientSecret = $this->client_secret;//'SPD30HRILMYRE1CRTUJOIOMGRVOJP0JU3SJCQMW2PXVATTMS';
	//$code = 'BFVH1JK5404ZUCI4GUTHGPWO3BUIUTEG3V3TKQ0IHVRVGVHS';
	//$accessToken = 'DT32251AY1ED34V5ADCTNURTGSNHWXCNTOMTQM5ANJLBLO2O';
	$redirectUri = 'http://www.socialxperience.com/index.php/iphone/foursquare/auth';
	//$userId = '8199986';
	//$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
	$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
	
	if(empty($code))
	{
		$authorizeUrl = $fsObjUnAuth->getAuthorizeUrl($redirectUri);
		header("Location: $authorizeUrl");
	}
	elseif(!empty($code))
	{
		try{
			$token = $fsObjUnAuth->getAccessToken($code, $redirectUri);
			$fsObjUnAuth->setAccessToken($token->access_token);
		}
		catch(EpiFoursquareException $e){
			echo $e->getMessage();
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	$self = $fsObjUnAuth->get("/users/self");
	if(!empty($self->response->user->id) AND !empty($token->access_token)){
		Engine_Api::_()->iphone()->foursquareAuthentication($viewer->getIdentity(), $self->response->user->id, $token->access_token);
		header("Location: index");
	}		  
  }

  public function getVenueInfoAction()
  {
    ob_start();
    require_once 'application/libraries/Foursquareapi/EpiCurl.php';
    require_once 'application/libraries/Foursquareapi/EpiFoursquare.php';

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get Client_id and Client_secret code
    $clientId = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientid', 'DYFMCNMP3Y5GXHWH33TYN03RUW0BX2CO00G3WKV5TEYXVOZ3');
    $clientSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientsecret', 'SPD30HRILMYRE1CRTUJOIOMGRVOJP0JU3SJCQMW2PXVATTMS');

    $fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
    $venue = $fsObjUnAuth->get('/venues/'.$_GET['id']);
    echo json_encode($venue->response);
    exit();
  }

  public function checkinAction()
  {
    $venue_id = $this->_getParam('id');
    if(!empty($venue_id))
    {
        ob_start();
        require_once 'application/libraries/Foursquareapi/EpiCurl.php';
        require_once 'application/libraries/Foursquareapi/EpiFoursquare.php';

        $viewer = Engine_Api::_()->user()->getViewer();

        // Get Client_id and Client_secret code
        $clientId = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientid', 'DYFMCNMP3Y5GXHWH33TYN03RUW0BX2CO00G3WKV5TEYXVOZ3');
        $clientSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientsecret', 'SPD30HRILMYRE1CRTUJOIOMGRVOJP0JU3SJCQMW2PXVATTMS');

        $table = Engine_Api::_()->getDbTable('foursquare', 'iphone');
        $select = $table->select()
                        ->where('user_id = ?', $viewer->getIdentity());
        $access_token = $table->fetchRow($select)->access_token;

        $fsObjAuth = new EpiFoursquare($clientId, $clientSecret, $access_token);
        $checkin = $fsObjAuth->post('/checkins/add', array('venueId' => $venue_id, 'broadcast' => 'public'));
        echo json_encode($checkin->notifications);
        exit();
    }   
  }
}
