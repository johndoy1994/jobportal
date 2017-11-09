@push('head')
	<link rel="stylesheet" href="{{asset('css/footer.css')}}"></link>
@endpush
<div class="clearfix"></div><br>
<!--footer-->
<footer class="footer1 well">
	<div class="container">
		<div class="row"><!-- row -->
		    <div class="col-lg-3 col-md-3"><!-- widgets column left -->
		        <ul class="list-unstyled clear-margins"><!-- widgets -->
		        	<li class="widget-container widget_nav_menu"><!-- widgets list -->
		                <h1 class="title-widget">{{env('PROJECT_TITLE')}}</h1>
		                <ul>
		                	@if(!empty($cmsFooterLinks))
		                    	@foreach($cmsFooterLinks as $key=>$link)
		                    		<li><a  href="{{route('api-public-view-footer-links',['pageId'=>$link->id])}}"><i class="fa fa-angle-double-right"></i> {{$link->page_title}}</a></li>
		                    	@endforeach
		                    @endif
		                </ul>
					</li>
		        </ul>     
		    </div><!-- widgets column left end -->

		    <div class="col-lg-3 col-md-3"><!-- widgets column left -->
			    <ul class="list-unstyled clear-margins"><!-- widgets -->
		        	<li class="widget-container widget_nav_menu"><!-- widgets list -->
		                <h1 class="title-widget">Jobseeker</h1>
		                <ul>
							<li><a  href="{{route('account-signin')}}"><i class="fa fa-angle-double-right"></i>  Jobseeker Login</a></li>
		                    <li><a  href="#"><i class="fa fa-angle-double-right"></i>  Link1</a></li>
		                    <li><a  href="#"><i class="fa fa-angle-double-right"></i>  Link2</a></li>
		                </ul>
					</li>
			    </ul>
		    </div><!-- widgets column left end -->
		                
		    <div class="col-lg-3 col-md-3"><!-- widgets column left -->
			    <ul class="list-unstyled clear-margins"><!-- widgets -->
			    	<li class="widget-container widget_nav_menu"><!-- widgets list -->
			            <h1 class="title-widget">Recruiter</h1>
			            <ul>
							<li><a href="{{route('recruiter-account-signin')}}"><i class="fa fa-angle-double-right"></i> Recruiter Login</a></li>
							<li><a  href="#"><i class="fa fa-angle-double-right"></i>  Link1</a></li>
		                    <li><a  href="#"><i class="fa fa-angle-double-right"></i>  Link2</a></li>
			            </ul>
					</li>
		        </ul>
		    </div><!-- widgets column left end -->
		                
		                
		    <div class="col-lg-3 col-md-3"><!-- widgets column center -->
			    <ul class="list-unstyled clear-margins"><!-- widgets -->
			    	<li class="widget-container widget_recent_news"><!-- widgets list -->
			            <h1 class="title-widget">Share Us on </h1>
			            <!--<div class="footerp">
				            <h2 class="title-median">Webenlance Pvt. Ltd.</h2>
				            <p><b>Email id:</b> <a href="mailto:info@webenlance.com">info@webenlance.com</a></p>
				            <p><b>Helpline Numbers </b>
							<b style="color:#ffc106;">(8AM to 10PM):</b>  +91-8130890090, +91-8130190010  </p>
							<p><b>Corp Office / Postal Address</b></p>
							<p><b>Phone Numbers : </b>7042827160, </p>
							<p> 011-27568832, 9868387223</p>
			            </div> -->
			            <div class="social-icons">
			            	<ul class="nomargin">
								@include('components.share', [
								    'url' => Request::fullUrl()
								])
			                </ul>
			            </div>
					</li>
			    </ul>
		    </div>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
					<div class="copyright">
						© {{date('Y')}}, {{env('PROJECT_TITLE')}}, All rights reserved
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
					<div class="design">
						 Web Design & Development by <a target="_blank" href="https://www.webplanex.com/">Webplanex Infotech Pvt. Ltd.</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>
<!--header-->

