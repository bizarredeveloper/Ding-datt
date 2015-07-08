@extends('header.header')
@section('body')

<div id="dialog-confirm"></div>
<script>
    function fnOpenNormalDialog() {
        var url = $(this).attr("id");
        var deletenamemultilingual = $('#txt_delete_record').val();
        var okmultilingual = $('#txt_ok').val();
        var cancelmultilingual = $('#txt_cancel').val();
        $("#dialog-confirm").html(deletenamemultilingual);
        var buttonsConfig = [
            {
                text: okmultilingual,
                "class": "ok",
                click: function () {
                    $(this).dialog('close');
                    window.location.href = url;
                }
            },
            {
                text: cancelmultilingual,
                "class": "cancel",
                click: function () {
                    $(this).dialog('close');
                }
            }
        ];
        // Define the Dialog and its properties.
        $("#dialog-confirm").dialog({
            resizable: false,
            modal: true,
            title: "Ding Datt",
            height: 250,
            width: 400,
            buttons: buttonsConfig,
        });
    }
</script>


<script>
    $(document).ready(function () {
        $('#example').DataTable();
        $('.btnOpenDialog').click(fnOpenNormalDialog);
    });

</script>

<div class="form-panel">
    <div class="header-panel">
        <h2></h2>
    </div>
    <div class="dash-content-panel"> <!-- dash panel start -->

        <div class="dash-content-row " > <!-- dash content row start -->
            <div class="dash-content-head tabContaier">
                <h5><span id="txt_user_details"></span></h5>
            </div>
            <!--- User Edit List ----->
            <p> {{ Form::hidden('pagename','userdetails', array('id'=> 'pagename')) }}

                @if(Session::has('Message'))
            <p class="alert">{{ Session::get('Message') }}</p>
            @endif
            </p>



            <!--- User List ------>
            <?php if (!empty($userdetails)) { ?>
                <div class="panel-row list-row">
                    <div class="dash-content-head tabContaier">
                        <h5><a href="<?php echo url(); ?>/userregister" /><span id="txt_add_user"></span></a></h5>

                    </div>
                    <div class="panel-tab-row">

                        <table id="example" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                        <!--<th>Category</th>-->
                                    <th><span id="txt_name"></span></th>
                                    <th><span id="txt_email"></span></th>
                                    <th><span id="txt_mobile"></span></th> 
                                    <th><span id="txt_status"></span></th>		
                                    <th><span id="txt_action"></span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userdetails as $userdetails) { ?>

                                    <tr>
                                        <td><?php echo $userdetails['firstname'] . " " . $userdetails['lastname']; ?></td>
                                        <td><?php echo $userdetails['email']; ?></td>
                                        <td><?php echo $userdetails['mobile']; ?></td>
                                        <td><?php if ($userdetails['status']) echo "Active";
                            else echo "In Active"; ?></td>

                                        <td><a href="<?php echo url(); ?>/useredit/<?php echo $userdetails['ID']; ?>"><button class="edtit-btn btn-sm"><span class="icon"></span></button></a>
                                <!--<a href="javascript:;" id="<?php echo url(); ?>/userdelete/<?php echo $userdetails['ID']; ?>" class="btnOpenDialog"><button class="delete-btn btn-sm"><span class="icon"></span></button></a>--></td>
                                    </tr>

    <?php } ?>
                            </tbody>
                        </table>
                    </div></div>
<?php } ?>

        </div>
    </div>
</div>{{ Form::hidden('txt_delete_record','', array('id'=> 'txt_delete_record')) }}
{{ Form::hidden('txt_ok','', array('id'=> 'txt_ok')) }}
{{ Form::hidden('txt_cancel','', array('id'=> 'txt_cancel')) }}

@stop