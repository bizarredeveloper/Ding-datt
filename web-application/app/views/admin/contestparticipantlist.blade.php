@extends('header.header')
<!-- This page for view the participant details from particular contest -->
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


<script src="{{ URL::to('assets/inner/js/jquery.sumoselect.js') }}"></script>
<link href="{{ URL::to('assets/inner/css/sumoselect.css') }}" rel="stylesheet" />
<script type="text/javascript">
$(document).ready(function () {
    window.asd = $('.SlectBox').SumoSelect({csvDispCount: 3});
    window.test = $('.testsel').SumoSelect({okCancelInMulti: true});
});
</script>
<!-- multi select -->      
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready(
        function () {
            $("#datepicker").datepicker({
                changeMonth: true, //this option for allowing user to select month
                changeYear: true, //this option for allowing user to select from year range
                dateFormat: 'yy-mm-dd'
            });
        }
);


$(document).ready(function (e) {


////// For tab 2 ////////////////////
    jQuery('#dd_group_list').dataTable({
        "bPaginate": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 10,
        "sPageButton": "paginate_button",
        "bFilter": false
    });

});

function showtab() {
    var main_tab = $('#mobileselected').val();
    window.location = "<?php echo url(); ?>/groupresponsive?tabname=" + main_tab;
}

/////////// User events ///////////
function changeactive(contestid) {
    var dataString = "contestid=" + contestid;
    $.ajax({
        type: "get",
        url: '../activecontest',
        data: dataString,
        success: function (data) {
            console.log(data);
            if (data == 1) {

                $('.backclr_' + contestid).css('background-color', 'green');
                $('#ajaxmessage').html('Activated successfully');

            } else {
                $('.backclr_' + contestid).css('background-color', 'red');
                $('#ajaxmessage').html('Deactivated successfully');
            }
        }
    });
}

function contestparticipantdelete(contestparticipantid, contest_id) {


    var answer = confirm("Are you sure remove this participant?");
    if (answer) {
        //alert(contestparticipantid);
        window.location = "<?php echo url(); ?>/removecontestparticipant?contestparticipantid=" + contestparticipantid + "&contest_id=" + contest_id;
    }
}
</script>	

@stop
@section('body')
{{ Form::hidden('pagename','user', array('id'=> 'pagename')) }}

<?php
if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = "userlist";
}


if (Session::has('contest_id')) {
    $contest_id = Session::get('contest_id');
}
?>
@if(isset($er_data))
<p class="alert" style="color:green;padding:5px;text-align:center;font-size:13px">{{ $er_data }}</p>
@endif
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
    //print_r($er_data);
}
if (Session::has('searcheduser')) {

    $searcheduser = Session::get('searcheduser');
} else {
    $searcheduser = '';
}
?>

<div id="con_grp" class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" <?php
    if ($tab == "userlist") {
        echo "checked";
    }
    ?> />

    <a href="{{ URL::to('managecontest') }}">
        <div id="subtab_div" class="con_cat_right mbnone" >
            <button class="bck_btn" >&laquo; <span class="txt_back" > Back</span> </button>
        </div>
    </a> 

    <label for="tab1">Participant List</span></label>

    <div class="mbblk">
        <select class="radius sel_lang" onchange="showtab()" id="mobileselected">
            <option value="createuser" <?php
    if ($tab == "createuser") {
        echo "selected";
    }
    ?>>Create Group</option>                
        </select>
    </div>

    <div class="tab-body-wrapper">

        <!-- Contest participant List -->
        <div id="tab-body-1" class="tab-body">
            @if(isset($er_data['message']))
            <p class="alert" style="color:green; font-size:13px;">{{ $er_data['message'] }}</p>
            @endif
            <span class="alert" id="ajaxmessage" style="color:green;"></span>

            <h1><?php
                if ($contest_id != '')
                    $contestname = contestModel::select('contest_name', 'contesttype')->where('ID', $contest_id)->first();

                echo $contestname->contest_name;
                ?></h1>

            <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Participant name</th>			
                        <th><?php
                if ($contestname->contesttype == 'p')
                    echo "Photo";
                elseif ($contestname->contesttype == 'v')
                    echo "Video";
                else
                    echo "Topic";
                ?></th>           
                        <th class="tr_wid_button1" align="center"><span class="txt_view">View</span></th>            
                        <th class="tr_wid_edit"><span class="txt_delete">Delete<span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($contest_id != '') {


                                            $participantlist = contestparticipantModel::select('contestparticipant.ID', 'user.firstname', 'user.lastname', 'user.username', 'user.profilepicture', 'contestparticipant.dropbox_path', 'contestparticipant.uploadfile', 'contestparticipant.uploadtopic')->LeftJoin('user', 'user.ID', '=', 'contestparticipant.user_id')->where('contestparticipant.contest_id', $contest_id)->get();

                                            for ($i = 0; $i < count($participantlist); $i++) {

                                                if ($participantlist[$i]['firstname'] != '') {
                                                    $name = $participantlist[$i]['firstname'] . ' ' . $participantlist[$i]['lastname'];
                                                } else {
                                                    $name = $participantlist[$i]['username'];
                                                }
                                                ?>
                                                <tr>
                                                    <td>{{ $i+1; }} </td>
                                                    <td class="tr_wid_id">{{ $name }}</td>
                                                    <td ><?php  if ($contestname->contesttype == 'p') { ?><img src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participantlist[$i]['uploadfile']; ?>" style="width:40px; height:40px;" /> <?php } elseif ($contestname->contesttype == 'v') { ?> <video src="<?php echo url() . '/public/assets/upload/contest_participant_photo/' . $participantlist[$i]['uploadfile']; ?>" style="width:60px; height:60px;" ></video>  <?php } else { ?> <div class="blacktxtbox"><?php echo substr(($participantlist[$i]['uploadtopic']), 0, 50) . "...."; ?></div>  <?php } ?>  </td>            

                                                    <td class="tr_wid_button1" align="center"><a href="<?php echo url() . '/adminviewcontest/' . $contest_id; ?>"  class="view-link"></a></td>
                                                    <td align="center"><?php if (Auth::user()->ID == 1) { ?><a href="#" class="del-link" onclick="contestparticipantdelete('<?php echo $participantlist[$i]['ID']; ?>', '<?php echo $contest_id; ?>')" ></a><?php } ?></td>
                                                </tr>
        <?php
    }
}
?>        
                                    </tbody>
                                    </table>   
                                    </div>
                                    </div>
                                    </div>
                                    <div class="clear"></div>
                                    @stop