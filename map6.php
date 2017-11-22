<html> 

<style>
html, body, #map-canvas {
    height: 100%;
    width: 100%;
    margin: 0px;
    padding: 0px
}

</style>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry,places,drawing&ext=.js"></script>
</head>
<body>



<?php

if(isset($_POST['address'])){

    $address = $_POST['address'];
/**
* Author: CodexWorld
* Author URI: http://www.codexworld.com
* Function Name: getLatLong()
* $address => Full address.
* Return => Latitude and longitude of the given address.
**/
function get_driving_information($start, $finish, $raw = false)
{
    if(strcmp($start, $finish) == 0)
    {
        $time = 0;
        if($raw)
        {
            $time .= ' seconds';
        }
 
        return array('distance' => 0, 'time' => $time);
    }
 
    $start  = urlencode($start);
    $finish = urlencode($finish);
 
    $distance   = 'unknown';
    $time       = 'unknown';
 
    $url = 'http://maps.googleapis.com/maps/api/directions/xml?origin='.$start.'&destination='.$finish.'&mode=driving&language=en-EN&sensor=false';
    if($data = file_get_contents($url))
    {
        $xml = new SimpleXMLElement($data);
 
        if(isset($xml->route->leg->duration->value) AND (int)$xml->route->leg->duration->value > 0)
        {
            if($raw)
            {
                $distance = ((string)$xml->route->leg->distance->text);
                $time     = (string)$xml->route->leg->duration->text;

            }
            else
            {
                $distance = (int)$xml->route->leg->distance->value / 1000 / 1.609344; 
                $time     = (int)$xml->route->leg->duration->value;
                $distance=$distance*1.609344;
            }
        }
        else
        {
            throw new Exception('Could not find that route');
        }
 
        return array('distance' => $distance, 'time' => $time);
    }
    else
    {
        throw new Exception('Could not resolve URL');
    }
}

try
{
    $info = get_driving_information('colombo', $address);?>
    <div style="width: 500px; height: 50px; padding-left: 100px;">
  <label>To: <?php echo "$address";?></label><br>
<input type="text" name="dist" value="<?php  echo (round($info['distance'])).' km ';?>" readonly>  </div>
 
<script language="javascript"> 
function changeParent() { 
    var value1 ="<?php  echo (round($info['distance']))?>"
    var result=0;
    var txtFirstNumberValue = value1;
         if(!txtFirstNumberValue.match(/\S/)){
                                {
                                    result=0;
                                }
                            }
                            else if (txtFirstNumberValue>20) {
                                result = parseInt(txtFirstNumberValue) * 40 ;{
                                    
                                }}
                            else{
                             result = parseInt(txtFirstNumberValue) * 30 ;{
                                   
                                }}


                        
  window.opener.document.getElementById('txt1').value=value1;
  window.opener.document.getElementById('txt2').value=result;
  window.close();
} console.log(~~NaN);
</script> 
 <div style="width: 500px; height: 50px; padding-left: 100px;">
<form> 
<input type=button onclick="javascript:changeParent()" value="submit the distance"> 
</form> </div>
</div><?php
}
catch(Exception $e)
{
    echo 'Caught exception: '.$e->getMessage()."\n";
}





function getLatLong($address){
    if(!empty($address)){
        //Formatted address
        $formattedAddr = str_replace(' ','+',$address);
        //Send request and receive json data by address
        $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddr.'&sensor=false'); 
        $output = json_decode($geocodeFromAddr);
        //Get latitude and longitute from json data
        $data['latitude']  = $output->results[0]->geometry->location->lat; 
        $data['longitude'] = $output->results[0]->geometry->location->lng;
        //Return latitude and longitude of the given address
        if(!empty($data)){
            return $data;
        }else{
            return false;
        }
    }else{
        return false;   
    }
}
/**
 * Use getLatLong() function like the following.
 */


$latLong = getLatLong($address);
$latitude = $latLong['latitude']?$latLong['latitude']:'Not found';
$longitude = $latLong['longitude']?$latLong['longitude']:'Not found';




?>
<input type="button" id="routebtn" value="route" />
<div id="map-canvas" style="width: 500px; height: 500px"></div>
<script type="text/javascript">


    function mapLocation() {
    var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
    var map;

    function initialize() {
        directionsDisplay = new google.maps.DirectionsRenderer();
        var chicago = new google.maps.LatLng(6.9271, 79.8612);
        var mapOptions = {
            zoom: 7,
            center: chicago
        };
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        directionsDisplay.setMap(map);
        google.maps.event.addDomListener(document.getElementById('routebtn'), 'click', calcRoute);
    }

    function calcRoute() {
     var bool = '<?php echo "$latitude"?>';
     var booll = '<?php echo "$longitude"; ?>';
      
        var start = new google.maps.LatLng(6.9271, 79.8612);
        //var end = new google.maps.LatLng(38.334818, -181.884886);
        var end = new google.maps.LatLng(bool, booll);
        /*
var startMarker = new google.maps.Marker({
            position: start,
            map: map,
            draggable: true
        });
        var endMarker = new google.maps.Marker({
            position: end,
            map: map,
            draggable: true
        });
*/
        var bounds = new google.maps.LatLngBounds();
        bounds.extend(start);
        bounds.extend(end);
        map.fitBounds(bounds);
        var request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function (response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.setMap(map);
            } else {
                alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
            }
        });
    }

    google.maps.event.addDomListener(window, 'load', initialize);
}
mapLocation();

</script>
<?php

?>

<?php  
}
?>
<form action="map6.php" method="POST">
<div style="width: 500px; height: 50px; padding-top: 30px;">
<label>Address</label>
<input type="text" name="address"  value="" ></input>
 <input class="btn btn-primary" type="submit" name="submit" value="submit" />
</form>
</div>


</div>
    




</body>

</html>