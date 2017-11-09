<div class="row">
	<div class="col-md-12">
		<div id="map" style="width:100%; height: 500px; text-align:center">
			<img src="{{asset('imgs/loader.gif')}}" />
		</div>
	</div>
	<div class="col-md-12">
		<br/>
		<legend>Key :</legend>
		<div class="col-md-3">
			<img src="{{asset('imgs/lp-exact-single.png')}}" class="pull-left" />
			<p class="pull-right">
				Single job
				exact location
				(e.g postcode)
			</p>
		</div>
		<div class="col-md-3">
			<img src="{{asset('imgs/lp-exact-multiple.png')}}" class="pull-left" />
			<p class="pull-right">
				Multiple jobs
				exact location
				(e.g postcode)
			</p>
		</div>
		<div class="col-md-3">
			<img src="{{asset('imgs/lp-approx-single.png')}}" class="pull-left" />
			<p class="pull-right">
				Single job
				approximate location
				(e.g town or city)
			</p>
		</div>
		<div class="col-md-3">
			<img src="{{asset('imgs/lp-approx-multiple.png')}}" class="pull-left" />
			<p class="pull-right">
				Multiple jobs
				approximate location
				(e.g town or city)
			</p>
		</div>
	</div>
</div>

@push('footer')
<script>
var map;
var markers = [];
var bounds;
var minZoomLevel = 14;

function initMap() {
	bounds = new google.maps.LatLngBounds();
    map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: {{$location_point[0]}}, lng: {{$location_point[1]}}},
    	zoom: {{$location_point[2]}}
    });
    
    @foreach($location_markers["exacts"] as $marker) 
    	<?php
    	$multiple = count($marker) > 1;
    	$markerTitle = $multiple ? count($marker)." jobs" : $marker[0]["job"]["title"] ; 
    	$point = $marker[0]["point"];
    	$uniq = uniqid();
    	$jsMarker = "marker".$uniq;
    	?>
	    var {{$jsMarker}} = new google.maps.Marker({
			position: { lat: {{$point[0]}}, lng: {{$point[1]}} },
			map: map,
			title: "{{$markerTitle}}",
			@if($multiple)
				icon: "{{asset('imgs/lp-exact-multiple.png')}}",
			@else
				icon: "{{asset('imgs/lp-exact-single.png')}}",
			@endif
		});
		bounds.extend({{$jsMarker}}.getPosition());
		var infoWindow_{{$jsMarker}} = new google.maps.InfoWindow({
		    content: "Loading...",
		     maxWidth: 200

		});
		{{$jsMarker}}.addListener('click', function(a) {
			infoWindow_{{$jsMarker}}.open(map, {{$jsMarker}});
			$.ajax({
				url : "{{route('api-public-jobs')}}",
				data: {
					'marker_ids' : [ @foreach($marker as $_marker) @if(isset($_marker["job"])) {{$_marker["job"]["id"]}}, @endif @endforeach ],
					@foreach($route_params as $key=>$value)
						'{{$key}}' : "{{$value}}",
					@endforeach
				},
				success: function(html) {
					infoWindow_{{$jsMarker}}.setContent(html);
				}
			});
		});
		map.panTo({{$jsMarker}}.position);
		//markers.push({{$jsMarker}});
    @endforeach

    @foreach($location_markers["approx"] as $marker) 
    	<?php
    	$multiple = count($marker) > 1;
    	$markerTitle = $multiple ? count($marker)." jobs" : $marker[0]["job"]["title"] ; 
    	$point = $marker[0]["point"];
    	$uniq = uniqid();
    	$jsMarker = "marker".$uniq;
    	?>
	    var {{$jsMarker}} = new google.maps.Marker({
			position: { lat: {{$point[0]}}, lng: {{$point[1]}} },
			map: map,
			title: "{{$markerTitle}}",
			@if($multiple)
				icon: "{{asset('imgs/lp-approx-multiple.png')}}",
			@else
				icon: "{{asset('imgs/lp-approx-single.png')}}",
			@endif
		});
		bounds.extend({{$jsMarker}}.getPosition());
		var infoWindow_{{$jsMarker}} = new google.maps.InfoWindow({
		    content: "Loading..."
		});

		{{$jsMarker}}.addListener('click', function(a) {
			infoWindow_{{$jsMarker}}.open(map, {{$jsMarker}});
			//autoset postion created by sagar start//
			infoWindow_{{$jsMarker}}.setPosition();
			//autoset postion created by sagar end//
			$.ajax({
				url : "{{route('api-public-jobs')}}",
				data: {
					'marker_ids' : [ @foreach($marker as $_marker) @if(isset($_marker["job"])) {{$_marker["job"]["id"]}}, @endif @endforeach ],
					@foreach($route_params as $key=>$value)
						'{{$key}}' : "{{$value}}",
					@endforeach
				},
				success: function(html) {
					infoWindow_{{$jsMarker}}.setContent(html);
				}
			});
		});
		map.panTo({{$jsMarker}}.position);
		//markers.push({{$jsMarker}});
    @endforeach

    map.fitBounds(bounds);

    google.maps.event.addListener(map, 'center_changed', function() {
		//rearrangeMapCenter();
	});

	google.maps.event.addListener(map, 'zoom_changed', function() {
    	
    });

}

function rearrangeMapCenter() {
	if (bounds.contains(map.getCenter())) return;

	// We're out of bounds - Move the map back within the bounds

	var c = map.getCenter(),
	x = c.lng(),
	y = c.lat(),
	maxX = bounds.getNorthEast().lng(),
	maxY = bounds.getNorthEast().lat(),
	minX = bounds.getSouthWest().lng(),
	minY = bounds.getSouthWest().lat();

	if (x < minX) x = minX;
	if (x > maxX) x = maxX;
	if (y < minY) y = minY;
	if (y > maxY) y = maxY;

	map.setCenter(new google.maps.LatLng(y, x));
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNmXGiokPzXN1lSHDSzB7qyN7BMvgUNYQ&callback=initMap&libraries=geometry" async defer></script>
@endpush