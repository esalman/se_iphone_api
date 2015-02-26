<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>API Test</title>
<!--<script src="include/js/mootools12.js"></script>-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.3.0/mootools-yui-compressed.js"></script>
<style>
	*{margin:2px; padding:2px;}
	body{font-family:Arial, Helvetica, sans-serif; font-size:82%;}
	form{display:none;}
	
	#form_box {
	background: #f8f8f8;
	border: 1px solid #d6d6d6;
	border-left-color: #e4e4e4;
	border-top-color: #e4e4e4;
	font-size: 11px;
	font-weight: bold;
	padding: 0.5em;
	margin-top: 10px;
	margin-bottom: 2px;
	} 
 
	#log {
	float:right;
	padding: 0.5em;
	margin-left: 10px;
	margin-right:10px;	
	border: 1px solid #d6d6d6;
	border-left-color: #e4e4e4;
	border-top-color: #e4e4e4;
	margin-top: 10px;
	
	}
	#log h3{	
	border: 1px solid #666666;
	border-left-color: #e4e4e4;
	border-top-color: #e4e4e4;
	margin-top: 5px;
	margin-bottom:10px;
	text-align:center;	
	}

 
	#log_res {	
	height:500px;
	overflow:auto;
	font-family:monospace;
    font-size:12px;
	}
 
	#log_res.ajax-loading {
	padding: 20px 0;
	background: url(images/spinner.gif) no-repeat center;
	}
	.formheader{
	width:50%;
	text-align:center;
	font-size:18px;
	border:2px #666666 solid;
	margin:auto;
	background-color:#f8f8f8;
	}
</style>
</head>

<body>
<div>
<div class="formheader">iPhone API Test Tool</div>
  <div style="width:300px; margin:auto;">
   <div id="form_box">
    <select id="formfield" name="formfield">
    	<option value="" selected="selected">-Select Your Form-</option>
			<option value="login">login</option>
			<option value="logout">logout</option>
			<option value="register">register</option>
			<option value="loadpoints">loadpoints</option>
			<option value="addpoint">addpoint</option>
			<option value="loadfriends">loadfriends</option>
			<option value="addfriend">addfriend</option>   
			<option value="sendmessage">sendmessage</option>
			<option value="loadmessagesnumber">loadmessagesnumber</option>
			<option value="loadmessages">loadmessages</option>
			<option value="readmessage">readmessage</option>
			<option value="deletemessage">deletemessage</option>
			<option value="replymessage">replymessage</option>
			<option value="savesettings">savesettings</option>
			<option value="loadsettings">loadsettings</option>
			<option value="registertoken">registertoken</option>
			<option value="savelocation">savelocation</option>
			<option value="loadusers">loadusers</option>   
			<option value="sharepoint">sharepoint</option>
			<option value="sharevideo">sharevideo</option>
			<option value="loaduser">loaduser</option>
    </select>
    <br /><br />
     
    </div>
    
    <div style="margin-left:-5px;">
          <!-- login -->
          <form id="api_login" action="iphone/api/login" method="post">
            <div id="form_box">
              <h2>login</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <input type="submit" value="login" />
            </div>
          </form>
          
          <!-- logout -->
          <form id="api_logout"   action="iphone/api/logout" method="post">
            <div id="form_box">
              <h2>logout</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <input type="submit" value="logout" />
            </div>  
          </form>
          
          <!-- register -->
          <form id="api_register" action="iphone/api/register" method="post" enctype="multipart/form-data">
            <div id="form_box">
              <h2>register</h2>
              <label>email:</label><br />
              <input name='email' /><br />
              <label>password:</label><br />
              <input name='password' type='password' /><br />
              <label>first_name:</label><br />
              <input name='first_name' /><br />
              <label>last_name:</label><br />
              <input name='last_name' /><br />
              <label>username:</label><br />
              <input name='username' /><br />
              <label>profile_photo:</label><br />
              <input type="file" name="profile_photo" /><br />
              <input type="submit" value="register" />
            </div>
          </form>
          
          <!-- loadpoints -->
          <form id="api_loadpoints" action="iphone/api/loadpoints" method="post">
            <div id="form_box">
              <h2>loadpoints</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>types:</label><br/>
              <input name="types" type="text" /> optional<br />
              <label>is_show_expired:</label><br/>
              <input name="is_show_expired" type="text" /> 1/0<br />
              <input type="submit" value="loadpoints" />
              <a href="iphone/api/deletepoints" style="float:right;">Delete points</a>
            </div>
          </form>

          <!-- addpoint -->
          <form id="api_addpoint" action="iphone/api/addpoint" method="post" enctype="multipart/form-data">
            <div id="form_box">
              <h2>addpoint</h2>
              <label>email:</label><br />
              <input name="email"  type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>lat:</label><br />
              <input name="lat" type="text" /><br />
              <label>lng:</label><br />
              <input name="lng" type="text" /><br />
              <label>name:</label><br />
              <input name="name" type="text" /><br />
              <label>description:</label><br />
              <input name="description" type="text" /><br />
              <label>type_id:</label><br />
              <select name="type_id">
			<option value="0" selected="selected">choose..</option>>
			<option value="1">Automotive - Consumers Win! Accepted</option>
			<option value="2">Automotive - Non- Member Automotive</option>
			<option value="11">Restaurants - Consumers Win! Accepted</option>
			<option value="12">Restaurants - Non- Member Restaurant</option>
			<option value="21">Entertainment - Consumers Win! Accepted</option>
			<option value="22">Entertainment - Non- Member Entertainment</option>
			<option value="31">Hair Salons & Day Spas</option>
			<option value="32">Health & Wellness</option>
			<option value="33">Professionals</option>
			<option value="34">Trades & Craftsmen</option>
			<option value="35">Retail Stores</option>
		</select><br />
              <label>point_photo:</label><br />
              <input type="file" name="point_photo" /><br />
              <input type="submit" value="addpoint" />
            </div>
          </form>
        
          <!-- loadfriends -->
          <form id="api_loadfriends" action="iphone/api/loadfriends" method="post">
            <div id="form_box">
              <h2>loadfriends</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>page:</label><br />
              <input name="page" type="text" /><br />
              <label>limit:</label><br />
              <input name="limit" /><br />
              <input type="submit" value="loadfriends" />
            </div>
          </form>

          <!-- addfriend -->
          <form id="api_addfriend" action="iphone/api/addfriend" method="post">
            <div id="form_box">
              <h2>addfriend</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>user_id:</label><br />
              <input name="user_id" type="text" /><br />
              <input type="submit" value="addfriend" />
            </div>
          </form>

          <!-- sendmessage -->
          <form id="api_sendmessage" action="iphone/api/sendmessage" method="post">
            <div id="form_box">
              <h2>sendmessage</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>recipient_id:</label><br />
              <input name="recipient_id" type="text" /><br />
              <label>subject:</label><br />
              <input name="subject" type="text" /><br />
              <label>message:</label><br />
              <input name="message" type="text" /><br />
              <input type="submit" value="sendmessage" />
            </div>
          </form>

          <!-- loadmessagesnumber -->
          <form id="api_loadmessagesnumber"  action="iphone/api/loadmessagesnumber" method="post">
            <div id="form_box">
              <h2>loadmessagesnumber</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <input type="submit" value="loadmessagesnumber" />
            </div>
          </form>
          
          <!-- loadmessages -->
          <form  id="api_loadmessages"  action="iphone/api/loadmessages" method="post">
            <div id="form_box">
              <h2>loadmessages</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>page:</label><br />
              <input name="page" type="text" /> optional<br />
              <label>limit:</label><br />
              <input name="limit" type="text" /> optional<br />
              <input type="submit" value="loadmessages" />
            </div>
          </form>
          
          <!-- readmessage -->
          <form  id="api_readmessage" action="iphone/api/readmessage" method="post">
            <div id="form_box">
              <h2>readmessage</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>id:</label><br />
              <input name="id" type="text" /><br />
              <input type="submit" value="readmessage" />
            </div>
          </form>
          
          <!-- deletemessage -->
          <form id="api_deletemessage" action="iphone/api/deletemessage" method="post">
            <div id="form_box">
              <h2>deletemessage</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>id:</label><br />
              <input name="id" type="text" /><br />
              <input type="submit" value="deletemessage" />
            </div>
          </form>
          
          <!-- replymessage -->
          <form  id="api_replymessage" action="iphone/api/replymessage" method="post">
            <div id="form_box">
              <h2>replymessage</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>id:</label><br />
              <input name="id" type="text" /><br />
              <label>body:</label><br />
              <input name="body" type="text" /><br />
              <input type="submit" value="replymessage" />
            </div>
          </form>
          
          <!-- savesettings -->
          <form id="api_savesettings" action="iphone/api/savesettings" method="post" >
            <div id="form_box">
              <h2>savesettings</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>types:</label><br />
              <input name="types" type="text" /> separate by ;<br />
              <label>radius:</label><br />
              <input name="radius" /><br />
              <input type="submit" value="savesettings" />
            </div>
          </form>
          
          <!-- loadsettings -->
          <form id="api_loadsettings" action="iphone/api/loadsettings" method="post">
            <div id="form_box">
              <h2>loadsettings</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <input type="submit" value="loadsettings" />
              <a href="api.php?t=delete&f=settings" style="float:right;">Delete settings</a>
            </div>
          </form>
          
          <!-- registertoken -->
          <form  id="api_registertoken" action="iphone/api/registertoken" method="post">
            <div id="form_box">
              <h2>registertoken</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>token:</label><br />
              <input name="token" type="text" /><br />
              <input type="submit" value="registertoken" />
            </div>
          </form>
          
          <!-- savelocation -->
          <form  id="api_savelocation" action="iphone/api/savelocation" method="post" >
            <div id="form_box">
              <h2>savelocation</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>lat:</label><br />
              <input name="lat" type="text" /><br />
              <label>lng:</label><br />
              <input name="lng" type="text" /><br />
              <input type="submit" value="savelocation" />
            </div>
          </form>
          
          <!-- loadusers -->
          <form id="api_loadusers" action="iphone/api/loadusers" method="post">
            <div id="form_box">
              <h2>loadusers</h2>
              <label>email:</label><br />
              <input name="email" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>is_online:</label><br />
              <input name="is_online" /><br />
              <label>page:</label><br />
              <input name="page" /><br />
              <label>limit:</label><br />
              <input name="limit" /><br />
              <input type="submit" value="loadusers" />
            </div>
          </form>
            
    
          <!-- sharepoint -->
          <form  id="api_sharepoint"  action="iphone/api/sharepoint" method="post">
            <div id="form_box">
              <h2>sharepoint</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>recipient_email:</label><br />
              <input name="recipient_email" type="text" /><br />
              <label>id:</label><br />
              <input name="id" type="text" /><br />
              <label>body:</label><br />
              <input name="body" type="text" /><br />
              <input type="submit" value="sharepoint" />
            </div>
          </form>
          
          <!-- sharevideo -->
          <form id="api_sharevideo" action="iphone/api/sharevideo" method="post">
            <div id="form_box">
              <h2>sharevideo</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>recipient_email:</label><br />
              <input name="recipient_email" type="text" /><br />
              <label>video_url:</label><br />
              <input name="video_url" type="text" /><br />
              <label>body:</label><br />
              <input name="body" type="text" /><br />
              <input type="submit" value="sharevideo" />
            </div>
          </form>
          
          <!-- loaduser -->
          <form id="api_loaduser" action="iphone/api/loaduser" method="post">
            <div id="form_box">
              <h2>loaduser</h2>
              <label>email:</label><br />
              <input name="email" type="text" /><br />
              <label>password:</label><br />
              <input name="password" type="password" /><br />
              <label>user_id:</label><br />
              <input name="user_id" type="text" /><br />
              <input type="submit" value="loaduser" />
            </div>
          </form>
          
	    </div>
    </div>
  
    <!--<div id="log" style="width:650px;">
      <h3>API Response</h3>
      <div id="log_res"></div>
    </div>-->
    <span class="clr"></span>
    <a href="test_activity_iphone.php">TEST_ACTIVITY_IPHONE</a>
  </div>




<script type="text/javascript">
window.addEvent('domready', function() {

	$$('form[id^=api_]').setStyle('display', 'none');
	if ( $('formfield').get('value') ) {
		$('api_'+$('formfield').get('value')).setStyle('display', 'block');
	}

	$('formfield').addEvent('change', function () {
		$(document.body).getElements('form[id^=api_]').setStyle('display', 'none');
		$('api_'+this.value).setStyle('display', 'block');
	});

});
</script>
</body>
</html>