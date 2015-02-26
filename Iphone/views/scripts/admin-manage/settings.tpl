<!--SUB NAVIGATION-->
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->navigation)->render();
		?>
	</div>
<?php endif; ?>

<h3>Google Maps API key</h3>
<form class="settings" action="" method="POST">
  <div class="form-wrapper">
	<div class="form-label">Google Maps API Key:</div>
	<div class="form-element"><input type="text" name="gmap_api_key"
	  value="<?php echo Engine_Api::_()->getApi('settings', 'core')->iphone['gmap_api_key']; ?>" style="width:300px;" /></div>
  </div>
  <button type='submit' class='submit'>Save</button>
</form>

<br /><br />

<h3>iPhone Default Location</h3>
<form class="settings" action="" method="POST">
	<div class="form-wrapper">
		<div class="form-label">Default latitude and longitude</div>
		<div class="form-element"><input type="text" name="latlng"
			value="<?php echo Engine_Api::_()->getApi('settings', 'core')->iphone['defaultloc']; ?>" style="width:300px;" />
			<br />Input latitude and longitide separated by comma.
		</div>
	</div>
	<div class="form-wrapper">
		<div class="form-label">Default radius:</div>
		<div class="form-element"><input type="text" name="radius"
			value="<?php echo Engine_Api::_()->getApi('settings', 'core')->iphone['defaultrad']; ?>" style="width:300px;" /></div>
	</div>
	<button type='submit' class='submit'>Save</button>
</form>

<br /><br />

<h3>Foursquare Settings</h3>

<form class="settings" action="" method="POST">
	<div class="form-wrapper">
		<div class="form-label">FourSquare Client ID</div>
		<div class="form-element">
                    <input type="text" name="client_id" id="client_id" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientid'); ?>" style="width:300px" />
		</div>
	</div>
	<div class="form-wrapper">
		<div class="form-label">FourSquare Client Secret</div>
		<div class="form-element">
                    <input type="text" name="client_secret" id="client_secret" value="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('iphone.foursquareclientsecret'); ?>" style="width:300px" />
                </div>
	</div>
        <input type="hidden" name="foursquare_settings" value="Yes" />
	<button type="submit" class="submit">Save</button>
</form>
