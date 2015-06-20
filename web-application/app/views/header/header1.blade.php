<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Ding Datt</title>
{{ HTML::script('public/assets/js/jquery-1.11.1.min.js') }}
{{ HTML::script('public/assets/js/jquery.dataTables.min.js') }}

{{ HTML::style('public/assets/css/jquery.dataTables.css') }}
{{ HTML::style('public/assets/css/style.css') }}

<!--{{ HTML::script('public/assets/js/jquery-ui.js') }}
{{ HTML::style('public/assets/css/jquery-ui.css') }}-->

{{ HTML::script('public/js/jquery.datetimepicker.js') }}
{{ HTML::style('public/css/jquery.datetimepicker.css') }}

{{ HTML::style('public/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('public/bootstrap/css/signin.css') }}
<!--

  
  
-->  

<script src="{{ URL::to('assets/inner/js/jquery.sumoselect.js') }}"></script>
    <link href="{{ URL::to('assets/inner/css/sumoselect.css') }}" rel="stylesheet" />

    <script type="text/javascript">
        $(document).ready(function () {
            window.asd = $('.SlectBox').SumoSelect({ csvDispCount: 3 });
            window.test = $('.testsel').SumoSelect({okCancelInMulti:true });
        });
    </script>
 <!-- <link rel="stylesheet" type="text/css" media="all" href="{{ URL::to('assets/inner/css/styles.css') }}">-->
  
<script type="text/javascript" >

$(document).ready(function() {
		
		if($('#sessionlanguage').val()=='')
		changelanguage('value_en');
		else
		changelanguage($('#sessionlanguage').val());

		$( "#languageid" ).change(function(e) {
		var languagename = $(this).val(); 
		e.preventDefault();
		changelanguage('value_'+languagename);	      
		});   
});

function changelanguage(languagename)
{ 				 
		var page_name = $('#pagename').val();  //console.log(page_name);  console.log(languagename);           
		var dataString = 'languagename='+languagename+'&page_name='+page_name; //console.log(dataString);
		$.ajax({
			type: "POST",
			url : "changeLanguage",
			data : dataString,			
			success : function(data){   //console.log(data);
			for(var i=0; i<data.length;i++)
			{ 
			if(data[i]['ctrlCaptionId']=='txt_submit' || data[i]['ctrlCaptionId']=='txt_delete_record' ||data[i]['ctrlCaptionId']=='txt_ok'||data[i]['ctrlCaptionId']=='txt_cancel'|| data[i]['ctrlCaptionId'] =='txt_save')
			$('#'+data[i]['ctrlCaptionId']).val(data[i][languagename]);
			else
			 $('#'+data[i]['ctrlCaptionId']).html(data[i][languagename]);
			}               
			}
		}); 
}

</script>

</head>

<?php
  $languageDetails = languagenameModel::lists('language_name','language_key');
  ?>
   {{ Form::hidden('sessionlanguage',Session::get('language'), array('id'=> 'sessionlanguage')) }}
  

 
           Language Selector 
		  <?php if(!empty($languageDetails)){	?>	   
		  {{ Form::select('language', array(''=>'Select Language')+$languageDetails,null, array('id'=> 'languageid'))}}
		  <?php } ?>
		  <?php /*
<header class="header" id="header"> 
		  <!--{{ Form::select('language', array(''=>'Select Language','english'=>'english','french'=>'french'),null, array('id'=> 'languageid'))}}-->
        
        <?php if(Auth::guest()){ ?>
		
		<ul><li><a href="{{ URL::to(''); }}" >Login</a></li></ul>
		
		
		<?php } else { ?>
		
		<ul id="menu-bar">

       <!-- <li><a href="#" ><span id="txt_menu_profile"></span></a> <ul >
		<?php if(Auth::user()->ID==1){  ?><li><a href="{{ URL::to('userdetails'); }}"><span id="txt_menu_userdetails" class="icon pro-icon"></span></a></li> <?php } ?>
        <li><a href="{{ URL::to('profileupdate/'.Auth::user()->ID);  }}"><span class="icon pro-icon" id="txt_menu_profile_update"></span></a></li>
        <li><a href="{{ URL::to('logout'); }}"><span class="icon logout-icon" id="txt_menu_logout"></span></a>
        </ul></li>-->
		<li><a href="#" >profile</a> <ul >
		<?php if(Auth::user()->ID==1){  ?><li><a href="{{ URL::to('userdetails'); }}">User Details</a></li> <?php } ?>
        <li><a href="{{ URL::to('profileupdate/'.Auth::user()->ID);  }}">Profile Update</a></li>
        <li><a href="{{ URL::to('logout'); }}">Logout</a>
        </ul></li>
		
		
		<li><a href="{{ URL::to('contest'); }}" ><span id="txt_menu_createcontest">Create Contest</span></a></li>
		<li><a href="{{ URL::to('contestlist'); }}" ><span id="txt_menu_contestlist">Contest List</span></a></li>
</ul> 
		
		
		<?php } ?>

       <div >
	   {{ Form::hidden('sessionlanguage',Session::get('language'), array('id'=> 'sessionlanguage')) }}
       
        </div>
 </header>  */ ?>
 @yield('body')
 </html>