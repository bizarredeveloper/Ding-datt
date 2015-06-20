@extends('header.header')
<?php
$assets_path = "assets/inner/";
?>
@section('includes')	


<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />

<script src="{{ URL::to('assets/inner/js/jquery-ui.js') }}"></script>

<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-sliderAccess.js') }}"></script>

<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>


<script type="text/javascript">
$(document).ready(function () {

    jQuery('#dd_group_list').dataTable({
        "bPaginate": true,
        "sPaginationType": "full_numbers",
        "iDisplayLength": 10,
        "sPageButton": "paginate_button"
    });


    $(".accept").click(function (e) {
        var jsonlist = [];
        var invitetype = [];
        var group_id = [];
        var user_id = [];
        if (jQuery('.checkmember:checked').length > 0)
        {


            jQuery('.checkmember:checked').each(function (index, element) {
                var s = jQuery(this).val().split('~');
                invitetype.push(s[0]);
                group_id.push(s[1]); //+"&grpid="+group_id	
                user_id.push(s[2]);
            });

            console.log(invitetype);
            console.log(group_id);
            console.log(user_id);


            jQuery.ajax({
                type: 'get',
                url: 'ajaxaccepgroup',
                data: "invite=" + invitetype + "&group_id=" + group_id + "&user_id=" + user_id,
                success: function (msg)
                {
                    console.log(msg);
                    $('#alrtmsg').html(msg);
                    location.reload();
                }
            });

        }
        else
        {
            alert("Choose member for accept");
        }



    });
    $(".reject").click(function (e) {
        // alert(jQuery('.checkmember:checked').length)
        var jsonlist = [];
        var invitetype = [];
        var group_id = [];
        var user_id = [];
        if (jQuery('.checkmember:checked').length > 0)
        {


            jQuery('.checkmember:checked').each(function (index, element) {
                var s = jQuery(this).val().split('~');
                invitetype.push(s[0]);
                group_id.push(s[1]); //+"&grpid="+group_id	
                user_id.push(s[2]);
            });
            jQuery.ajax({
                type: 'get',
                url: 'ajaxrejectgroup',
                data: "invite=" + invitetype + "&group_id=" + group_id + "&user_id=" + user_id,
                success: function (msg)
                {
                    //console.log(msg);
                    $('#alrtmsg').html(msg);
                    location.reload();
                }
            });
        }
        else
        {
            alert("Choose member for Reject");
        }



    });

    jQuery('.checkall').click(function (event) {
        if (jQuery(this).is(':checked'))
            jQuery('.checkmember').prop('checked', true);
        else
            jQuery('.checkmember').prop('checked', false);
    });
///

});

</script>
@stop
@section('body')
{{ Form::hidden('pagename','acceptmemberlist', array('id'=> 'pagename')) }}
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
    //print_r($er_data);
}
?>


<!-- onload="__pauseAnimations();"-->

<div class="clrscr"></div>

<div class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" checked="checked"/>
    <label for="tab1"><span id="txt_groupmember_Accept">Group Request</span></label>


    <div class="tab-body-wrapper">
        <div id="tab-body-1" class="tab-body">
            @if(isset($er_data['memberdelete']))
            <p class="alert" style="color:red;">{{ $er_data['memberdelete'] }}</p>
            @endif
            @if(isset($er_data['message']))
            <p class="alert" style="color:red;">{{ $er_data['message'] }}</p>
            @endif
            <span id="alrtmsg" style="color:red;"></span>
            <div id="p">
                <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                    <thead>
                        <tr>

                            <th class="tr_wid_button1" align="center"><center><input type="checkbox" class="checkall" /></center></th>
                    <th><span class="txt_groupname">Group Name</span></th> 
                    <th><span class="txt_grp_img">Group Image</span></th>
                    <th><?php if ($accepttype == 'admin') { ?><span class="txt_memname">User Name</span><?php } else { ?><span class="txt_grpowner">Group owner</span><?php } ?></th>

                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($accepttype == 'admin') {

                            $invitedlist = invitememberforgroupModel::select('invitememberforgroup.user_id as usrid', 'user.profilepicture', 'user.firstname', 'user.lastname', 'user.username', 'group.groupname', 'group.ID as group_id', 'invitememberforgroup.invitetype', 'group.groupimage')->where('group.createdby', Auth::user()->ID)->where('invitememberforgroup.invitetype', 'u')->LeftJoin('group', 'group.ID', '=', 'invitememberforgroup.group_id')->Join('user', 'user.ID', '=', 'invitememberforgroup.user_id')->get();
                        } else {
                            $invitedlist = invitememberforgroupModel::select('invitememberforgroup.user_id as usrid', 'user.profilepicture', 'user.firstname', 'user.lastname', 'user.username', 'group.groupname', 'invitememberforgroup.invitetype', 'group.ID as group_id', 'group.groupimage', 'group.createdby')->LeftJoin('user', 'user.ID', '=', 'invitememberforgroup.user_id')->where('user_id', Auth::user()->ID)->where('invitetype', 'm')->LeftJoin('group', 'group.ID', '=', 'invitememberforgroup.group_id')->get();

                        }



                        for ($i = 0; $i < count($invitedlist); $i++) {

                            if ($accepttype == 'admin') {
                                if ($invitedlist[$i]['firstname'] != '') {
                                    $membername = $invitedlist[$i]['firstname'] . ' ' . $invitedlist[$i]['lastname'];
                                } else {
                                    $membername = $invitedlist[$i]['username'];
                                }
                            } else {
                                $memberdetails = User::find($invitedlist[$i]['createdby']);
                                if ($memberdetails['firstname'] != '') {
                                    $membername = $memberdetails['firstname'] . ' ' . $memberdetails['lastname'];
                                } else {
                                    $membername = $memberdetails['username'];
                                }
                            }
                            ?>

                            <tr>
                                <td align="center"><input type="checkbox" class="checkmember" value="<?php echo $invitedlist[$i]['invitetype'] . '~' . $invitedlist[$i]['group_id'] . '~' . $invitedlist[$i]['usrid']; ?>" /></td>
                                <td >{{ $invitedlist[$i]['groupname']}}</td>
                                <td align="center"><img src="{{ ($invitedlist[$i]['groupimage']!='')?(URL::to('public/assets/upload/group/'.$invitedlist[$i]['groupimage'])):(URL::to('assets/inner/img/default_groupimage.png')) }}" width="50" height="50"></td>
                                <td class="tr_wid_id"><?php echo $membername; ?></td>            


                            </tr>
<?php } ?>



                    </tbody>
                </table>
            </div>
        </div>
        <div class="loginbox" style="margin:0 35%;">
            <center>
                <button class="radius martop_10 accept" name="client_login"><span class="btn_accept" id="txt_accept">Accept</span></button>
                <button class="radius martop_10 reject blue_btn" name="client_login"><span class="submnu_creategroup" id="txt_reject">Reject</span></button>
            </center>
        </div>
    </div>
</div>

<div class="clrscr"></div>
</div>
@stop