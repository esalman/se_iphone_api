<br />
<h3>FourSquare Point List</h3><?php
if(count($this->points))
{
  $i = 1;
  foreach($this->points as $point):
?>
    <div style="margin-bottom:10px; padding-bottom:5px; border-bottom:1px solid #CCC;">
      <div style="float:left;">
        <a href="#"><strong><?php echo $i++.'. '.$point['title']; ?></strong></a>
        <p>Category: <?php echo $point['type_title']; ?></p>
        <br />
      </div>
      <div style="float:right; width:160px;">
        <p>0 Reviews</p>
        <p><?php echo $point['address']; ?></p>
      </div>
      <div style="clear:both; height:5px;"></div>
      <div>
        <img src="<?php echo $point['icon']; ?>" style="float:left; margin:0px 5px 5px 0px;" />
        <?php echo $point['description']; ?>
        <div style="clear:both"></div>
      </div>
    </div>
<?php
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
<div style="clear:both;"></div>
