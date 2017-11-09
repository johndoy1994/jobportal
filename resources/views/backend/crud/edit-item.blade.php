@extends('layouts.backend')

@section('title', 'Edit Item - CRUD Listing')

@section('content')
	<div id="page-wrapper">
		<br/>
		<div class="row">
			<div class="col-md-12">
				<a href="{{route('admin-crud')}}" class="btn btn-primary">Back</a>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-12">
				<form class="well form-horizontal" method="post" action="{{route('admin-crud-edit-item-post', ['item'=>$item->id])}}">
					<legend>Edit Item</legend>
					@if(session('success_message'))
						<div class="alert alert-success">
							{{session('success_message')}}
						</div>
					@endif

					@if(session('error_message'))
						<div class="alert alert-danger">
							{{session('error_message')}}
						</div>
					@endif

					@if(count($errors)>0)
						<div class="alert alert-warning">
							@foreach($errors->all() as $error)
								<li>{{$error}}</li>
							@endforeach
						</div>
					@endif
					<fieldset>
						<div class="form-group">
							<label class="control-label col-lg-3">Item Name :</label>
							<div class="col-lg-9">
								<input type="text" name="name" class="form-control" value="{{$item->name}}" />
							</div>
						</div>	
						<div class="form-group">
							<label class="control-label col-lg-3">Item Price :</label>
							<div class="col-lg-9">
								<input type="text" name="price" class="form-control" value="{{$item->price}}" />
							</div>
						</div>	
					</fieldset>
					{{csrf_field()}}
					<div class="form-group">
						<div class="col-lg-9 col-lg-offset-3">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection