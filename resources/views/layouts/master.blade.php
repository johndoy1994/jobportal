<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>@yield('master-title')</title>

		@stack('head')
		@stack('after-head')
	</head>
  	<body>
  		@yield('master-content')
  		@stack('footer')
  		@stack('after-footer')
  		<script>
  		function autochat(ul, type, ref) {
  			$.ajax({
  				type: "post",
  				url: "{{route('api-secure-autochat')}}",
  				data: { type: type, ref: ref,_token: "{{csrf_token()}}" },
  				success: function(html) {
  					$(ul).append(html);
            if(html.length > 0) {
              $(".chat-panel .panel-body").scrollTop($(".chat-panel .panel-body").prop('scrollHeight'));
            }
  				},
  				complete: function() {
  					setTimeout(function() {
  						autochat(ul, type, ref);
  					}, 1000);
  				}
  			});
  		}
  		$(document).ready(function() {
  			var chatUl = $(".chat[role='autochat']");
  			if(chatUl.length) {
  				var type = $(chatUl).attr('data-type');
  				var ref = $(chatUl).attr('data-ref');
  				autochat(chatUl, type, ref);
  			}
  		});
  		</script> 
	</body>
</html>