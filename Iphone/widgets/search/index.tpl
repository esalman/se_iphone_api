<form method="POST" action="http://<?php echo $_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/index.php/iphone" class="global_form">
  <div style="width:100%;">
    <div>
      Search for location (address, neighbourhood, city, location or zip)<br />
      <input type="text" name="searchNear" /><br /><br />
      <button type="submit">Search</button>
    </div>
  </div>
</form>
