@extends('header.header')
@section('body')
<script>
function contestlistname(type)
{ 
$('#contestlisttype').val(type);
$('#subtitle').show();
}

function contesttype(contesttype){ 
$('#contestname').val(contesttype);

var form = document.getElementById('contest_list_details');
$.ajax({
			url : "showcontestlistdetails",
			type: "POST",
			data: new FormData(form),
			processData: false,
			contentType: false,
			async: false,
			success : function(data){ console.log(data); } 
		});
}

</script>
<style>
#subtitle
{
display:none;
}
</style>

<script>


$(document).ready(function() {

$('.contestname').click(function(e) {

alert($(this).val());

});
});
</script>

<div class="form-panel">
        <div class="header-panel">
        <h2></h2>
        </div>
        <div class="dash-content-panel"> <!-- dash panel start -->
        
        <div class="dash-content-row " > <!-- dash content row start -->
        <div class="dash-content-head tabContaier">
        <h5>Contest List</h5>
		</div>

{{ Form::open(array('url' => 'showcontestlistdetails', 'enctype' => 'multipart/form-data', 'class' => 'form-signin','id' =>'contest_list_details'))  }}	
<ul>
<li><a href="#" onclick='return contestlistname("current");'>Current</a></li>
<li><a href="#" onclick='return contestlistname("upcoming");'>Upcoming</a></li>
<li><a href="#" onclick='return contestlistname("archive");'>Archive</a></li>
<li><a href="#" onclick='return contestlistname("private");'>Private</a></li>
</ul>

<ul id="subtitle">
<li ><a href="#" onclick='return contesttype("photo");' >Photo</a></li>
<li ><a href="#" onclick='return contesttype("video");' >Video</a></li>
<li ><a href="#" onclick='return contesttype("topic");' >Topic</a></li>
</ul>

{{ Form::hidden('contestlisttype',"null",array('id'=> 'contestlisttype')) }}
{{ Form::hidden('contestname','null',array('id'=>'contestname')) }} 
		
{{ Form::close(); }}

		
</div>
</div>
</div>

@stop