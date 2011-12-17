<!DOCTYPE html>
<html>
<head>
    <?php

function getLatLong($address){
    if (!is_string($address))die("All Addresses must be passed as a string");
    $_url = sprintf('http://maps.google.com/maps?output=js&q=%s',rawurlencode($address));
    $_result = false;
    if($_result = file_get_contents($_url)) {
        if(strpos($_result,'errortips') > 1 || strpos($_result,'Did you mean:') !== false) return false;
        preg_match('!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U', $_result, $_match);
        $_coords['lat'] = $_match[1];
        $_coords['long'] = $_match[2];
    }
    return $_coords;
}
echo '<pre>';var_dump(getLatLong('221 N 6th Elmwood NE 68349'));echo '<pre>';
?>
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
  function initialize() {
    var position = new google.maps.LatLng(40.843, -96.29);
    var myOptions = {
      zoom: 10,
      center: position,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(
        document.getElementById("map_canvas"),
        myOptions);

    var marker = new google.maps.Marker({
        position: position,
        map: map,
        title:"This is the place."
    });

    var contentString = 'Hello <strong>World</strong>!';
    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map,marker);
    });

  }

</script>
</head>
<body onload="initialize()">
  <div id="map_canvas" style="width:500px; height:500px"></div>
</body>
</html>