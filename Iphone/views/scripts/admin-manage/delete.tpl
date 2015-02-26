<form class="global_form_popup" action="./admin/iphone/manage/delete/id/<?php echo $this->point->id; ?>" method="POST">
  <div>
	<h2>Delete Point</h2>
	<div class="form-wrapper">
	  Are you sure you want to delete this point?
	</div>
	<div class="form-wrapper">
		<button type="submit" class="button">Delete</button>
		<button type="button" class="button" onclick="javascript:parent.Smoothbox.close();">Cancel</button>
	</div>
  </div>
</form>