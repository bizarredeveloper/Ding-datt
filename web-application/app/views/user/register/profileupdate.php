@extends('header.header')
@section('body')

<style> 
    .error {

        color: blue;
    }
</style>
<script>


</script>

<?php
if (!empty($profileeditbyid)) {
    ?>
    {{ Form::open(array('url' => 'laravel_registeredit/'.$profileeditbyid[0]['ID'], 'files'=> true, 'class' => 'form-signin')) }}
<?php } else { ?>
    {{ Form::open(array('url' => 'laravel_register', 'class' => 'form-signin')) }}
<?php
}
$input = Input::old();
?>
<h4>User Registration</h4><br>
<h2 class="form-signin-heading">
    <h2 class="form-signin-heading"><a href='login'>Back to Login</a></h2>
</h2>
<div id="stage" ></div>
<div id="div1" style="color:green;display:none;">Registered Succesfully, You will be Rdirected in 10 Seconds to Login Page.</div>

@if(Session::has('Message'))
<p class="alert">{{ Session::get('Message') }}</p>
@endif

<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"  id="_token">
First Name :<input type="text" class="form-control" placeholder="First Name" name="firstname"  id="firstname">         
<div class="error" >{{ $errors->first('firstname') }}</div>
Last Name :<input type="text" class="form-control" placeholder="Last Name" name="lastname" value="<?php echo Input::old('lastname'); ?>" id='lastname' >
<div class="error" >{{ $errors->first('lastname') }}</div>
Email : <input type="text" class="form-control" placeholder="Email Address" name="email" value="<?php echo Input::old('email'); ?>" id='email'>
<div class="error" >{{ $errors->first('email') }}</div>
Mobile : <input type="text" class="form-control" placeholder="Mobile Number" name="mobile" value="<?php echo Input::old('mobile'); ?>" id='mobile'>
<div class="error" >{{ $errors->first('mobile') }}</div>
Password : <input type="password" class="form-control" placeholder="Password"  name="password" value="<?php echo Input::old('password'); ?>" id='password'>
<div class="error" >{{ $errors->first('password') }}</div>
Confirm Password : <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" value="<?php echo Input::old('password_confirmation'); ?>" id='password_confirmation'>
<div class="error" >{{ $errors->first('password_confirmation') }}</div>
<input type="hidden" class="form-control" placeholder="Mobile Number" name="userType" value="<?php echo "2" ?>" id='userType'>
{{ Form::submit('Save', array('class' => 'btn btn-lg btn-primary btn-block')); }}
</form>
{{ Form::close() }}
@stop