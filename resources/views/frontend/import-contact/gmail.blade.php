@extends('layouts.frontend')

@section('title', 'Your G-Mail Contacts')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-12">
				@if(count($googleContacts) == 0)
					No contacts are there, please try again.
				@else
					<form class="form-horizontal" method="post" action="{{route('jobseeker-gmail-contacts-post')}}">
						<div class="form-group">
							<label class="control-label col-lg-3">Contacts : </label>
							<div class="col-lg-9">
								<select id="emails" name="emails[]" required="" multiple class="form-control">
									@foreach($googleContacts as $googleContact)
										<option value="{{$googleContact['email']}}" selected>{{$googleContact['name']}} ({{$googleContact['email']}})</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Subject : </label>
							<div class="col-lg-9">
								<input class="form-control" required="" type="text" name="subject" placeholder="Subject..." />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3">Message : </label>
							<div class="col-lg-9">
								<textarea class="form-control" required="" placeholder="Message" name="message"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-3"></label>
							<div class="col-lg-9">
								<button type="submit" class="btn btn-primary">Send Mail</button>
							</div>
						</div>
						{{csrf_field()}}
					</form>
				@endif
			</div>
		</div>
	</div>
@endsection

@push('footer')
<script>
	$("#emails").select2({
		tags: ','
	});
</script>
@endpush
