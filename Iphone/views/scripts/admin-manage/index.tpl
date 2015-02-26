<!--SUB NAVIGATION-->
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->navigation)->render();
		?>
	</div>
<?php endif; ?>

<h2>Points Settings</h2>

<?php if ($this->points->count() == 0) { ?>

  <table cellpadding='0' cellspacing='0' width='400' align='center'>
    <tr>
      <td align='center'>
        <div class='box' style='width: 300px;'><b>There are no points added yet.</b></div>
      </td>
    </tr>
  </table>
  <br />

<?php } else { ?>

  <script language='JavaScript'> 
  <!---
  var checkboxcount = 1;
  function doCheckAll() {
    if(checkboxcount == 0) {
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = false;
      }}
      checkboxcount = checkboxcount + 1;
      }
    } else
      with (document.items) {
      for (var i=0; i < elements.length; i++) {
      if (elements[i].type == 'checkbox') {
      elements[i].checked = true;
      }}
      checkboxcount = checkboxcount - 1;
      }
  }
  // -->
  </script>
  
  <?php echo $this->pointSuccessMessage; ?>
	
  <?php echo $this->paginationControl($this->points); ?>

  <form action='admin/iphone/manage' method='post'>
	<table cellpadding='0' cellspacing='0' class='admin_table'>
	  <thead>
		<tr>
		  <!--<th style='width:1%;'><input type='checkbox' name='select_all' onClick='javascript:doCheckAll()'></th>-->
		  <th style='width:1%;'><a href='admin_viewsighting.php?s={$i}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>ID</a></th>
		  <th class='admin_table_bold'><a href='admin_viewsighting.php?s={$t}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>Title</a></th>
		  <th class='admin_table_bold'><a href='admin_viewsighting.php?s={$o}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>User</a></th>
		  <th style='width:1%;'><a href='admin_viewsighting.php?s={$d}&p={$p}&f_title={$f_title}&f_owner={$f_owner}'>Date added</a></th>
		  <th class='admin_table_options'>Options</th>
		</tr>
	  </thead>
	  
	  <?php foreach ( $this->points as $point ) { ?>
	  <tbody>
		<tr class=''>
		<!--  <td style='width:1%;'>-->
		<!--	<input type='checkbox' name='delete_sights[]' value='<?php echo $point->id; ?>' />-->
		<!--	</td>-->
		  <td style='width:1%;'>
			<?php echo $point->id; ?>
		  </td>
		  <td class='admin_table_bold'>
			<?php echo $point->title; ?>
		  </td>
		  <td class='admin_table_bold'>
			<a href='./profile/<?php
			  $user = Engine_Api::_()->user()->getUser($point->user_id);
			  echo $user->username; ?>' target='_blank'><?php echo $user->getTitle(); ?></a>
		  </td>
		  <td style='width:1%;'>
			<?php echo  date('Y-m-d H:i:s', $point->datecreated); ?>
		  </td>
		  <td class='admin_table_options'>
			<a href='<?php echo $this->url(array('action' => 'edit', 'id' => $point->id)); ?>'><?php echo $this->translate("edit") ?></a> |
			<a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $point->id));?>'><?php echo $this->translate("delete") ?></a>
		  </td>
		</tr>
	  <?php } ?>
	  </tbody>
	</table>
	<br />
  
	<?php echo $this->paginationControl($this->points); ?>
	
  </form>

<?php } ?>