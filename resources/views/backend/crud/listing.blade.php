@extends('layouts.backend')

@section('title', 'CRUD Listing')

@section('content')
	<div id="page-wrapper">
		<br/>
		<div class="row">
			<div class="col-md-12">
				<a href="{{route('admin-crud-new-item')}}" class="btn btn-primary">New Item</a>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-md-12">
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
			</div>
			<div class="col-md-12">
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th>#</th>
							<th>Item name</th>
							<th>Item Price</th>
							<th>Action</th>
						</tr>
					</thead>

					<tbody>
						@foreach($items as $item)
							<tr>
								<td>{{$item->id}}</td>
								<td>{{$item->name}}</td>
								<td>Rs. {{$item->price}}</td>
								<td>
									<a href="{{route('admin-crud-edit-item', ['item'=>$item->id])}}">Edit</a>
									<a href="{{route('admin-crud-delete-item', ['item'=>$item->id])}}"  onclick="return confirm('Are you sure to delete this Job Type ?')">Delete</a>
								</td>
							</tr>
						@endforeach
					</tbody>

					<tbody>
						<tr>
							<td colspan=4>
							{{$items->render()}}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection