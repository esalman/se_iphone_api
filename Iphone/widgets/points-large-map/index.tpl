<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=
<?php echo Engine_Api::_()->getApi('settings', 'core')->iphone['gmap_api_key']; ?>
" type="text/javascript"></script>

<style>
button.mapButton {
  background: url(<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/hybrid_sat.png) no-repeat;
  border: none;
  color: #FFF;
  font-family: trebuchet ms;
  font-weight: bold;
  width: 75px;
  height: 24px;
}
</style>


<script type="text/javascript">
  // VARS
  var currentLoc = null;
  var html = null;
  var latlng = null;
  var latlng1 = null;
  var map = null;
  var mapState = 1;
  var marker = null;
  var markerImage = null;
  var markerOptions = null;
  var newLoc = null;
  var panByPixel = null;
  var timer = null;
  var title = null;
  
  // TIMED FUNCTION
  function fetchSighting() {
    clearTimeout(timer);
    var req = new Request.JSON({
      url: '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/index.php/iphone/ajax/getnext',
      method: 'get',
      onSuccess: function (response) {
        if ( response != null ) {
          latlng = new google.maps.LatLng(response.sight_info.lat, response.sight_info.lng);
          panByPixel = map.fromLatLngToContainerPixel(latlng);
          html = "<div style='width:300px;'>"+
			"<span style='font-size:15px; font-weight:bold; color:#900;'>"+
			response.sight_info.title+"</span><br />"+
			"<img style='float:left; margin:0px 10px 10px 0px;' src='"+
			response.sight_info.image+ "' height='100px' width='100px' /><span>"+
			response.sight_info.description+"</span></div>";
          map.panBy(new GSize( - panByPixel.x + 450, - panByPixel.y + 350 ));
        }
        
        timer = setTimeout('fetchSighting()', 5000);
      }
    }).send();
    
  }
  
  // MAP CONTROLS
  function TextualControl() {}
  
  TextualControl.prototype = new GControl();
  
  TextualControl.prototype.initialize = function(map) {
    var container = new Element('div', {
      'styles': {
        'background': 'url(<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/map_bg.png) repeat-x',
        'height': '26px',
        'padding': '10px',
        'position': 'absolute',
        'width': '930px'
      }
    });
    var mapHeader = new Element('div', {
      'html': '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'iPhone') ?> Map',
      'styles': {
        'color': '#F48215',
        'float': 'left',
        'font-size': '18pt',
        'width': '620px',
        'text-align': 'center',
        'padding': '0px 0px 0px 150px'
      }
    });
    var startStop = new Element('button', {
      'html': 'Stop',
      'class': 'mapButton',
      'events': {
        'click': function(){
          if ( this.get('html') == 'Start' ) {
            this.set('html', 'Stop&nbsp;');
            mapState = 1;
            timer = setTimeout('fetchSighting()', 500);
          }
          else {
            this.set('html', 'Start');
            mapState = 0;
            clearTimeout(timer);
          }
        }
      },
      'id': 'idButton'
    });
    var mapTypeChng = new Element('button', {
      'html': 'Hybrid',
      'class': 'mapButton',
      'events': {
        'click': function(){
          if ( this.get('html') == 'Hybrid' ) {
            this.set('html', 'Normal');
            map.setMapType(G_HYBRID_MAP);
          }
          else {
            this.set('html', 'Hybrid');
            map.setMapType(G_NORMAL_MAP);
          }
        }
      },
      'id': 'typeButton'
    });
    container.appendChild(mapHeader);
    container.appendChild(startStop);
    container.appendChild(mapTypeChng);
    map.getContainer().appendChild(container);
    return container;
  }
  
  TextualControl.prototype.getDefaultPosition = function() {
    return new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(0, 0));
  }
  
  window.addEvent('domready', function () {
    
    var req = new Request.JSON({
      url: '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/index.php/iphone/ajax/getall',
      method: 'post',
      onSuccess: function (response) {
		map = new google.maps.Map2(document.getElementById("map_canvas"));
        latlng = new google.maps.LatLng(response[0].sight_info.lat, response[0].sight_info.lng);
        map.setCenter(latlng, 4);
        map.addControl(new TextualControl());
        map.setMapType(G_NORMAL_MAP);
        var topRight = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(10,65));
        map.addControl(new GLargeMapControl3D(), topRight);
        map.enableGoogleBar();
        html = "<div style='width:300px;'>"+
			"<span style='font-size:15px; font-weight:bold; color:#900;'>"+
			response[0].sight_info.title+"</span><br />"+
			"<img style='float:left; margin:0px 10px 10px 0px;' src='"+
			response[0].sight_info.image+ "' height='100px' width='100px' /><span>"+
			response[0].sight_info.description+"</span></div>";
        
        for ( i = 0; i < response.length; i++ )
        {
          markerImage = new GIcon(G_DEFAULT_ICON);
          markerImage.iconSize = new GSize(45, 42);
          markerImage.iconAnchor = new GPoint(22, 21);
          markerImage.infoWindowAnchor = new GPoint(22, 21);
          markerImage.image = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->baseUrl(); ?>/application/modules/Iphone/externals/images/icons/'+response[i].sight_info.icon;
          markerImage.imageMap = [ 0,0 , 45,0 , 45,42 , 0,42 ];
          markerOptions = {
            icon: markerImage,
            title: response[i].sight_info.sight_id
          };
          latlng = new GLatLng(response[i].sight_info.lat, response[i].sight_info.lng);
          marker = new GMarker(latlng, markerOptions);
          map.addOverlay(marker);
          
          // MARKER CLICK EVENT
          GEvent.addListener(marker, "click", function() {
            map.closeInfoWindow();
            title = this.getTitle();
            timer = setTimeout('fetchSighting()', 5000);
            var req = new Request.JSON({
              url: 'sighting_ajax.php',
              method: 'get',
              data: {
                'task': 'getbyid',
                'id': title
              },
              onSuccess: function (response) {
                if ( response.sight_info.sight_lat ) {
                  latlng = new google.maps.LatLng(response.sight_info.lat, response.sight_info.lng);
                  html = "<div style='width:300px;'>"+
					"<span style='font-size:15px; font-weight:bold; color:#900;'>"+
					response.sight_info.sight_title+"</span><br />"+
					"<img style='float:left; margin:0px 10px 10px 0px;' src='"+
					response.sight_info.sight_image+ "' height='100px' width='100px' /><span>"+
					response.sight_info.sight_desc+"</span></div>";
                  map.openInfoWindowHtml(latlng, html, {noCloseOnClick: true});
                }
              }
            }).send();
          });
        }
        
        // MAP MOVE END EVENT - OPEN INFO WINDOW
        GEvent.addListener(map, "moveend", function() {
          if ( mapState ) {
            map.openInfoWindowHtml(latlng, html, {noCloseOnClick: true});
          }
        });
        
        timer = setTimeout('fetchSighting()', 500);

      }
    }).send();    
    
  });

</script>

<div id="map_canvas" style="width: 950px; height: 520px;  margin:0px auto 15px; position:relative;"></div>
<div id="debug"></div>