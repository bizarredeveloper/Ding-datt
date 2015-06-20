{{ HTML::style('bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('bootstrap/css/signin.css') }}
{{ Form::open(array('class' => 'form-signin')) }}
<style> 
    .error {

        color: blue;
    }
</style>


<h4>Request through JQuery</h4><br>
<h2 class="form-signin-heading">
    <a href='login'>Back to Login</a></h2>
<div id="stage" style="color:red"></div>
<div id="div1" style="color:green;display:none;">Registered Succesfully, You will be Rdirected in 10 Seconds to Login Page.</div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"  id="_token">
First Name :<input type="text" class="form-control" placeholder="First Name" name="firstname"  id="firstname">
Last Name :<input type="text" class="form-control" placeholder="Last Name" name="lastname"  id='lastname' >
Email : <input type="text" class="form-control" placeholder="Email Address" name="email"  id='email'>
Mobile : <input type="text" class="form-control" placeholder="Mobile Number" name="mobile"  id='mobile'>
Password : <input type="password" class="form-control" placeholder="Password"  name="password"  id='password'>
Confirm Password : <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation"  id='password_confirmation'>    
<!--
        {{ Form::submit('Save', array('class' => 'btn btn-lg btn-primary btn-block')); }}
-->



<input type="button" id="driver" value="Register">

</form>
{{ Form::close() }}


<hr>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
$(document).ready(function ()
{

 $(':input').on('keydown', function (ev) {

     //if(ev.which===13 || ev.which===9 || ev.which===254 || ev.which===254)
     if (ev.which === 13)
     {
         $('#driver').trigger('click');
     }
 });
 $("#driver").click(function (event)

         // $("form").submit(function(event)
         {
             var firstname = $("#firstname").val();
             var lastname = $("#lastname").val();
             var email = $("#email").val();
             var mobile = $("#mobile").val();
             var password = $("#password").val();
             var password_confirmation = $("#password_confirmation").val();
             var _token = $("#_token").val();
             $.post("jquery_register",
                     {_token: _token, firstname: firstname, lastname: lastname, email: email, mobile: mobile, password: password, password_confirmation: password_confirmation},
             function (data)
             {
                 if (data != '')
                 {
                     //console.log(data) ;
                     obj = JSON.parse(data);
                     var error_string = '';
                     $.each(obj, function (entry) {
                         error_string += obj[entry] + '<br/>';
                     });
                     $('#stage').html(error_string);
                 }
                 else
                 {

                     $("#div1").fadeIn(1);
                     $("#div1").fadeOut(9000);


                     setTimeout(function () {
                         window.location.href = "login";
                     }, 10000);

//window.location.href = "login";
                     //location.reload();

                     //$('#stage').html('Success Saving data'); 
                 }
             });
         });
});
</script>