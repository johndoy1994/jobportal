@extends('layouts.backend')

@section('title', 'Your G-Mail Contacts')

@section('content')
	<div id="page-wrapper">
		<div class="row padding-top-10">
			<div class="col-md-12">
				@if(count($googleContacts) == 0)
					No contacts are there, please try again.
				@else 
					<form class="form-horizontal" method="post" action="{{route('admin-gmail-contacts-post')}}">
						<div class="form-group">
							<label class="control-label col-lg-2">Contacts : </label>
							<div class="col-lg-10">
								<select id="emails" required="" name="emails[]" multiple class="form-control">
									@foreach($googleContacts as $googleContact)
										<option value="{{$googleContact['email']}}" selected>{{$googleContact['name']}} ({{$googleContact['email']}})</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">Subject : </label>
							<div class="col-lg-10">
								<input class="form-control" required="" type="text" name="subject" placeholder="Subject..." />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">Message : </label>
							<div class="col-lg-10">
								<textarea class="form-control" required="" placeholder="Message" name="message"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2"></label>
							<div class="col-lg-10">
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