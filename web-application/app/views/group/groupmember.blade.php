@extends('header.header')
<!-- Group member view list page -->
<?php
$assets_path = "assets/inner/";
if (Session::has('contest_id')) {
    $contest_id = Session::get('contest_id');
}
?>
@section('includes')   
<link rel="stylesheet" media="all" type="text/css" href="{{ URL::to('assets/inner/css/jquery-ui.css') }}" />

<script src="{{ URL::to('assets/inner/js/jquery-ui.js') }}"></script>

<script type="text/javascript" src="{{ URL::to('assets/inner/js/jquery-ui-sliderAccess.js') }}"></script>

<link type="text/css" rel="stylesheet" href="{{ URL::to('assets/inner/js/datatbl/demo_table.css') }}">
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('assets/inner/js/datatbl/jquery.dataTables.js') }}"></script>

<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "d4ab7898-2b6c-4cc4-a753-12192e1bb354", shorten: false, doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>

<script>
    stWidget.addEntry({
        "service": "sharethis",
        "element": document.getElementById('button_1'),
        "url": "http://sharethis.com",
        "title": "sharethis",
        "type": "large",
        "text": "ShareThis",
        "image": "http://www.softicons.com/download/internet-icons/social-superheros-icons-by-iconshock/png/256/sharethis_hulk.png",
        "summary": "this is description1"
    });
</script>

<style>
</style>
<script type="text/javascript">
    $(document).ready(function () {

        jQuery('#dd_group_list').dataTable({
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            "iDisplayLength": 10,
            "sPageButton": "paginate_button",
            'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': [0] /* 1st one, start by the right */
                }]
        });


        jQuery('.delete-link').live('click', function (event) {
            if (confirm('Are you sure to delete?'))
            {
                jQuery.ajax({
                    type: 'POST',
                    url: jQuery(this).attr('href'),
                    data: "id=" + jQuery(this).attr('id') + "&action=DELETE",
                    success: function (msg)
                    {
                        if (msg == 1) {
                            alert('Successfully deleted');
                            window.location.reload(true);
                        }
                        else if (msg == 0) {
                            alert("This member booked the class. So not able to delete member.");
                            return false;
                        }
                    }
                });
            }
            event.preventDefault();
        });

        jQuery('.checkall').click(function (event) {


            var contest_id = '<?php echo $contest_id; ?>';
            var groupid = '<?php echo $group_id; ?>';
            var groupmemberids = [];
            if (jQuery(this).is(':checked')) {
                jQuery('.checkseparate').prop('checked', true);
                /// Invite All /////

                jQuery('.checkseparate').each(function (index, element) {
                    if (jQuery(this).is(':checked')) {
                        groupmemberids.push(jQuery(this).val());
                        jQuery('.invitetype_' + jQuery(this).val()).html('Proccessing');
                        $(".invitetype_" + jQuery(this).val()).css('color', 'blue');

                    }
                });
                var dataString = 'groupid=' + groupid + "&contest_id=" + contest_id + "&checkseparate=" + groupmemberids + "&invitetype=all";
                $.ajax({
                    type: "GET",
                    url: 'invitegroupmemberforcontest',
                    data: dataString,
                    success: function (data) {
                        console.log(data);
                        if (data == 1)
                        {
                            $(".invitetypeall").css('color', 'red');
                            $(".invitetypeall").html("Invited");
                            $("#inv_success").html("Invited successfully");
                        }
                    }
                });


                //console.log(groupmemberids);

            } else {

                jQuery('.checkseparate').prop('checked', false);
                /// Univite All /////
                jQuery('.checkseparate').each(function (index, element) {
                    groupmemberids.push(jQuery(this).val());
                });  //console.log(groupmemberids);

                //	window.location = "<?php echo url(); ?>/uninviteallgroupmemberforcontest?groupid="+groupid+"&checkseparate="+groupmemberids+"&contest_id="+contest_id;

                var dataString = 'groupid=' + groupid + "&contest_id=" + contest_id + "&checkseparate=" + groupmemberids + "&uninvitetype=all";
                $.ajax({
                    type: "GET",
                    url: 'uninviteallgroupmemberforcontest',
                    data: dataString,
                    success: function (data) {
                        console.log(data);
                        if (data == 1)
                        {
                            $(".invitetypeall").css('color', 'green');
                            $(".invitetypeall").html("Invite");
                            $("#inv_success").html("Uninvited successfully");
                        }
                    }
                });

            }


        });

        jQuery('.checkseparate').click(function (event) {

            var contest_id = '<?php echo $contest_id; ?>';
            var groupid = '<?php echo $group_id; ?>';
            var checkseparate = jQuery(this).val();


            jQuery('.invitetype_' + checkseparate).html('Proccessing');
            $(".invitetype_" + checkseparate).css('color', 'blue');
            //groupid,contest_id,checkseparate

            if (jQuery(this).is(':checked')) {

                var dataString = 'groupid=' + groupid + "&contest_id=" + contest_id + "&checkseparate=" + checkseparate + "&invitetype=seperate";
                $.ajax({
                    type: "GET",
                    url: 'invitegroupmemberforcontest',
                    data: dataString,
                    success: function (data) {
                        console.log(data);
                        if (data == 1)
                        {
                            $(".invitetype_" + checkseparate).css('color', 'red');
                            $(".invitetype_" + checkseparate).html("Invited");
                            $("#inv_success").html("Invited successfully");
                        }
                    }
                });


                //window.location = "<?php echo url(); ?>/invitegroupmemberforcontest?groupid="+groupid+"&checkseparate="+checkseparate+"&contest_id="+contest_id;	

            } else {
                /// Uninvite process ////

                var dataString = 'group_id=' + groupid + "&contest_id=" + contest_id + "&groupmemberid=" + checkseparate;
                $.ajax({
                    type: "GET",
                    url: 'uninvite_group_member',
                    data: dataString,
                    success: function (data) {
                        console.log(data);
                        if (data == 1)
                        {

                            $(".invitetype_" + checkseparate).html("Invite");
                            $(".invitetype_" + checkseparate).css('color', 'green');
                            $("#inv_success").html("Uninvited successfully");
                        }
                    }
                });
            }

        });

        //// For pagination /////
        $(".paginate_button").click(function () {
            jQuery('.checkseparate').click(function (event) {

                var contest_id = '<?php echo $contest_id; ?>';
                var groupid = '<?php echo $group_id; ?>';
                var checkseparate = jQuery(this).val();


                jQuery('.invitetype_' + checkseparate).html('Proccessing');
                $(".invitetype_" + checkseparate).css('color', 'blue');
                //groupid,contest_id,checkseparate

                if (jQuery(this).is(':checked')) {

                    var dataString = 'groupid=' + groupid + "&contest_id=" + contest_id + "&checkseparate=" + checkseparate + "&invitetype=seperate";
                    $.ajax({
                        type: "GET",
                        url: 'invitegroupmemberforcontest',
                        data: dataString,
                        success: function (data) {
                            console.log(data);
                            if (data == 1)
                            {
                                $(".invitetype_" + checkseparate).css('color', 'red');
                                $(".invitetype_" + checkseparate).html("Invited");
                                $("#inv_success").html("Invited successfully");
                            }
                        }
                    });


                    //window.location = "<?php echo url(); ?>/invitegroupmemberforcontest?groupid="+groupid+"&checkseparate="+checkseparate+"&contest_id="+contest_id;	

                } else {
                    /// Uninvite process ////

                    var dataString = 'group_id=' + groupid + "&contest_id=" + contest_id + "&groupmemberid=" + checkseparate;
                    $.ajax({
                        type: "GET",
                        url: 'uninvite_group_member',
                        data: dataString,
                        success: function (data) {
                            console.log(data);
                            if (data == 1)
                            {

                                $(".invitetype_" + checkseparate).html("Invite");
                                $(".invitetype_" + checkseparate).css('color', 'green');
                                $("#inv_success").html("Uninvited successfully");
                            }
                        }
                    });
                }

            });


        });

    });

    function groupmrmbrdelete(groupmemberid, group_id)
    {
        var answer = confirm('Are you sure you want to remove?');
        if (answer)
        {
            window.location = "<?php echo url(); ?>/groupmemberdelete?groupmemberid=" + groupmemberid + "&group_id=" + group_id;

        }
    }

    function invitegroupmember(groupid)
    {

        if (jQuery('.checkseparate:checked').length > 0)
        {
            var checkseparate = [];
            jQuery('.checkseparate').each(function (index, element) {
                if (jQuery(this).is(':checked')) {
                    checkseparate.push(jQuery(this).val());
                }
            });
            var contest_id = '<?php echo $contest_id; ?>';
            //alert(contest_id);
            window.location = "<?php echo url(); ?>/invitegroupmemberforcontest?groupid=" + groupid + "&checkseparate=" + checkseparate + "&contest_id=" + contest_id;

        }
        else {
            alert("Choose member for invite");
        }
    }


    function uninviteallgroupmember(groupid)
    {

        if (jQuery('.checkseparate:checked').length > 0)
        {
            var checkseparate = [];
            jQuery('.checkseparate').each(function (index, element) {
                if (jQuery(this).is(':checked')) {
                    checkseparate.push(jQuery(this).val());
                }
            });
            var contest_id = '<?php echo $contest_id; ?>';

            window.location = "<?php echo url(); ?>/uninviteallgroupmemberforcontest?groupid=" + groupid + "&checkseparate=" + checkseparate + "&contest_id=" + contest_id;

        }
        else {
            alert("Choose member for Uninvite");
        }
    }



    function exitgroup(groupid, groupmeberuserid) {

        //console.log(groupid); console.log(groupmeberuserid);
        var answer = confirm('Are you sure you want to exit?');
        if (answer)
        {
            window.location = "<?php echo url(); ?>/exitgroup?groupid=" + groupid + "&groupmeberuserid=" + groupmeberuserid;
        }
    }

</script>
@stop
@section('body')
{{ Form::hidden('pagename','groupmember', array('id'=> 'pagename')) }}
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');

}

if (Session::has('Message')) {
    $Message = Session::get('Message');
}
?>
<div class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" checked="checked"/>
    <label for="tab1"><span id="txt_groupmember">Group Member</span></label>

    <div id="subtab_div" class="con_cat_right mbnone" >
        <button class="bck_btn" onclick="goback()" >&laquo; <span class="txt_back" > Back </span> </button>
    </div>

    <div class="tab-body-wrapper">
        <div id="tab-body-1" class="tab-body">
            @if(isset($er_data['memberdelete']))
            <p class="alert" style="color:red; font-size:13px;">{{ $er_data['memberdelete'] }}</p>
            @endif
            @if(isset($er_data['message']))
            <p class="alert" style="color:red; font-size:13px;">{{ $er_data['message'] }}</p>
            @endif
            @if(isset($Message))
            <p class="alert" style="color:red; font-size:13px;">{{ $Message }}</p>
            @endif
            <span id="inv_success" class="alert" style="color:green; font-size:13px;"></span>
            <div id="p">
<?php $d = groupModel::select('groupname', 'createdby', 'ID')->where('ID', $group_id)->get(); ?>
                <h1><?php echo $d[0]['groupname']; ?></h1>
                <div class="fleft mb_brk">
<?php $membercount = groupmemberModel::where('group_id', $group_id)->where('user_id', Auth::user()->ID)->get()->count();
?>
                    <nav class="slidernav">
                        <div id="navbtns" class="clearfix" ><b>
<?php if ($showjoinbtn != 'no') { ?>
                        <?php if ($membercount != 1) { ?><a href="<?php echo url(); ?>/joinintogroup/<?php echo $group_id; ?>"><span class="txt_join">Join</span></a><?php } ?>
                        <?php if ($d[0]['createdby'] == Auth::user()->ID || Auth::user()->ID == 1) { ?><a href="<?php echo url(); ?>/addmembertogroup/<?php echo $group_id; ?>"><span class="btn_addmember">Add Member</span></a><?php } ?>
                    <?php } ?></b>
                        </div>
                    </nav> 
                </div>

                                <?php
                                if ($showjoinbtn != 'no') {
                                    //if(Auth::user()->ID==$d[0]['createdby']) { 
                                    ?>

                    <meta property="og:title" content="DingDatt - Invitation for Group"/>
                    <meta property="og:type" content="website"/>
                    <meta property="og:url" content="{{ Request::url() }}"/>
                    <meta property="og:image" content="{{ URL::to('assets/inner/img/DingDatt_logo_web1.png')}}"/>
                    <!--<meta property="og:description" content="{{ 'Group name: '.$d[0]['groupname'] }}"/>-->

                    <meta property="og:description" content="{{ URL::to('sharegroup/'.$d[0]['ID']) }}"/>

                    <div class="fright mb_brk" style="font: normal 13px/20px Arial, Helvetica, sans-serif;">Share with
                        <span class='st_facebook_large' ></span>
                        <span class='st_twitter_large' ></span>
                        <span class='st_tumblr_large' ></span>					
                        <span class='st_email_large'  ></span>
                        <span class='st_googleplus_large'></span>
                        <span class='st_instagram_large'></span>
                        <span class='st_pinterest_large'></span>

                    </div>
    <?php //}
}
?>



                <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                    <thead>
                        <tr>
                <?php
                if ($showjoinbtn == 'no') {

                    $invited = invitegroupforcontestModel::where('group_id', $group_id)->where('contest_id', $contest_id)->count();

                    $groupmemberlist = groupmemberModel::where('group_id', $group_id)->get()->count();
                    ?><th style="background-color:#0896D6"><input type="checkbox" name="checkall" class="checkall" <?php if ($invited + 1 == $groupmemberlist && $groupmemberlist != 1) echo "checked"; ?> /></th> <?php } ?>
                            <th style="background-color:#0896D6"><span class="txt_sno">S.NO</span></th>
                            <th><span class="txt_img">Image</span></th>
                            <th><span class="txt_memname">Member Name</span></th> 
                            <?php if ($showjoinbtn == 'no') { ?><th>Invite/Uninvite</th> <?php } ?>
                            <?php if ($showjoinbtn != 'no') { ?><th class="tr_wid_button1" align="center"><span class="txt_remove">Remove / Exit</span></th><?php } ?>
                            <?php if ($showjoinbtn != 'no') { ?><th class="tr_wid_button1" align="center"><span class="txt_exit">Exit</span></th><?php } ?>
                            <th class="tr_wid_button1" align="center"><span class="txt_view">View</span></th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php
                            $savegroupmembers = groupmemberModel::select('group_members.id as groupmemberid', 'group_members.user_id', 'user.firstname', 'user.lastname', 'user.username', 'user.profilepicture', 'group.createdby as groupadmin_userid', 'user.ID as usrid')
                                            ->LeftJoin('user', 'user.ID', '=', 'group_members.user_id')
                                            ->where('group_id', $group_id)->where('user.status', 1)
                                            ->LeftJoin('group', 'group.ID', '=', 'group_members.group_id')
                                            ->orderby('group_members.id')->get();

                            if ($showjoinbtn == 'no') {
                                $contestdetails = contestModel::where('ID', $contest_id)->get()->first();
                            }


                            for ($i = 0; $i < count($savegroupmembers); $i++) {
                                ?>
                            <?php
                            if ($showjoinbtn == 'no') {
                                $invited = invitegroupforcontestModel::where('group_id', $group_id)->where('contest_id', $contest_id)->where('user_id', $savegroupmembers[$i]['user_id'])->count();
                                ?> <td><?php if ($contestdetails['createdby'] != $savegroupmembers[$i]['user_id'] && $savegroupmembers[$i]['user_id'] != 1) { ?> <input type="checkbox" name="checkseparate" class="checkseparate" <?php if ($invited > 0) {
                            echo "checked";
                        } ?> value="{{ $savegroupmembers[$i]['groupmemberid'] }}" /><?php } ?></td> 
                            <?php }
                            ?>
                        <td>{{ $i+1; }} </td>
                        <td align="center"><img src="{{ ($savegroupmembers[$i]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$savegroupmembers[$i]['profilepicture'])):(URL::to('assets/inner/images/avator.png')) }}" width="50" height="50"></td>
                        <td class="tr_wid_id"><?php
                            if ($savegroupmembers[$i]['firstname'] != '') {
                                echo $savegroupmembers[$i]['firstname'] . ' ' . $savegroupmembers[$i]['lastname'];
                            } else {
                                echo $savegroupmembers[$i]['username'];
                            }
                            if ($savegroupmembers[$i]['usrid'] == $savegroupmembers[$i]['groupadmin_userid'])
                                echo '<font class="alert" style="color:green;"> (Group owner)</font>';
                            ?></td> 
                            <?php if ($showjoinbtn == 'no') { ?><td <?php if ($contestdetails['createdby'] != $savegroupmembers[$i]['user_id']) { ?> class="invitetype_<?php echo $savegroupmembers[$i]['groupmemberid']; ?> invitetypeall" id="invitetype" <?php } ?>> 

                                <?php if ($contestdetails['createdby'] != $savegroupmembers[$i]['user_id'] && $contestdetails['createdby'] != 1 && $savegroupmembers[$i]['user_id'] != 1) {
                                    if ($invited > 0) {
                                        echo "<font style='color:red;' id='yy' class='asas' >Invited</font>";
                                    } else {
                                        echo "<font style='color:green;' id='yy' class='asas' >Invite</font>";
                                    } /* ?>

                                      <a href="#" <?php if($invited>0){ ?> title="Uninvited" style="background-color:red;" onClick="uninvite_groupmember('{{ $group_id }}','{{ $contest_id }}','{{ $savegroupmembers[$i]['groupmemberid'] }}');" id="invite_list_{{ $savegroupmembers[$i]['groupmemberid'] }}" <?php } else { ?> title="Invite" onClick="invite_groupmemberseperate('{{ $group_id }}','{{ $contest_id }}','{{ $savegroupmembers[$i]['groupmemberid'] }}');" <?php }?> id="invite_list_{{ $savegroupmembers[$i]['groupmemberid'] }}" class="add-link"></a>
                                      <?php */
                                } ?>

                            </td> <?php } ?>			
                    <?php if ($showjoinbtn != 'no') { ?> <td class="tr_wid_button1" align="center"><?php if (Auth::user()->ID == $savegroupmembers[$i]['groupadmin_userid'] && Auth::user()->ID != $savegroupmembers[$i]['user_id'] || Auth::user()->ID == 1 && $savegroupmembers[$i]['usrid'] != $savegroupmembers[$i]['groupadmin_userid']) { ?><a href="#" onclick="groupmrmbrdelete('<?php echo $savegroupmembers[$i]['groupmemberid']; ?>', '<?php echo $group_id; ?>')" class="remove-link"></a><?php } ?></td><?php } ?>

    <?php if ($showjoinbtn != 'no') { ?><td align="center"><?php if (Auth::user()->ID == $savegroupmembers[$i]['user_id'] && Auth::user()->ID != $savegroupmembers[$i]['groupadmin_userid']) { ?><a href="#" onClick="exitgroup('<?php echo$group_id; ?>', '<?php echo $savegroupmembers[$i]['user_id']; ?>')" class="exit-link" style="background-color:red"></a><?php } ?> </td><?php } ?>
                        <td align="center"><a href="<?php echo url(); ?>/other_profile/<?php echo $savegroupmembers[$i]['user_id']; ?>" class="view-link"></a></td>
                        </tr>
<?php } ?>
                    </tbody>
                </table>
<?php if ($showjoinbtn == 'no') {
    
} ?>
            </div>
        </div>
    </div>
</div>
<div class="clrscr"></div>
</div>
@stop