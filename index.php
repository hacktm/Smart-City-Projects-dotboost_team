<html>
    <head>
        <title>Turist Destination Aplication</title>
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
            <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry,places"></script>
            <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
            <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
            <!-- Latest compiled and minified CSS -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
            <!-- Optional theme -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
            <!-- Latest compiled and minified JavaScript -->
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
            
            <style>
                #map_canvas{
                    width: 100%;
                    height: 40%;
                }
                body{
                    background-image: url('bg.png');
                    background-repeat: repeat-x repeat-y;
                    }
                
                
                .destination{
                    width: 40%;
                    min-height: 25%;
                    background: whitesmoke;
                    margin-left: 30%;
                    margin-top: 15px;
                    border-radius: 25px;
                    float: left;
                    padding: 15px;
                    text-align: center;
                    alignment-adjust: central;
                }
                .container{
                    width: 100%;
                    float: left;
                    margin-bottom: 15px;
                }
                .text-title{
                    font-size: 150%;
                }
                #hint_route{
                    background: white;
                    border-radius: 15px;
                    margin-top: 15px;
                    margin-left: 50px;
                    margin-right: 50px;
                    
                }
                select {
                   width: 240px;
                   height: 34px;
                   background-color: whitesmoke;
                   border: 1px solid #ccc;
                }
                
                .maps{
                  margin-bottom: 15px;
                }
                .header {
                    margin-top: 7px;
                }
                .dest {
                    margin-top: 9px;
                }
                .area {
                    margin-top: 9px;
                }
              
            </style>
            <script>
                //main function here
                function initialize() {
                var lat, lon, map, myOptions;
                
                $('#table').empty();
                $('#table_hint').hide( 'slow' );
                
                //check if user has geo feature
                if(navigator.geolocation) {
                
                navigator.geolocation.getCurrentPosition(
                //get position
                function(position){
                    lat = position.coords.latitude;
                    lon = position.coords.longitude;
                
                    //init map
                    myOptions = {
                       center: new google.maps.LatLng(lat, lon),
                       zoom: 14,
                       mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    
                   map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
                
                   var marker = new google.maps.Marker({
                      position: new google.maps.LatLng(lat, lon),
                      map: map,
                      title: 'My current position !!',
                      icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                  });
                    
                    var location = document.getElementById('location').value;
                    var area = document.getElementById('area').value;
                    
                 
                  var request = {
                    location: new google.maps.LatLng(lat, lon),
                    radius: area,
                    query: location
                  };
                
                  service = new google.maps.places.PlacesService(map);
                  service.textSearch(request, placesCallback);
                
                function placesCallback(results, status, pagination) {
                
                if (status == google.maps.places.PlacesServiceStatus.OK) {      
                    get_table(results)
                    for (var int = 0; int < results.length; int++) {    
                        var place_name = results[int].name;
                        var place = results[int];
                      createMarker(results[int], place_name);
                    }
                
                 } 
                
                if (pagination.hasNextPage) {
                    sleep:2;
                    pagination.nextPage();
                  }
                }
                
                function createMarker(place, place_name) {
                  var placeLoc = place.geometry.location;
                  var marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    draggable:true,
                    animation: google.maps.Animation.DROP,
                    title: place_name
                  });
                  
                  google.maps.event.addListener(marker, 'click', function() {
                    
                    
                  });
                  
                  }
                  
                  //$('<table>').appendTo($('#table'));
                  function get_table(obj){
                    console.log(obj);
                       for (var int = 0; int < obj.length; int++) {
                          var name = obj[int].name;
                          var address = obj[int].formatted_address;
                          var icon = obj[int].icon;
                          
                          var dest_lat = obj[int].geometry.location.k;  
                          var dest_lon = obj[int].geometry.location.B;
                          
                          $('<div class="destination"><div><img src="'+icon+'" width="30%" max-height="30%" /></div><div class="text-title">'+name+'</div><div>'+address+'</div><div><a href="#up_page" ><button class="btn btn-default" onclick="display_route('+dest_lat+', '+dest_lon+')">Get Directions&nbsp<span class="glyphicon glyphicon-play";></span></button></a></div></div>').appendTo($('#table'));
                       }
                  }
                  
                },
                // if there was an error
                function(error){
                    alert('Error found!!!');
                });
                }
                //case the users browser doesn't support geolocations
                else {
                   alert("Your Browser In Not Compatibile With Geolocation Support...Try Installing Google Chrome");
                  }
                 }
                
                function display_route(dest_lat, dest_long){
                 
                 var markerArray = [];
                    
                if(navigator.geolocation){
                
                navigator.geolocation.getCurrentPosition(
                //get position
                function(position){
                    lat = position.coords.latitude;
                    lon = position.coords.longitude;
                
                    //init map
                    myOptions = {
                       center: new google.maps.LatLng(lat, lon),
                       zoom: 14,
                       mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    
                   map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
                
                  // First, clear out any existing markerArray
                  // from previous calculations.
                  for (i = 0; i < markerArray.length; i++) {
                    markerArray[i].setMap(null);
                  }
                
                
                   var marker = new google.maps.Marker({
                      position: new google.maps.LatLng(lat, lon),
                      map: map,
                      title: 'The my current position !!',
                      icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                  });
                  
                      
                var directionsService = new google.maps.DirectionsService()
                  , directionsDisplay = new google.maps.DirectionsRenderer({
                      /* options */
                    })
                  , origin = new google.maps.LatLng(lat, lon)
                  , destination = new google.maps.LatLng(dest_lat, dest_long)
                  , // request options
                    request = {
                      origin: origin,
                      destination: destination,
                      travelMode: google.maps.TravelMode.DRIVING
                    };
                
                // set your map to the directions Display
                directionsDisplay.setMap(map);
                
                // route it (passing request options as 1st parameter)
                directionsService.route(request, function(response, status) {
                  if (status === google.maps.DirectionsStatus.OK) { // if it can route it
                    directionsDisplay.setDirections(response); // show route
                    showSteps(response);
                  }
                });
                  });
                      }
                      
                function show_hint_maps(myRoute){
                    $('#hint_route').hide( 'slow' );
                    //console.log(myRoute);
                    var myRoute_lenght = myRoute.steps.length;
                    $('#hint_route').empty();
                    $('<table>').attr('id','table_hint').attr('class','table').appendTo($('#hint_route'));
                    
                    for(i=0; i<myRoute_lenght; i++){
                        var duration = myRoute.steps[i].duration.text;
                        var distance = myRoute.steps[i].distance.text;
                        var instructions = myRoute.steps[i].instructions;
                      $("#table_hint").append('<tr><td>'+i+'</td><td>'+duration+'</td><td>'+distance+'</td><td>'+instructions+'</td></tr>');
                    }
                    
                    $('#hint_route').show( 'slow' );
                }    
                  
                //show maps in the maps  
                function showSteps(directionResult) {
                  // For each step, place a marker, and add the text to the marker's
                  // info window. Also attach the marker to an array so we
                  // can keep track of it and remove it when calculating new
                  // routes.
                  var myRoute = directionResult.routes[0].legs[0];
                  
                  show_hint_maps(myRoute);
                  
                  for (var i = 0; i < myRoute.steps.length; i++) {
                      var marker = new google.maps.Marker({
                        position: myRoute.steps[i].start_point,
                        map: map
                      });
                      //console.log(myRoute.steps[i]);
                      attachInstructionText(marker, myRoute.steps[i].instructions);
                      markerArray[i] = marker;
                  }
                }
                
                function attachInstructionText(marker, text) {
                  google.maps.event.addListener(marker, 'click', function() {
                    stepDisplay.setContent(text);
                    stepDisplay.open(map, marker);
                  });
                }    
                      
                      }
            
        </script>
    </head>
    <body>
            <nav class="navbar navbar-default" role="navigation">
                
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                      <ul class="nav navbar-nav">
                
            <div class="container-fluid">
           <div class="header">
           <table style="width: 100%;">
             <tr>
               <td><label class="dest">Destination:</label></td>
               <td>
               <select id="location" onchange="initialize()" class="form-control">
                      <option>Select Destination</option>
                      <option value="airport">Airport</option> 
                      <option value="aquarium">Aquarium</option>
                      <option value="art_gallery">Art gallery</option>
                      <option value="atm">ATM</option>
                      <option value="cafe">Cafe</option>
                      <option value="doctor">Doctor</option>
                      <option value="embassy">Embassy</option>
                      <option value="gas_station">Gas station</option>
                      <option value="hospital">Hospital</option>
                      <option value="insurance_agency">Insurance agency</option>
                      <option value="movie_theater">Cinema</option>
                      <option value="museum">Museum</option>
                      <option value="park">Park</option>
                      <option value="parking">Parking</option>
                      <option value="pharmacy">Pharmacy</option>
                      <option value="police">Police</option>
                      <option value="restaurant">Restaurant</option>
                      <option value="shopping_mall">Mall</option>
                      <option value="stadium">Stadium</option>
                      <option value="travel_agency">Travel agency</option>
                </select>
               </td>
               <td>&nbsp;&nbsp;<label class="area">Area:</label></td>
               <td>
               <select id="area" onchange="initialize()"  class="form-control">
                    <option value="50">50 meters</option>
                    <option value="100">100 meters</option>
                    <option value="500">500 meters</option>
                    <option value="1000">1 km</option>
                    <option value="5000">5 km</option>
                </select>
               </td>
             </tr>
           </table>
               
             </div>
             </div><!-- /.container-fluid -->
             </nav>     
                <a name="up_page"></a>
                <div id="map_canvas" class="maps"></div>
                <div id="hint_route" style="display: none;"></div>
                <div id="table"></div>
        </div>
    </body>
</html>