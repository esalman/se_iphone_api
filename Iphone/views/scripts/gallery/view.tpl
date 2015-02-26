<div style='width:800px; text-align:center;'>
	<span style='font-size:15px; font-weight:bold; color:#900; '><?php echo $this->point->title; ?></span><br />
	Reported by
	<?php $user = Engine_Api::_()->user()->getUser($this->point->user_id); ?>
	<a href="./profile/<?php echo $user->getTitle(); ?>"><?php echo $user->getTitle(); ?></a>
	<br />
	<span><?php echo $this->point->description; ?></span>
	<br /><br />
	<?php $image = Engine_Api::_()->getItem('storage_file', $this->point->image); ?>
	<img src="<?php echo "http://".$_SERVER['HTTP_HOST'].$this->baseUrl()."/".@$image->storage_path; ?>" />
</div>
