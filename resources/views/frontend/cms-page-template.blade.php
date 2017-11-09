@extends('layouts.frontend')

@section('title', 'Job Search')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@include('includes.frontend.request_messages')
				@include('includes.frontend.validation_errors')
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h3>{{$cmsPageData->page_title}}</h3>
					<?php 
						preg_match_all('/<img[^>]+>/i',$cmsPageData->page_content, $result); 
						foreach ($result[0] as $key => $value) {
							$newValue = str_replace(">", " class=img-responsive >", $value);
							$cmsPageData->page_content = str_replace($value, $newValue, $cmsPageData->page_content);
						}
						echo $cmsPageData->page_content;
					?>
			</div>
		</div>
	</div>
@endsection