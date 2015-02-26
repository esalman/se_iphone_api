<h2>CrazyGood Gallery</h2>
<?php
foreach ( $this->points as $point ) {
	$image = Engine_Api::_()->getItem('storage_file', $point->image);
	$thumb = Engine_Api::_()->getItem('storage_file', $point->image+1);
	?>
	<div style="width:120px; height:140px; overflow:hidden; float:left; margin:3px;">
		<div style="width:120px;height:16px;float:left; margin:3px; cursor:pointer; color:#990000;font-size:12px;font-weight:bold; text-align:center;" title="<?php echo $point->title; ?>">
		  <?php echo substr($point->title, 0, 15).'..'; ?>
		</div>
		<div style="width:120px; height:120px; overflow:hidden; float:left; margin:3px;" title="<?php echo $point->title; ?>">
			<a class="smoothbox" href="./iphone/gallery/view/id/<?php echo $point->id; ?>">
				<img src="<?php echo "http://".$_SERVER['HTTP_HOST'].$this->baseUrl()."/".@$thumb->storage_path; ?>" alt="<?php echo $point->title; ?>" width="200px" />
			</a>
		</div>
	</div>
	<?
}
?>