<?php
if ( count($this->points) ) {
  $i = 1;
  foreach ( $this->points as $point ):
    switch ( Zend_Controller_Front::getInstance()->getRequest()->getActionName() ) {
      case 'view':
	?>
	<div style="margin-bottom:10px; padding-bottom:5px; border-bottom:1px solid #CCC;">
	  <div style="float:left;">
	    <strong><?php echo $i++.'. '.$point['title']; ?></strong>
	    <p style="width:420px;">0 Reviews</p>
	    <p style="width:420px;"><?php echo $point['address']; ?></p>
	    <p style="width:420px;">Category: <?php echo $point['type_title']; ?></p>
	    <br />
	  </div>
	  <div style="float:right;">
	    <?php
	    $files = Engine_Api::_()->getItem('storage_file', $point['image']+1);
	    ?>
	    <img src="<?php echo (@$files->storage_path ? "http://".$_SERVER['HTTP_HOST'].$this->baseUrl()."/".@$files->storage_path : "http://".$_SERVER['HTTP_HOST'].$this->baseUrl()."/public/iphone/icons/NoPhotoAvailable.jpg" ); ?>"
			    style="width:150px;" />
	  </div>
	  <div style="clear:both; height:5px;"></div>
	  <div>
	    <img src="http://<?php echo $_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/icons/<?php echo $point['icon']; ?>" style="float:left; margin:0px 5px 5px 0px;" />
	    <?php echo $point['description']; ?>
	    <div style="clear:both"></div>
	  </div>
	</div>
	<?php
	break;
      default:
	?>
	<div style="margin-bottom:10px; padding-bottom:5px; border-bottom:1px solid #CCC;">
	  <div style="float:left;">
	    <a href="index.php/iphone/index/view/id/<?php echo $point['id']; ?>"><strong><?php echo $i++.'. '.$point['title']; ?></strong></a>
	    <p>Category: <?php echo $point['type_title']; ?></p>
	    <br />
	  </div>
	  <div style="float:right; width:160px;">
	    <p>0 Reviews</p>
	    <p><?php echo $point['address']; ?></p>
	  </div>
	  <div style="clear:both; height:5px;"></div>
	  <div>
	    <img src="http://<?php echo $_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/icons/<?php echo $point['icon']; ?>" style="float:left; margin:0px 5px 5px 0px;" />
	    <?php echo $point['description']; ?>
	    <div style="clear:both"></div>
	  </div>
	</div>
	<?php
	break;
    }
  endforeach;
}
else {
  ?>
  <div class="tip">
    <span>No venue has been found.</span>
  </div>
  <?php
}
?>
<div style="clear:both; height:15px;"></div>
