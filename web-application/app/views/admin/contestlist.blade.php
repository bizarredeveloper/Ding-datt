@extends('header.header')
<!-- All Cotest details view page --->
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
        url: 'activecontest',
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

function contestdelete(contestid) {

    var searchkey = $('#tsearch').val();

    var answer = confirm("Are you sure delete this contest?");
    if (answer)
    {
        window.location = "<?php echo url(); ?>/contestdelete?contestid=" + contestid + "&searchkey=" + searchkey;
    }

}
function regenerate(contestid) {
    var dataString = "contestid=" + contestid;
    $.ajax({
        type: "get",
        url: 'regenerateleaderboard',
        data: dataString,
        success: function (data) {
            console.log(data);
            if (data == 1) {
                $('#ajaxmessage').html('Leaderboard will be generate soon');
                $('#gen_btn_' + contestid + ' a').css('background-color', 'green');

            }
        }
    });
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

if (Session::has('usercontestlist')) {

    $usercontestlist = Session::get('usercontestlist');
}
if (Session::has('searchkey')) {
    $searchkey = Session::get('searchkey');
}

if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
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

    <label for="tab1">Contest List</span></label>
    <input type="radio" name="tab" id="tab2" class="tab-head" />
    <div class="mbblk">
        <select class="radius sel_lang" onchange="showtab()" id="mobileselected">
            <option value="createuser" <?php
            if ($tab == "createuser") {
                echo "selected";
            }
            ?>>Contest List</option>                
        </select>
    </div>

    <div class="tab-body-wrapper">

        <!------ Cotest List---------------------->
        <div id="tab-body-1" class="tab-body">
            @if(isset($er_data['message']))
            <p class="alert" style="color:red; font-size:13px">{{ $er_data['message'] }}</p>
            @endif
            <span class="alert" id="ajaxmessage" style="color:green; font-size:13px"></span>

            <?php if ($usercontestlist == '') { ?>
                <div class="con_hed_blk">
                    <div class="group_search">
                        <form name="tab2-search"  action="{{ URL::to('contestsearch') }}" method="post" >
                            <div class="mb_group_search" style="vertical-align:top;margin:0; padding:0;">
                                <input type="hidden" name="tab" value="grouplist">

                                <input type="text" name="tsearch2" id="tsearch" value="{{ isset($searchkey)?$searchkey:'' }}" class="pch_searchgroup" placeholder="Search contest" />
                                <input class="search_btn" type="submit" value="" />
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>	

            <div style="padding-left:1000px; color:#000000; font-size:13px;"><div class="gen_btn1" ><a href="{{ URL::to('contest') }}" style="text-decoration:none;">Create contest</a></div></div>

            <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Contest name</th>
                                    <!--<th>Contest owner</th>-->
                        <th>Contest type</th>
                        <th>Visibility type</th>
                        <th>Image</th>
                        <th>Leaderboard </br>regenerate</th>
                        <th>Active/Inactive</th>
                        <th class="tr_wid_button1" align="center"><span class="txt_view">Participant</span></th>
                        <th class="tr_wid_button1" align="center"><span class="txt_edit">Edit</span></th>
                        <th class="tr_wid_edit"><span class="txt_delete">Delete<span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($usercontestlist != '') {
                                            $usercontestlist = contestModel::where('createdby', $usercontestlist)->get();
                                        } else if ($searchkey != '') {
                                            $usercontestlist = contestModel::where('contest_name', 'like', '%' . $searchkey . '%')->get();
                                        } else {
                                            $usercontestlist = contestModel::get();
                                        }
                                        for ($i = 0; $i < count($usercontestlist); $i++) {

                                            //$createdowner = User::select('firstname','lastname','username')->where('ID',$usercontestlist[$i]['createdby'])->get();
                                            ?>
                                            <tr>
                                                <td>{{ $i+1; }} </td>
                                                <td class="tr_wid_id"><a href="{{ URL::to('contest_info/'.$usercontestlist[$i]['ID']) }}" style="text-decoration:none;">{{ $usercontestlist[$i]['contest_name'] }}</a></td>
                                                <?php /* <td><?php if($createdowner[0]['firstname']!=''){ echo $createdowner[0]['firstname'].' '.$createdowner[0]['lastname'];  }else{ echo $createdowner[0]['username']; } ?></td> */ ?>
                                                <td ><?php
                                                    if ($usercontestlist[$i]['contesttype'] == 'p')
                                                        echo "Photo";
                                                    elseif ($usercontestlist[$i]['contesttype'] == 'v')
                                                        echo "Video";
                                                    else
                                                        echo "Topic"
                                                        ?></td>
                                                <td ><?php
                                                    if ($usercontestlist[$i]['visibility'] == 'p')
                                                        echo "Private";
                                                    else
                                                        echo "Public";
                                                    ?></td>
                                                <td align="center"><img src="{{ URL::to('/public/assets/upload/contest_theme_photo/'.$usercontestlist[$i]['themephoto']) }}" width="50" height="50"></td>
                                                <td>

                                                    <div class="gen_btn" id="gen_btn_<?php echo $usercontestlist[$i]['ID']; ?>"><a name="submitphoto" onclick="regenerate('<?php echo $usercontestlist[$i]['ID']; ?>')">Generate</a></div>

                                                </td>
                                                <td><a href="#" onclick="changeactive('<?php echo $usercontestlist[$i]['ID']; ?>')" <?php
                                                    if ($usercontestlist[$i]['status'] == 1) {
                                                        echo 'style="background-color:green;"';
                                                    } else {
                                                        echo 'style="background-color:red;"';
                                                    }
                                                    ?> class="add-link backclr_<?php echo $usercontestlist[$i]['ID']; ?>"></a></td>
                                                <td class="tr_wid_button1" align="center"><a href="<?php echo url(); ?>/contestparticipantlist/<?php echo $usercontestlist[$i]['ID']; ?>" class="view-link"></a></td>
                                                <td class="tr_wid_button1" align="center"><?php if (Auth::user()->ID == 1) { ?><a href="{{ URL::to('edit_contest/'.$usercontestlist[$i]['ID']) }}" class="edit-link"></a><?php } ?></td>
                                                <td align="center"><?php if (Auth::user()->ID == 1) { ?><a href="#" class="del-link" onclick="contestdelete('<?php echo $usercontestlist[$i]['ID']; ?>')" ></a><?php } ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>        
                                    </tbody>
                                    </table>   
                                    </div>

                                    </div>
                                    </div>
                                    <div class="clear"></div>
                                    @stop