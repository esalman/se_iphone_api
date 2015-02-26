<div class="layout_top" style="margin-bottom:15px;">
  <?php // echo $this->content()->renderWidget('iphone.foursquare-tab-links') ?>
</div>

<div style="clear:both"></div>

<div class="layout_top" style="margin-bottom:15px;">
  <?php echo $this->content()->renderWidget('iphone.search') ?>
</div>

<div style="clear:both"></div>

<div class="layout_top" style="margin-bottom:15px;">
  <!--MAP-->
  <?php echo $this->content()->renderWidget('iphone.points-large-map') ?>
  <br />
  <!--<h3><a href="index.php/iphone/add-point">Click Here to Tag Location</a></h3>-->
</div>

<div style="clear:both"></div>

<div class="layout_left" style="width:605px">
  <?php echo $this->content()->renderWidget('iphone.points-list') ?>
</div>

<div class="layout_right" style="width:300px;">
  <!--CATEGORIES-->
  <?php echo $this->content()->renderWidget('iphone.categories') ?>
</div>