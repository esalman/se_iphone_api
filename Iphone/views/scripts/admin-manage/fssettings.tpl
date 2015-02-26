<!--SUB NAVIGATION-->
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->navigation)->render();
		?>
	</div>
<?php endif; ?>

<h2>FourSquare Settings</h2>

* Edit FourSquare Client ID and Client Secret Code.
<br /><br />
<?php if(!empty($this->statusMessage)){ ?><p style="color: #006699; font-weight: bold;"><?php echo $this->statusMessage; ?></p><br /><?php } ?>
<form action="" method="POST">
    <label for="client_id">FourSquare Client ID :<br />
        <input type="text" name="client_id" id="client_id" value="<?php echo $this->clientId; ?>" style="width:400px" />
    </label>
    <br /><br />
    <label for="client_secret">FourSquare Client Secret :<br />
        <input type="text" name="client_secret" id="client_secret" value="<?php echo $this->clientSecret; ?>" style="width:400px" />
    </label>
  <br /><br />
  <button type='submit' class='submit'>Save</button>
</form>