@extends('layouts.frontend')

@section('title', 'My Account')

@section('content')
	<div class="container">
	@include('includes.frontend.request_messages')
    @include('includes.frontend.validation_errors')
		<div class="row">
			{{Form::open(array('method' => 'get','class'=>''))}}	
	        	<div class="form-group pull-right" style="width:200px;">
            		<div class="input-group">
            			<input type="text" class="form-control" name="search" id="search" value="{{Request::get('search')}}" />
            			<span class="input-group-btn">
            				<button type="submit" class="btn btn-default">Search</button>
            			</span>
            		</div>
	            </div>
	        {{Form::close()}}
			<div class="col-lg-12">
				@if($isRequestSearch)
					<div class="pull-right">
						<a href="{{route('frontend-message')}}">Reset Search </a>	
					</div>
					<br>
				@endif
	        	<div class="panel panel-default">
	                <div class="panel-heading">
	                    Conversation List
	                </div>
	                <div class="panel-body">
	                    <div class="table-responsive">
	                        <table class="table table-striped table-hover">
	                            <thead>
	                                <tr>
	                                    <th>Photo</th>
	                                    <th>Name </th>
	                                    <th>Action</th>
	                                </tr>
	                            </thead>
	                            <tbody>
	                            @if(count($myConversations) > 0)
									@foreach($myConversations as $myConversation)
	                                    <tr>
	                                        <td><img src="{{route('account-avatar-100x100', ['id'=>$myConversation->getConversationId()])}}" alt="..." style="width:40px; height:40px;"></td>
	                                        <td title="{{$myConversation->getConversationInfo()}}">{{$myConversation->getConversationTitle()}}@if(isset($myConversation->messagecount) && $myConversation->messagecount > 0) <span class="badge" style="background:red; font-weight:bold;">{{$myConversation->messagecount}}</span> @endif</td>
	                                        <td><a href="{{route('frontend-conversation', array_merge(['conversation_ref'=>$myConversation->conversation_ref],Request::all()))}}" class="btn btn-primary" role="button">CHAT</a></td>
	                                    </tr>
	                                @endforeach
	                            @else
	                            <tr>
									<td colspan="3" class="text-center">No record(s) found.</td>
								</tr>
	                            @endif  
	                            	@if(count($myConversations) > 0)
									<tr>
										<td colspan=3 class="text-center">
										{{$myConversations->appends(['Usertype'=>Request::get('Usertype'),'sortBy'=>Request::get('sortBy'),'sortOrder'=>Request::get('sortOrder')])->render()}}
										</td>
									</tr>
									@endif
	                            </tbody>
	                        </table>
	                    </div>
	                </div>
	            </div>
	        </div>
		</div>
	</div>
@endsection
<!-- @push('footer')
<script type="text/javascript">
	$(document).on('click', '#btn-chat', function() {
			var message = $('#message').val();
			var receiverId=31;
			$.ajax({
                dataType:'json',
                type:'post',
                url:  "{{route('api-messages-newmessage')}}",
                data: {'message' : message ,'receiverId' : receiverId, 'type':1 ,'_token': "{{csrf_token()}}"},
                success: function(data){
                    $('#message').val('');

                    
                }
            });
        });
</script>
@endpush -->