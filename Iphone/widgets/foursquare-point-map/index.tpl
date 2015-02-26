<div class="headline">
    <div class="tabs">
        <ul class="navigation">
            <li>
                <a href="/index.php/iphone" class="menu_activitypoints_main activitypoints_topusers">iPhone</a>
            </li>
            <li class="active">
                <a href="/index.php/iphone/foursquare/index" class="menu_activitypoints_main activitypoints_help">FourSquare</a>
            </li>
        </ul>
    </div>
</div>
<div style="clear:both;"></div>

<?php if ($this->pointsEncoded): ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=<?php echo Engine_Api::_()->getApi('settings', 'core')->iphone['gmap_api_key']; ?>" type="text/javascript"></script>

<script type="text/javascript">
window.addEvent('domready', function () {
  var points = <?php echo $this->pointsEncoded; ?>;
  map = new google.maps.Map2(document.getElementById("map_canvas"));

  latlng = new google.maps.LatLng(points[0].lat, points[0].lng);
  map.setCenter(latlng, 15);
  var mapControl = new GMapTypeControl();
  map.addControl(mapControl);
  map.addControl(new GLargeMapControl3D());
  //map.addControl(new GSmallZoomControl3D);
  map.setMapType(G_NORMAL_MAP);
  for ( i = 0; i < points.length; i++ )
  {
    markerImage = new GIcon(G_DEFAULT_ICON);
    markerImage.shadow = '';
    markerImage.iconSize = new GSize(22, 21);
    markerImage.iconAnchor = new GPoint(22, 21);
    markerImage.infoWindowAnchor = new GPoint(22, 21);
    markerImage.image = points[i].icon;
    markerImage.imageMap = [ 0,0 , 22,0 , 22,21 , 0,21 ];
    markerOptions = {
      icon: markerImage,
      title: points[i].id
    };
    latlng = new GLatLng(points[i].lat, points[i].lng);
    marker = new GMarker(latlng, markerOptions);
    map.addOverlay(marker);
    
    // MARKER CLICK EVENT
    GEvent.addListener(marker, "click", function() {
      map.closeInfoWindow();
      title = this.getTitle();
      var req = new Request.JSON({
        url: 'index.php/iphone/foursquare/get-venue-info',
        method: 'get',
        data: {
          'id': title
        },
        onSuccess: function (response) {
          if ( response.venue.location.lat ) {
            latlng = new google.maps.LatLng(response.venue.location.lat, response.venue.location.lng);
            html = "<div style='width:300px;'>"+
                "<h3>"+response.venue.name+"</h3>"+
                "<p><b>"+response.venue.location.address+", "+response.venue.location.city+", "+response.venue.location.state+"</b></p><br />"+
                "<p><b>If you want, you can check in here.</b></p><br />"+
                //"<form name='checkin' method='post' action='index.php/iphone/foursquare/checkin'>"+
                //"<input type='hidden' name='venue_id' value='"+response.venue.id+"' />"+
                "<button type='submit' id='"+title+"' name='submit' onclick='checkinUser(this)'>Checkin Here</button><br />"+
                //"</form>"+
              "</div>";
            map.openInfoWindowHtml(latlng, html, {noCloseOnClick: true});
          }
        }
      }).send();
    });
    //End Marker Click Event
  }
});

function checkinUser(obj)
{
    window.addEvent('domready', function () {
        title = obj.get('id');
        var req = new Request.JSON({
            url: 'index.php/iphone/foursquare/checkin',
            method: 'get',
            data: {
                'id': title
            },
            onSuccess: function (response) {
                alert(response[0].item.message);
            }
        }).send();
    });
}
</script>
<h3>Map</h3>
<a style="float:right" href="#update">Update your location</a>
<div style="clear:both"></div>
<div id="map_canvas" style="width:950px; height:520px; margin:0px auto 15px; position:relative;"></div>
<?php endif; ?>
<div style="clear:both;"></div>