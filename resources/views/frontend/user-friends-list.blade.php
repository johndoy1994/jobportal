@extends('layouts.frontend')

@section('title', 'Contact Lists')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.request_messages')
				@include('includes.frontend.validation_errors')
			</div>
			<div class="col-md-12">
				<div class="well">
					<legend>Search Job</legend>
				</div>
				<div class="">
					{{$token}}
					{{$user}}
					<!-- <input type="hidden" id="fb-friend-list" value="{{$user->verification->token}}"> -->
					<div id="fb-friends"></div>
	                </div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('footer')
<script>  
	$(document).ready(function(){
		//var userId = $("#fb-friend-list").val();
		//userId = userId.split("_");
		$.getJSON('https://graph.facebook.com/eris.risyana/friends?limit=100&access_token={{$token}}', function(mydata) {
		var output="<ul>";
		for (var i in mydata.data) {
		    output+="<li>NAMA : " + mydata.data[i].name + "<br/>ID : " + mydata.data[i].id + "</li>";
		}

		output+="</ul>";
		document.getElementById("fb-friends").innerHTML=output;   });
	});
    </script> 
@endpush