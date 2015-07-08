<?php
/* this controller for maintaining the login process */
class LoginController extends BaseController {

    public function Login() {
        $user_name = Input::get('username');
        $password = Input::get('password');
        $LoginData_email = ['email' => $user_name, 'password' => $password];
        $LoginData_user = ['username' => $user_name, 'password' => $password];

        $LoginData = Input::except(array('_token', 'terms', 'client_login'));
        $validator = Validator::make($LoginData, User::$loginrule);
        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";

        if ($validator->fails()) {
            if ($validator->messages()->first('username') == "The username field is required when email is .")
                $username = "The usename or email field is required.";
            else
                $username = $validator->messages()->first('username');
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', [$username, $validator->messages()->first('password')])->get()->toArray();

            foreach ($languageDetails as $key => $val) {
                if (in_array($val['ctrlCaptionId'], ['alert_enterusername', 'alert_invaliduserpass', 'alert_enteruseroremail']))
                    $er_data['username'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enterpassword']))
                    $er_data['password'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
            }
            return Redirect::to('/')->withInput()->with('er_data', $er_data);
        }
        elseif (Auth::attempt(array('email' => $user_name, 'password' => $password), (Input::has('terms') ? true : false)) || Auth::attempt(array('username' => $user_name, 'password' => $password), (Input::has('terms') ? true : false))) {

            $userid = Auth::user()->ID;
            $Response = array(
                'success' => '1',
                'message' => 'successfully Login',
                'userid' => $userid
            );
            $final = array("response" => $Response);
            $user_names = Auth::user()->firstname . " " . Auth::user()->lastname;
            if (Auth::user()->firstname != '')
                Session::put('login_user', $user_names);
            else
                Session::put('login_user', Auth::user()->username);
            ///Verify for active ////
            if (Auth::user()->status == 1) {
                return Redirect::intended('webpanel');
            } else {
                $admindetails = User::select('email')->where('ID', 1)->first();
                $er_data['username'] = "<span>Your account is inactive please contact admin ($admindetails->email)</span>";
                Session::flush();
                return Redirect::to('/')->withInput()->with('er_data', $er_data);
            }
        } else {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_invaliduserpass'])->get()->toArray();
            $er_data['username'] = "<span id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";
            return Redirect::to('/')->withInput()->with('er_data', $er_data);
        }
    }

    public function loginWithFacebook() {

        // get data from input
        $code = Input::get('code');
        // get fb service
        $fb = OAuth::consumer('Facebook');
        // check if code is valid
        // if code is provided get user data and sign in
        if (!empty($code)) {

            $token = $fb->requestAccessToken($code);

            $result = json_decode($fb->request('/me'), true);
            $user_id = User::select('id', 'facebook_id', 'facebookpage')->where('email', $result['email'])->first();
            if (isset($user_id)) {
                $user = User::find($user_id->id);
                if ($user->status == 1) {
                    Auth::login($user);
                    $updatedata['facebook_id'] = $result['id'];
                    $userregister = User::where('ID', $user_id->id)->update($updatedata);
                    Session::put('login_user', Auth::user()->firstname . ' ' . Auth::user()->lastname);
                    return Redirect::to('/webpanel');
                } else {
                    $admindetails = User::select('email')->where('ID', 1)->first();

                    $er_data['username'] = "<span>Your account is inactive please contact admin ($admindetails->email)</span>";
                    Session::flush();
                    return Redirect::to('/')->withInput()->with('er_data', $er_data);
                }
            } else {
                $updatedata['facebook_id'] = $result['id'];
                $updatedata['email'] = $result['email'];
                $updatedata['username'] = $result['email'];
                $updatedata['firstname'] = $result['first_name'];
                $updatedata['lastname'] = $result['last_name'];
                $updatedata['facebookpage'] = $result['link'];
                $updatedata['timezone'] = "EST";
                $updatedata['status'] = 1;
				$updatedata['dateformat'] = "mm/dd/yy";

                if ($result['gender'] == "male")
                    $updatedata['gender'] = "m";
                elseif ($result['gender'] == "female")
                    $updatedata['gender'] = "f";
                else
                    $updatedata['gender'] = "o";

                $userregister = User::create($updatedata);
                $user = User::find($userregister->ID);
                Auth::login($user);
                Session::put('login_user', Auth::user()->firstname . ' ' . Auth::user()->lastname);
                return Redirect::to('/webpanel');
            }
        }
        // if not ask for permission first
        else {
            // get fb authorization
            $url = $fb->getAuthorizationUri();
            return Redirect::to((string) $url);
        }
    }

    public function loginWithGoogle() {

        // get data from input
        $code = Input::get('code');

        // get google service
        $googleService = OAuth::consumer('Google');

        // check if code is valid
        // if code is provided get user data and sign in
        if (!empty($code)) {

            // This was a callback request from google, get the token
            $token = $googleService->requestAccessToken($code);

            // Send a request with it
            $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

            $user_id = User::select('id')->where('email', $result['email'])->first();
            if (isset($user_id)) {
                $user = User::find($user_id->id);
                if ($user->status == 1) {
                    Auth::login($user);
                    Session::put('login_user', Auth::user()->firstname . ' ' . Auth::user()->lastname);
                    return Redirect::to('/webpanel');
                } else {
                    $admindetails = User::select('email')->where('ID', 1)->first();
                    $er_data['username'] = "<span>Your account is inactive please contact admin ($admindetails->email)</span>";
                    Session::flush();
                    return Redirect::to('/')->withInput()->with('er_data', $er_data);
                }
            } else {
                $updatedata['email'] = $result['email'];
                $updatedata['username'] = $result['email'];
                $updatedata['firstname'] = $result['given_name'];
                $updatedata['lastname'] = $result['family_name'];
                $updatedata['timezone'] = "EST";
                $updatedata['status'] = 1;
				$updatedata['dateformat'] = "mm/dd/yy";
                $userregister = User::create($updatedata);
                $user = User::find($userregister->ID);
                Auth::login($user);
                Session::put('login_user', Auth::user()->firstname . ' ' . Auth::user()->lastname);
                return Redirect::to('/webpanel');
            }
        }
        // if not ask for permission first
        else {
            // get googleService authorization
            $url = $googleService->getAuthorizationUri();

            // return to google login url
            return Redirect::to((string) $url);
        }
    }

    public function loginWithTwitter() {

        // get data from input
        $token = Input::get('oauth_token');
        $verify = Input::get('oauth_verifier');

        // get twitter service
        $tw = OAuth::consumer('Twitter');

        // check if code is valid
        // if code is provided get user data and sign in
        if (!empty($token) && !empty($verify)) {

            // This was a callback request from twitter, get the token
            $token = $tw->requestAccessToken($token, $verify);

            // Send a request with it
            $result = json_decode($tw->request('account/verify_credentials.json'), true);

            $message = 'Your unique Twitter user id is: ' . $result['id'] . ' and your name is ' . $result['name'];
            echo $message . "<br/>";

            //Var_dump
            echo "<pre>";
            dd($result);
            echo "<pre>";
            //display whole array().
        }
        // if not ask for permission first
        else {
            // get request token
            $reqToken = $tw->requestRequestToken();

            // get Authorization Uri sending the request token
            $url = $tw->getAuthorizationUri(array('oauth_token' => $reqToken->getRequestToken()));

            // return to twitter login url
            return Redirect::to((string) $url);
        }
    }

    public function Logout() {
        Session::flush();
        return Redirect::to('/');
    }

    public function ForgotPassword() {

        return View::make('login/forgot')->with('admin', '0');
    }

    public function forgotadmin() {
        return View::make('login/forgotadmin')->with('admin', '1');
    }

    public function CreateUserLayout() {
        return View::make('login/createuser');
    }

    public function CreateUserProcess() {
        $UserData = Input::all();

        $validation = Validator::make($UserData, User::$rules);
        if ($validation->passes()) {
            User::create($UserData);
            return Redirect::to('createuser')->with('Message', 'User Details Saved Succesfully');
        } else {
            return Redirect::to('createuser')->withInput()->withErrors($validation->messages());
        }
    }

    public function ForgotPasswordProcess() {
        $UserData = Input::except(array('_token'));
        $requestusername = $UserData['username'];
        $admin = Input::get('admin');
        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";
        if ($requestusername == "") {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_enteruseroremail'])->get()->toArray();
            $er_data = "<span id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";
            return Redirect::to('/forgot')->with('Message', $er_data);
        }
        $UserDetails = User::where('username', $requestusername)->orWhere('email', $requestusername)->get()->toArray();
        if ($UserDetails) {
            $string = str_random(5);
            $passworddata = User::find($UserDetails[0]['ID']);
            if ($UserDetails[0]['ID'] != 1) {
                $email = $UserDetails[0]['email'];
                $username = $UserDetails[0]['username'];
                $passworddata->password = $string;
                $passworddata->save();
                Mail::send([], array('pass' => $string, 'email' => $email, 'username' => $username), function($message) use ($string, $email, $username) {
                    /* $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear {username}</div>
								
								<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your Forgot password request Received.Your Password details is<br><br>Username: {username} <br><br>Password: {password}  </div>
								
								<div style="margin:10px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
								</div>
								
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b; width:100%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; float:none; position:relative;  clear:both;  ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span></div>
							</body>'; */
							
							$mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%; width:100%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear {username}</div>
								
								<table width="100%"><tr style="height:10px;"><td></td></tr><tr><td style="height:30px;">
								Your Forgot password request Received.Your Password details is
								</td></tr>
								<tr><td style="height:30px;">Username: {username}</td></tr>
								<tr><td style="height:30px;">Password: {password}</td></tr>
								<tr><td style="height:45px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a></td></tr>
								<tr><td style="border-top:#005377 1px solid; height:30px;"><span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span></td></tr>
								</table>
							</div>
														
							
							</body>';

                    $mail_body = str_replace("{password}", $string, $mail_body);
                    $mail_body = str_replace("{username}", $username, $mail_body);

                    $message->setBody($mail_body, 'text/html');
                    $message->to($email);
                    $message->subject('DingDatt - Forgot password');
                });

                $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_sendpasssuccess'])->get()->toArray();
                $er_data = "<span style='color:green' id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";


                if ($admin == 1)
                    return Redirect::to('/forgot')->withInput()->with('Message', $er_data)->with('admin', 1);
                else
                    return Redirect::to('/forgot')->withInput()->with('Message', $er_data)->with('admin', 0);
            }else {
                $er_data = "<span style='color:green' >Admin password will set in seperate part</span>";
                return Redirect::to('/forgot')->withInput()->with('Message', $er_data)->with('admin', 0);
            }
        } else {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_useroremailnotfound'])->get()->toArray();
            $er_data = "<span id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";
            if ($admin == 1)
                return Redirect::to('/forgot')->withInput()->withInput()->with('Message', $er_data)->with('admin', 1);
            else
                return Redirect::to('/forgot')->withInput()->withInput()->with('Message', $er_data)->with('admin', 0);
        }

    }

    public function ForgotPasswordProcessforadmin() {
        $UserData = Input::except(array('_token'));
        $requestusername = $UserData['username'];
        $admin = Input::get('admin');
        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";
        if ($requestusername == "") {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_enteruseroremail'])->get()->toArray();
            $er_data = "<span id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";
            return Redirect::to('/forgot')->with('Messageadmin', $er_data)->with('admin', 1);
        }
        $UserDetails = User::where('username', $requestusername)->orWhere('email', $requestusername)->get()->toArray();
        if ($UserDetails) {
            $string = str_random(5);
            $passworddata = User::find($UserDetails[0]['ID']);
            $email = $UserDetails[0]['email'];
            $username = $UserDetails[0]['username'];

            if ($UserDetails[0]['ID'] == 1) {
                $passworddata->password = $string;
                $passworddata->save();
                Mail::send([], array('pass' => $string, 'email' => $email, 'username' => $username), function($message) use ($string, $email, $username) {
                    $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
                                                            <body style="font-family:Arial, sans-serif; margin:0px auto; padding:0px;">

							    <div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							    </div>
								
							    <div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%; width:100%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear {username}</div>
								
								<table width="100%"><tr style="height:10px;"><td></td></tr><tr><td style="height:30px;">
								Your Forgot password request Received.Your Password details is
								</td></tr>
								<tr><td style="height:30px;">Username: {username}</td></tr>
								<tr><td style="height:30px;">Password: {password}</td></tr>
								<tr><td style="height:45px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a></td></tr>
								<tr><td style="border-top:#005377 1px solid; height:30px;"><span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span></td></tr>
								</table>
							</div>
							</body>'; 

                    $mail_body = str_replace("{password}", $string, $mail_body);
                    $mail_body = str_replace("{username}", $username, $mail_body);

                    $message->setBody($mail_body, 'text/html');
                    $message->to($email);
                    $message->subject('DingDatt - Forgot password');
                });

                $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_sendpasssuccess'])->get()->toArray();
                $er_data = "<span style='color:green' id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";

                return Redirect::to('/forgotadmin')->withInput()->with('Messageadmin', $er_data)->with('admin', 1);
            } else {
                $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_sendpasssuccess'])->get()->toArray();
                $er_data = "<span style='color:green' >You are not a admin</span>";
                return Redirect::to('/forgotadmin')->with('Messageadmin', $er_data)->with('admin', 1);
            }
        } else {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_useroremailnotfound'])->get()->toArray();
            $er_data = "<span id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";
            if ($admin == 1)
                return Redirect::to('/forgotadmin')->withInput()->with('Messageadmin', $er_data)->with('admin', 1);
            else
                return Redirect::to('/forgotadmin')->withInput()->with('Messageadmin', $er_data)->with('admin', 0);
        }
    }

    public function admin() {
        return View::make('admin/adminlogin');
    }

    public function adminLogin() {
        $user_name = Input::get('username');
        $password = Input::get('password');
        $LoginData_email = ['email' => $user_name, 'password' => $password];
        $LoginData_user = ['username' => $user_name, 'password' => $password];

        $LoginData = Input::except(array('_token', 'terms', 'client_login'));
        $validator = Validator::make($LoginData, User::$loginrule);
        $lantyp = Session::get('language');
        if ($lantyp == "")
            $lantyp = "value_en";
        if ($validator->fails()) {
            if ($validator->messages()->first('username') == "The username field is required when email is .")
                $username = "The usename or email field is required.";
            else
                $username = $validator->messages()->first('username');
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', [$username, $validator->messages()->first('password')])->get()->toArray();

            foreach ($languageDetails as $key => $val) {
                if (in_array($val['ctrlCaptionId'], ['alert_enterusername', 'alert_invaliduserpass', 'alert_enteruseroremail']))
                    $er_data['username'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enterpassword']))
                    $er_data['password'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
            }
            return Redirect::to('/admin')->withInput()->with('er_data', $er_data);
        }
        elseif (Auth::attempt(array('email' => $user_name, 'password' => $password), (Input::has('terms') ? true : false)) || Auth::attempt(array('username' => $user_name, 'password' => $password), (Input::has('terms') ? true : false))) {
            $userid = Auth::user()->ID;
            if ($userid == 1) {

                $user_names = Auth::user()->firstname . " " . Auth::user()->lastname;
                if (Auth::user()->firstname != '')
                    Session::put('login_user', $user_names);
                else
                    Session::put('login_user', Auth::user()->username);
                return Redirect::intended('user');
            }
            else {

       
				Session::flush();
                return Redirect::to('/admin')->withInput()->with('Message', 'You are not a admin');
            }
        } else {
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('ctrlCaptionId', ['alert_invaliduserpass'])->get()->toArray();
            $er_data['username'] = "<span id='" . $languageDetails[0]['ctrlCaptionId'] . "'>" . $languageDetails[0][$lantyp] . "</span>";
            return Redirect::to('/admin')->withInput()->with('er_data', $er_data);
        }
    }

    public function adminlogout() {

        Session::flush();
        return Redirect::to('/admin');
    }

}