<div class="layout_top" style="margin-bottom:15px;">
  <?php echo $this->content()->renderWidget('iphone.search') ?>
</div>

<div style="clear:both"></div>

<div class="layout_left" style="width:605px">
  <?php echo $this->content()->renderWidget('iphone.points-list') ?>
</div>

<div class="layout_right" style="width:300px;">
  <!--CATEGORIES-->
  <?php echo $this->content()->renderWidget('iphone.categories') ?>
  
  <!--MAP-->
  <?php echo $this->content()->renderWidget('iphone.points-map') ?>
</div>