@extends('layouts.dashboardlayout')
@section('body')
        <div class="form-panel">
        <div class="header-panel">
        <h2><!--<span class="icon icon-student"></span>-->Settings Master</h2>
        </div>
        <div class="dash-content-panel"> <!-- dash panel start -->
        
        <div class="dash-content-row "> <!-- dash content row start -->
        <div class="dash-content-head tabContaier">
        <h5>Add Grade</h5>
        @if(Session::has('Message'))
        <p class="alert">{{ Session::get('Message') }}</p>
        @endif
        {{ Form::open(array('url' => 'classupdateprocess/'.$ClassDetailsbyid[0]['AutoID'])) }}
        </div>
        <div class="tabDetails">         
        <div class="panel-row">
        <ul class="dash-form-lister">
        <li>
        <div class="label-control">
        {{ Form::label('GradeName', 'Grade' ) }}<em>*</em>
        </div>
        <div class="input-control">
        {{ Form::text('GradeName', null, array('id' => 'ClassName')) }}
        </div>
        {{ $errors->first('GradeName', '<div class="error">:message</div>') }}
        </li>
        
        </ul>
        <div class="btn-group form-list-btn-group" >
        {{ Form::submit('Save', ['class' => 'submit-btn']) }}    
        {{ Form::reset('Cancel', ['class' => 'resetbutton']) }}
        </div>
        {{ Form::close() }}
        </div>
<script>
$(document).ready(function(){
$("#ClassName").val("<?php echo $ClassDetailsbyid[0]['GradeName']?>");

$('#student-listing-table').dataTable();
});
</script>
        <div class="panel-row list-row">
        <div class="dash-content-head tabContaier">
        <h5>Grade List</h5>
        </div>
     
        <div class="panel-tab-row"> <!---------------- student listing table start ------>
        <table class="student-listing-table" id="student-listing-table">
        <thead>
        <tr>
       
        <th>GradeName</th>
        <th>Action</th>
        </tr>
        </thead>
        <tbody>
		<?php
		
		foreach ($ClassDetails as $Classvalue)
{
		?>
        <tr>
        <td><span class="tab-check"></span><?php echo $Classvalue['GradeName'];?></td>
       
         <td>       
        <a href="<?php echo url();?>/classedit/<?php echo $Classvalue['AutoID'];?>"><button class="edtit-btn btn-sm"><span class="icon"></span></button></a>
        
		<a href="javascript:;" id="<?php echo url();?>/classdelete/<?php echo $Classvalue['AutoID'];?>" class="btnOpenDialog"><button class="delete-btn btn-sm"><span class="icon"></span></button></a></td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
        </div>
        </div>
        </div>
        </div>
        <!-- dash content row end --> 
        </div>
        </div>
@stop