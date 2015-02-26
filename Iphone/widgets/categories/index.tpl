<div class="quicklinks">
  <ul class="navigation">
  <?php
  foreach ( $this->types as $type ):
    ?>
    <li>
      <img
	   src="http://<?php echo $_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/icons/<?php echo $type->icon; ?>"
	   style="height: 24px; margin-right: -17px; vertical-align: middle; width: 24px;;" />
      <a href="http://<?php echo $_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/index.php/iphone/index/category/id/<?php echo $type->id; ?>" class="buttonlink" style="cursor:pointer;"><?php echo $type->title; ?></a><br />
    </li>
    <?php
  endforeach;
  ?>
  </ul>
</div>
