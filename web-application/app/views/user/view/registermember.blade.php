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
        "sPageButton": "paginate_button",
        "bFilter": false
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
});
</script>
@stop
@section('body')
{{ Form::hidden('pagename','userlist', array('id'=> 'pagename')) }}
<?php
if (Session::has('er_data')) {
    $er_data = Session::get('er_data');
    //print_r($er_data);
}
?>
<!-- onload="__pauseAnimations();"-->

<div class="tabs-wrapper">
    <input type="radio" name="tab" id="tab1" class="tab-head" checked="checked"/>
    <label for="tab1"><span id="txt_userlist">User List</span></label>

    <a href="<?php echo url(); ?>/viewgroupmember/<?php echo $group_id; ?>">
        <div id="subtab_div" class="con_cat_right mbnone" >
            <button class="bck_btn" >&laquo; Back</button>
        </div></a>

    <div class="tab-body-wrapper">
        <div id="tab-body-1" class="tab-body">
            @if(isset($er_data['memberdelete']))
            <p class="alert" style="color:red;">{{ $er_data['memberdelete'] }}</p>
            @endif
            @if(isset($er_data['message']))
            <p class="alert" style="color:red;">{{ $er_data['message'] }}</p>
            @endif
            <div id="p">

                <h1><span class="txt_userlist">User List</span></h1>

                <div class="con_hed_blk">
                    <div class="group_search">
                        <form name="tab2-search"  action="{{ URL::to('usersearch/'.$group_id) }}" method="post" >
                            <div class="mb_group_search" style="vertical-align:top;margin:0; padding:0;">					

                                <input type="text" name="usersearch" id="usersearch" value="{{ isset($inputs['usersearch'])?$inputs['usersearch']:'' }}" class="pch_searchuser" placeholder="Search User name or Firstname" />
                                <input class="search_btn" type="submit" value="" />
                            </div>
                        </form>
                    </div>
                </div>
                <table class="display" cellspacing="0" width="100%" id="dd_group_list">
                    <thead>
                        <tr>
                            <th><span class="txt_sno">S.NO</span></th>
                            <th><span class="txt_img">Image</span></th>
                            <th><span class="txt_memname">User Name</span></th>     

                            <th class="tr_wid_button1" align="center"><span class="txt_view">Add to group</span></th>
                        </tr>
                    </thead>
                    <tbody>
<?php
if (Session::has('searcheduser')) {
    $searcheduser = Session::get('searcheduser');
}
if ($searcheduser != '') {
    for ($i = 0; $i < count($savegroupmembers); $i++) {
        ?>
                                <tr>
                                    <td>{{ $i+1; }} </td>
                                    <td align="center"><img src="{{ ($savegroupmembers[$i]['profilepicture']!='')?(URL::to('public/assets/upload/profile/'.$savegroupmembers[$i]['profilepicture'])):(URL::to('assets/inner/images/avator.png')) }}" width="50" height="50"></td>
                                    <td class="tr_wid_id"><?php if ($savegroupmembers[$i]['firstname'] != '') {
                            echo $savegroupmembers[$i]['firstname'] . ' ' . $savegroupmembers[$i]['lastname'];
                        } else {
                            echo $savegroupmembers[$i]['username'];
                        } ?></td>            

                                    <td align="center"><a href="<?php echo url(); ?>/addthismembertogroup?userid=<?php echo $savegroupmembers[$i]['ID']; ?>&group_id=<?php echo $group_id; ?>" class="add-link"></a></td>
                                </tr>
                            <?php }
                        }
                        ?>        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>    
<div class="clrscr"></div>
</div>
@stop