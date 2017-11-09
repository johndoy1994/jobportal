<!-- <div class="social-buttons text-center col-md-12 text-center">

    <div class="col-md-4 col-md-offset-4">
      <div style="font-size:medium;font-weight:bold"> Share this job on : </div>
     
    </div>
</div> -->
<?php $segment1 = Request::segment(1); 
if($segment1=='recruiters'){
  $userType='recruiter';
}else{
$userType='web';
}
 $isloginuser = \App\MyAuth::user($userType); 
?>
  @if($segment1=='recruiters')
      @if($isloginuser)
        <a class="btn btn-primary btn-sm" role="button" href="{{route('get-recruiter-import-contact',array_merge(['url'=>$url]))}}">sharing with my contacts</a>
      @endif
  @else
      @if($isloginuser)
        <a class="btn btn-primary btn-sm" role="button" href="{{route('get-jobseeker-import-contact',array_merge(['url'=>$url]))}}">sharing with my contacts</a>
      @endif  
  @endif
  </a>
<div class="social-buttons">
   <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
           target="_blank">
           <i class="fa fa-facebook-square fa-3x"></i>
  </a>
  <a href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}"
     target="_blank">
      <i class="fa fa-twitter-square fa-3x"></i>
  </a>
  <a href="https://plus.google.com/share?url={{ urlencode($url) }}"
     target="_blank">
     <i class="fa fa-google-plus-square fa-3x"></i>
  </a>
  <a href="https://pinterest.com/pin/create/button/?url={{urlencode($url)}}" target="_blank">
      <i class="fa fa-pinterest-square fa-3x"></i>
  </a>
</div>
