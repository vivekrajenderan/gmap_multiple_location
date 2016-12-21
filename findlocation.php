<style>
#map_canvas {
  height: 100%;
}
</style>
     <?php 
     $paths = array(array (
     		      "source_latitude" =>8.8651642,
               "source_longitude" =>77.5000799,
               "destination_latitude"=>8.8252078,
               "destination_longitude"=>77.5691064,
               "current_lat"=>8.83707131,               
               "current_long"=>77.54639626,
               "request_no"=>"SN007",
               "driver_name"=>"Saravanan"               
               ),            
              array (
               "source_latitude" =>8.8651642,
               "source_longitude" =>77.5000799,
               "destination_latitude"=>8.8252078,
               "destination_longitude"=>77.5691064,
               "current_lat"=>8.83707131,               
               "current_long"=>77.54639626,
               "request_no"=>"SN0010",
               "driver_name"=>"Thangam" 
            ),            
           array (
               "source_latitude" =>8.8808316,
               "source_longitude" =>77.38632202,
               "destination_latitude"=>8.95697561,
               "destination_longitude"=>77.31199265,
               "current_lat"=>8.91220689,               
               "current_long"=>77.33379364,
               "request_no"=>"SN0123",
               "driver_name"=>"Vivek"      
            )
         );
     //echo "<pre>";print_r($marks);die;
     ?>       
    
    <div id="map_canvas" style="height:400px;width:800px;"></div>
    
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCshNR138wRUbZTZHeX3Sx_ZirAh2UwyBQ&libraries=places"></script>    

<script type="text/javascript">
    var total_path=<?php echo json_encode($paths);?>;
    
    var my={directionsSVC:new google.maps.DirectionsService(),maps:{},routes:{}};
    /**
        * base-class     
        * @param points optional array array of lat+lng-values defining a route
        * @return object Route
    **/                     


    function Route(points) {
        this.origin       = null;
        this.destination  = null;
        this.waypoints    = [];
        if(points && points.length>1) { this.setPoints(points);}
        return this; 
    };

    /**
        *  draws route on a map 
        *              
        * @param map object google.maps.Map 
        * @return object Route
    **/                    
    Route.prototype.drawRoute = function(map) {
        var _this=this;
        my.directionsSVC.route(
          {"origin": this.origin,
           "destination": this.destination,
           "waypoints": this.waypoints,
           "travelMode": google.maps.DirectionsTravelMode.DRIVING
          },
          function(res,sts) {
                if(sts==google.maps.DirectionsStatus.OK){
                    if(!_this.directionsRenderer) { _this.directionsRenderer=new google.maps.DirectionsRenderer({ "draggable":true }); }
                    _this.directionsRenderer.setMap(map);
                    _this.directionsRenderer.setDirections(res);
                    google.maps.event.addListener(_this.directionsRenderer,"directions_changed", function() { _this.setPoints(); } );
                }   
          });
        return _this;
    };

    /**
    * sets map for directionsRenderer     
    * @param map object google.maps.Map
    **/             
    Route.prototype.setGMap = function(map){ this.directionsRenderer.setMap(map); };

    /**
    * sets origin, destination and waypoints for a route 
    * from a directionsResult or the points-param when passed    
    * 
    * @param points optional array array of lat+lng-values defining a route
    * @return object Route        
    **/
    Route.prototype.setPoints = function(points) {
        this.origin = null;
        this.destination = null;
        this.waypoints = [];
        if(points) {
          for(var p=0;p<points.length;++p){
            this.waypoints.push({location:new google.maps.LatLng(points[p][0], points[p][1]),stopover:false});
          }
          this.origin=this.waypoints.shift().location;
          this.destination=this.waypoints.pop().location;
        }
        else {
          var route=this.directionsRenderer.getDirections().routes[0];
          for(var l=0;l<route.legs.length;++l) {
            if(!this.origin)this.origin=route.legs[l].start_location;
            this.destination = route.legs[l].end_location;

            for(var w=0;w<route.legs[l].via_waypoints.length;++w) { this.waypoints.push({location:route.legs[l].via_waypoints[w], stopover:false});}
          }
          //the route has been modified by the user when you are here you may call now this.getPoints() and work with the result
        }
        return this;
    };

    /**
    * retrieves points for a route 
    *         
    * @return array         
    **/
    Route.prototype.getPoints = function() {
      var points=[[this.origin.lat(),this.origin.lng()]];

      for(var w=0;w<this.waypoints.length;++w) { points.push([this.waypoints[w].location.lat(), this.waypoints[w].location.lng()]);}
      points.push([this.destination.lat(), this.destination.lng()]);
      return points;
    };

    function initialize() {
      var myOptions = { zoom: 8, center: new google.maps.LatLng(-34.397, 150.644), mapTypeId: google.maps.MapTypeId.ROADMAP};
        my.maps.map1 = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        var directionsDisplay = new google.maps.DirectionsRenderer;
        directionsDisplay.suppressMarkers = true;
        directionsDisplay.setMap(my.maps.map1);

        var infowindow = new google.maps.InfoWindow();
        for(var i = 0; i < total_path.length; i++) {

		my.routes.ri = new Route([[total_path[i]['source_latitude'], total_path[i]['source_longitude']],[total_path[i]['destination_latitude'], total_path[i]['destination_longitude']]]).drawRoute(my.maps.map1);		
		var faisalabad = {lat:total_path[i]['current_lat'], lng:total_path[i]['current_long']};			
		var myMarker = new google.maps.Marker({
		map: my.maps.map1,
		animation: google.maps.Animation.DROP,
		position: faisalabad,	
    icon: 'car-placeholder.png',        
		});

    makeInfoWindowEvent(my.maps.map1, infowindow, total_path[i]['driver_name']+" , "+total_path[i]['request_no'], myMarker);

    

		//addYourLocationButton(my.maps.map1, myMarker);
		}
        

        //my.routes.r0 = new Route([[8.9705516, 77.3026459],[8.9201967, 77.3812494]]).drawRoute(my.maps.map1);
        //my.routes.r1 = new Route([[51.454513, -2.58790],[52.6308859, 1.2973550]]).drawRoute(my.maps.map1);
        //console.log(my.routes.r0);
        my.routes.rx=new Route();
        
    }

    function makeInfoWindowEvent(map, infowindow, contentString, marker) {
    google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(contentString);
    infowindow.open(map, marker);
  });
  }

    google.maps.event.addDomListener(window, "load", initialize);
</script>
