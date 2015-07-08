@extends('header.header')
<!-- This page for edit the group -->
<?php
$assets_path = "assets/inner/";
?>
@section('includes')
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui-timepicker-addon.css') }}" />    
<script src="{{ URL::to('assets/inner/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-timepicker-addon.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-sliderAccess.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/date_time_script.js') }}"></script>
<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>

<script type="text/javascript">

$(document).ready(function (e) {
$('#ct1').click(function () {
$('.rdogrp').show();
});
$('#ct2').click(function () {
$('.rdogrp1').hide();
});

$(document).on("change", "#groupimage", function () {
console.log("The text has been changed.");
var file = this.files[0];
console.log(file);
var imagefile = file.type;
var match = ["image/jpeg", "image/png", "image/jpg"];
if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
{
    $('.imgblink!').attr('src', 'noimage.png');
    return false;
}
else
{
    var reader = new FileReader();
    reader.onload = imageIsLoaded;
    reader.readAsDataURL(this.files[0]);
}
$("#txt_uploadgroupimage").html(file.name);
});
////// For tab 2 ////////////////////
jQuery('#dd_group_list').dataTable({
"bPaginate": true,
"sPaginationType": "full_numbers",
"iDisplayLength": 10,
"sPageButton": "paginate_button"
});
});

function imageIsLoaded(e) {

var image = new Image();
image.src = e.target.result;
image.onload = function () {
$(".roundedimg").attr('src', e.target.result);
}
}
;


function updateuploadcontestimg(vals)
{
$("#txt_uploadgroupimage").html(vals);
}


</script>

@stop
@section('body')
{{ Form::hidden('pagename','editgroup', array('id'=> 'pagename')) }}

<?php
if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = "creategroup";
}
?>
@if(isset($er_data))
<p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $er_data }}</p>
@endif
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
}

$groupdata = groupModel::where('ID', $groupid)->get();
?>

<div id="con_grp" class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" checked />
    <label for="tab1"><span id="submnu_editgroup">Edit Group</span></label>

	<div class="con_cat_right">
	<a onclick="goback()" ><label for="tab6" id="txt_editprofile">Back</label></a>
	</div>
	
    <div class="mbblk">
        <select class="radius sel_lang">
            <option>Edit Group</option>

        </select>
    </div>

    <div class="tab-body-wrapper">

        <div id="tab-body-1" class="tab-body">
            <div id="p">

                <div class="clrscr"></div>

                <form id="editprofile" name="editgroup/<?php echo $groupid; ?>" enctype="multipart/form-data" action="<?php echo url(); ?>/editgroup/<?php echo $groupid; ?>" method="post" class="form_mid">
                    <div class="loginform loginbox grp_ctr"><!-- mar1-->

                        <legend class="radius"><div class="leg_head"><span id="txt_groupinfo" class="txt_groupinfo">Group Info</span></div>
                            @if(isset($er_data['message']))
                            <p class="alert" style="color:red;">{{ $er_data['message'] }}</p>
                            @endif
                            <p>
                            <div class="inp_pfix"><img src="{{ URL::to('/assets/inner/img/bell_icons.png') }}" width="25" height="25"></div>
                            <input type="text" id="txt_groupname" name="groupname" placeholder="Group Name" value="{{ $groupdata[0]['groupname'] }}" class="radius pfix_mar" />
                            </p>
                            @if(isset($er_data['groupname']))
                            <p class="alert" style="color:red;">{{ $er_data['groupname'] }}</p>
                            @endif

                            <div class="rdogrp">
                                <label><strong><span id="txt_grouptype" class="txt_grouptype">Group Type</span></strong></label>
                                <div class="mb_brk"></div>
                                <input type="radio" id="ct1" name="grouptype" value="private" <?php if (Auth::user()->ID != 1) {
    echo "disabled";
} ?>  <?php echo ($groupdata[0]['grouptype'] == "private") ? "checked" : ""; ?>  >
                                <label for="ct1"><span id="txt_private" class="txt_private">Private<span></label>
                                            <input type="radio" id="ct2" name="grouptype"value="open" <?php if (Auth::user()->ID != 1) {
    echo "disabled";
} ?> <?php echo ($groupdata[0]['grouptype'] == "open") ? "checked" : ""; ?> >
                                            <label for="ct2"><span id="txt_open" class="txt_open">Open<span></label>
                                                        </div>

                                                        <div class="clrscr"></div>            
                                                        <p>


                                                        <div class="img_pfix mb_upimg mbmt"><img src="{{ ($groupdata[0]['groupimage']!='')?(URL::to('public/assets/upload/group/'.$groupdata[0]['groupimage'])):(URL::to('assets/inner/img/default_groupimage.png')) }}" width="45" height="45" class="roundedimg"></div>
                                                          <!--<input type="file" name="file" value="Upload New Image" class="inp_file" />-->
                                                        <label class="myLabel">
                                                            <input name="groupimage" type="file"  id="groupimage" onchange="updateuploadcontestimg(this.value)"  />
                                                            <span id="txt_uploadgroupimage">Upload Group Image</span>
                                                        </label>
                                                        </p>
                                                        <div class="clrscr"></div>
                                                        <p>   
                                                        <div class="rdogrp rdogrp1" <?php if ($groupdata[0]['grouptype'] == "open") { ?>style='display:none;' <?php } ?>>

                                                        </div>
                                                        </p>                 
                                                        </legend>


                                                        </div>

                                                        <div class="clrscr"></div>

                                                        <div class="loginbox">
                                                            <p><center>
                                                                <button class="radius martop_10" name="client_login"><span class="txt_updategrp">Update Group</span></button>
                                                            </center></p> 
                                                        </div>
                                                        </form>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        @stop