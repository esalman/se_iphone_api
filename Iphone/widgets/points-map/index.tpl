<?php if ($this->pointsEncoded): ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=
<?php echo Engine_Api::_()->getApi('settings', 'core')->iphone['gmap_api_key']; ?>
" type="text/javascript"></script>

<script type="text/javascript">
window.addEvent('domready', function () {
  var points = <?php echo $this->pointsEncoded; ?>;
  map = new google.maps.Map2(document.getElementById("map_canvas"));
  latlng = new google.maps.LatLng(points[0].lat, points[0].lng);
  map.setCenter(latlng, 11);
  map.addControl(new GSmallZoomControl3D);
  map.setMapType(G_NORMAL_MAP);
  for ( i = 0; i < points.length; i++ )
  {
    markerImage = new GIcon(G_DEFAULT_ICON);
    markerImage.shadow = '';
    markerImage.iconSize = new GSize(22, 21);
    markerImage.iconAnchor = new GPoint(22, 21);
    markerImage.infoWindowAnchor = new GPoint(22, 21);
    markerImage.image = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/icons/'+points[i].icon;
    markerImage.imageMap = [ 0,0 , 22,0 , 22,21 , 0,21 ];
    markerOptions = {
      icon: markerImage,
      title: points[i].id
    };
    latlng = new GLatLng(points[i].lat, points[i].lng);
    marker = new GMarker(latlng, markerOptions);
    map.addOverlay(marker);
    
    GEvent.addListener(marker, "click", function() {
      map.closeInfoWindow();
      title = this.getTitle();
      var req = new Request.JSON({
	url: 'index.php/iphone/ajax/getnext',
	method: 'get',
	data: {
	  'id': title
	},
	onSuccess: function (response) {
	  latlng = new google.maps.LatLng(response.sight_info.lat, response.sight_info.lng);
	  map.openInfoWindowHtml(latlng, '<img src="'+response.sight_info.image+'" style="float:left; width:48px; margin:0px 3px 3px 0px;"><span>'+response.sight_info.title+'</span>', {noCloseOnClick: true});
	}
      }).send();
    });

  }
});
</script>
<h3>Map</h3>
<div id="map_canvas" style="width:300px; height:300px;  margin:0px auto 15px; position:relative;"></div>
<?php endif; ?>