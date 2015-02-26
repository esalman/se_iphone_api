<form class="global_form" id="addpoint" action="index.php/iphone/add-point" method="post" enctype="multipart/form-data">
  <div style="width: 100%;">
    <div>
        <!-- addpoint -->
        <?php if(!empty($this->errorMessage)){ ?>
            <ul class="form-errors">
                <?php echo $this->errorMessage; ?>
            </ul>
        <?php } ?>
        <?php if($this->statusMessage != ''){ ?><h4 style="color:#006633;"><?php echo $this->statusMessage; ?></h4><?php } ?>
        <div id="form_box">
            <h2>Tag Location</h2>
            <label>Address:</label><br />
            <input name="address" type="text" style="width: 300px;" /><br /><br />
            <label>Name:</label><br />
            <input name="name" type="text" style="width: 300px;" /><br /><br />
            <label>Description:</label><br />
            <input name="description" type="text" style="width: 300px;" /><br /><br />
            <label>Type ID:</label><br />
            <select name="type_id" style="width: 300px;">
                <option value="" selected="selected">choose..</option>>
                <?php foreach($this->types as $type){ ?>
                <option value="<?php echo $type->id; ?>"><?php echo $type->title; ?></option>
                <?php } ?>
            </select><br /><br />
            <label>Point Photo:</label><br />
            <input type="file" name="photo" style="width: 300px;" /><br /><br />
            <button type="submit" name="submit">Add Point</button>
        </div>
    </div>
  </div>
</form>

