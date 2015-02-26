<!--SUB NAVIGATION-->
<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->navigation)->render();
		?>
	</div>
<?php endif; ?>

<div class="settings">
  <form action="./admin/iphone/manage/" method="POST" enctype="multipart/form-data" class="global_form">
	<div>
	  <h2>Edit Point</h2>
	  <div class="form-wrapper">
		<div class="form-label">
		  Sight ID
		</div>
		<div class="form-element">
		  <?php echo $this->point->id; ?>
		  <input type="hidden" name="id" value="<?php echo $this->point->id; ?>" />
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Sight type
		</div>
		<div class="form-element">
		  <select name="type">
			<?php foreach ( $this->types as $type ) { ?>
			  <option value="<?php echo $type->id ?>"
					  <?php if ( $this->point->type == $type->id ) echo 'selected'; ?> >
					  <?php echo $type->title; ?>
			  </option>
			<?php } ?>
		  </select>
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Added by
		</div>
		<div class="form-element">
		  <?php $user = Engine_Api::_()->user()->getUser($this->point->user_id); ?>
		  <a href="./profile/<?php echo $user->getTitle(); ?>"><?php echo $user->getTitle(); ?></a>
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Latitude
		</div>
		<div class="form-element">
		  <input type="text" name="lat" value="<?php echo $this->point->lat; ?>" />
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Longitude
		</div>
		<div class="form-element">
		  <input type="text" name="lng" value="<?php echo $this->point->lng; ?>" />
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Title
		</div>
		<div class="form-element">
		  <input type="text" name="title" value="<?php echo $this->point->title; ?>" />
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Description
		</div>
		<div class="form-element">
		  <textarea name="description" style="width:400px; height:200px;"><?php echo $this->point->description; ?></textarea>
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Image
		</div>
		<div class="form-element">
		  <?php $image = Engine_Api::_()->getItem('storage_file', $this->point->image+1); ?>
		  <a class="smoothbox" href="./iphone/gallery/view/id/<?php echo $this->point->id; ?>">
			<img src="<?php echo "http://".$_SERVER['HTTP_HOST'].$this->baseUrl()."/".@$image->storage_path; ?>" /></a>
		  <div id="enlargeImage" style="display:none;">
			<div style="text-align:center"><img src="{$url->url_base}{$sight.sight_image}" /></div>
		  </div>
		  <br />
		  <input type="checkbox" name="delete_image" value="yes" /> Delete image
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Upload new image
		</div>
		<div class="form-element">
		  <input type="file" name="new_image" />
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Date Created
		</div>
		<div class="form-element">
		  <?php if ( $this->point->dateupdated ) echo date('Y-m-d H:m:s', $this->point->datecreated) ?>
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label">
		  Date Updated
		</div>
		<div class="form-element">
		  <?php if ( $this->point->dateupdated ) echo date('Y-m-d H:m:s', $this->point->dateupdated); ?>
		</div>
	  </div>
	  <div class="form-wrapper">
		<div class="form-label"></div>
		<div class="form-element">
		  <button type="submit" class="button">Submit</button>
		  <button type="button" class="button" onclick="javascript:window.location.href='./admin/iphone/manage/';" >Go back</button>
		</div>
	  </div>
	  <input type="hidden" name="task" value="doedit" />
	  
	</div>
  </form>
</div>