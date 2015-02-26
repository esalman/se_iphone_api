<?php
    $setEnable = $this->setEnable;
    $getLatLng = $this->getLatLng;
    $errors = $this->errors;
?>
<?php //echo $this->content()->renderWidget('iphone.foursquare-tab-links'); ?>

<div class="layout_left" style="width:605px">
  <?php echo $this->content()->renderWidget('iphone.foursquare-point-list'); ?>
</div>

<?php if(empty($errors)){ ?>
<div class="layout_right" style="width:300px;">
    <h3>Update Your Location</h3>
    <br />
    
    <form name="getLatLng" method="post" action="index.php/iphone/foursquare/index">
        <p>Enter Your Address</p>
        <input type="text" name="address" id="address" style="width: 300px;" />
        <br /><br />
<!--        <p>Enter Radius</p>
        <input type="text" name="radius" id="radius" style="width: 300px;" />
        <input type="hidden" name="latlng" value="Yes" />
        <br /><br />-->
        <button type="submit" id="llsubmit" name="llsubmit">Get Venues Near You</button>
    </form>
</div>
<?php } ?>

<?php echo $this->content()->renderWidget('iphone.foursquare-point-map'); ?>

<?php if($setEnable){ ?>
<div class="layout_top" style="margin-bottom:15px;">
    <h3>Four Square Account Enable</h3>
    <form name="setEnable" method="post" action="index.php/iphone/foursquare/index">
        <input type="hidden" name="enable" value="Yes" />
        <br /><br />
        <button type="submit" id="ensubmit" name="ensubmit">Enable Your Account</button>
    </form>
</div>
<?php } ?>

<?php if($getLatLng){ ?>
<div class="layout_top" style="margin-bottom:15px;">
    <h3>Get Latitude &amp; Longitude</h3>
    <br />
    <?php //if(!empty($errors)){ ?><p style="color: #ff0000; font-weight: bold;"><?php //echo $errors; ?></p><br /><?php //} ?>
    <form name="getLatLng" method="post" action="index.php/iphone/foursquare/index">
        <p>Enter Your Address</p>
        <input type="text" name="address" id="address" style="width: 300px;" />
        <br /><br />
<!--        <p>Enter Radius</p>
        <input type="text" name="radius" id="radius" style="width: 300px;" />
        <input type="hidden" name="latlng" value="Yes" />
        <br /><br />-->
        <button type="submit" id="llsubmit" name="llsubmit">Get Venues Near You</button>
    </form>
</div>
<?php } ?>




