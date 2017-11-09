<div class="modal" tabindex="-1" role="dialog" id="applicationModal">
	<div class="modal-dialog">
		<div class="modal-content loader">
			<div class="modal-body text-center">
				<p><img src="{{asset('imgs/loader.gif')}}" /><br/><br/><label>Loading Details</label></p>
			</div>
		</div>
		<div class="modal-content job-application hide"></div>
	</div>
</div>

@push('after-footer')
<script>
$(document).ready(function() {
	$("a[role='show-job-application']").click(function() {
		var modal = "#applicationModal";
		var $appId = $(this).attr('data-application');
		$(modal).find('.loader').removeClass('hide');
		$(modal).find('.job-application').addClass('hide');
		$(modal).modal('show');
		$.ajax({
			url: "{{route('api-secure-showjobapplication')}}",
			type:"POST",
			data: { jobApp: $appId, _token: "{{csrf_token()}}" },
			success: function(html) {
				$(modal).find('.job-application').removeClass('hide');
				$(modal).find('.loader').addClass('hide');
				$(modal).find(".job-application").html(html);
			},
			error: function() {
				$("#applicationModal").modal('hide');
				alert("Error while trying to retry application details, please try again");
			}
		});
	});
});
</script>
@endpush