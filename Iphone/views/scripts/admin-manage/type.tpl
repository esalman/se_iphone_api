<!--SUB NAVIGATION-->
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->navigation)->render();
		?>
	</div>
<?php endif; ?>

<h2>Point Types & Email Settings</h2>

* You can add multiple recipient addresses in any field separating them by comma.
<br /><br />

<form action="" method="POST">
  <table cellpadding='0' cellspacing='0' class='admin_table'>
	<thead>
	  <tr>
		<th style='width:1%;'>Type ID</a></th>
		<th class='admin_table_bold'>Type name</a></th>
		<th class='admin_table_centered'>Type icon</a></th>
		<th class='admin_table_centered'>Recipient email address</a></th>
	  </tr>
	</thead>
    <?php foreach ( $this->types as $type ) { ?>
	<tbody>
	  <tr style='width:1%;'>
		<td class="item">
		  <?php echo $type->id; ?>
		</td>
		<td class='admin_table_bold'>
		  <input type="text" name="type_name[]" value="<?php echo $type->title; ?>" />
		</td>
		<td class='admin_table_centered'>
		  <!--<input type="text" name="type_name[]" value="{$types[types_loop].sightingtype_title}" />-->
		  <img src="./public/iphone/icons/<?php echo $type->icon; ?>" />
		</td>
		<td class='admin_table_centered'>
		  <input type="text" name="type_email[]" value="<?php echo $type->email; ?>" />
		</td>
	  </tr>
	</tbody>
    <?php } ?>
  </table>
  <br />
  <button type='submit' class='submit'>Save</button>
</form>