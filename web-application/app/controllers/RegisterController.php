<?php
/* user registration */
class RegisterController extends BaseController {

    //start of jquery register
    public function jquery_register() {
        $firstname = Input::get('firstname');
        $lastname = Input::get('lastname');
        $email = Input::get('email');
        $mobile = Input::get('mobile');
        $password = Input::get('password');
        //$password = Crypt::encrypt(1);    // we can encrypt if we require
        $data = Input::except(array('_token'));
        $rule = array(
            'username' => 'required|unique:user',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:5|confirmed',
            'dateofbirth' => 'required'
                );
        $validator = Validator::make($data, $rule);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return json_encode($validator->messages());  //php encoded value
        } else {
            DB::insert('insert into user (firstname, lastname, email, password, mobile) values (?, ?, ?, ?, ?)', array($firstname, $lastname, $email, $password, $mobile));
            return '';
        }
    }

    //end of jquery register
//start of laravel register
    public function laravel_register() {
        $username = Input::get('username');
        $email = Input::get('email');
        $pass = Input::get('password');
        $password = Hash::make(Input::get('password'));
        $dateofbirth = Input::get('dateofbirth');
        $data = Input::except(array('_token', 'client_login'));
        // $validator = Validator::make($data,$rule);
        $validator = Validator::make($data, ProfileModel::$webrule);
        $lantyp = Session::get('language');
        $data['password'] = $password;
        if ($validator->fails()) { //return $validator->messages();
            if ($validator->messages()->first('terms') != "")
                $terms = "Accept terms";
            else
                $terms = "";
            $languageDetails = languageModel::select($lantyp, 'ctrlCaptionId')->whereIn('value_en', [$validator->messages()->first('username'), $validator->messages()->first('email'), $validator->messages()->first('password'), $validator->messages()->first('dateofbirth'), $terms])->get()->toArray();
            foreach ($languageDetails as $key => $val) {
                if (in_array($val['ctrlCaptionId'], ['alert_enterusername', 'alert_alreadyuser']))
                    $er_data['username'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enteremail', 'alert_validemail', 'alertr_emailalready']))
                    $er_data['email'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enterpassword', 'alert_minpass5']))
                    $er_data['password'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                elseif (in_array($val['ctrlCaptionId'], ['alert_enterdob']))
                    $er_data['dateofbirth'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
                else if (in_array($val['ctrlCaptionId'], ['alert_enterterms']))
                    $er_data['terms'] = "<span id='" . $val['ctrlCaptionId'] . "'>" . $val[$lantyp] . "</span>";
            }
            //return $er_data;
            return View::make('user/register/userregister')->with('er_data', $er_data)->with('old_value', $data);
        }
        else {
            unset($data['terms']);
            $lantyp = Session::get('language');
            $data['timezone'] = "EST";
            $data['status'] = 1;
            $userregister = ProfileModel::create($data);
            Mail::send([], array('pass' => $pass, 'email' => $email, 'username' => $username), function($message) use ($pass, $email, $username) {
                //$user = MailTemplate::find(1);
                //$mail_body = $user->MailContent;
                //$mail_body = str_replace("{password}", Session::get('sess_string'), $mail_body);

                /* $mail_body = "Dear {username},<br><br>Your DingDatt Registration successfully completed.Your Login details are<br><br>Username: {username}<br>Password: {password} <br><br> Thank You, <br><br>Regards,<br>DingDatt";
                  $mail_body = str_replace("{password}", $pass, $mail_body);
                  $mail_body = str_replace("{username}", $username, $mail_body); */

                $mail_body = '<style>.thank{text-align:center; width:100%;}
					.but_color{color:#ffffff;}
					.cont_name{width:100px;}
					.cont_value{width:500px;}
					
					</style>
			 <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

				<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
					&nbsp;&nbsp;<a href="' . URL() . '"><img src="' . URL::to('assets/images/logo.png') . '" style="margin-top:3px; line-height:20px;" /></a>&nbsp;&nbsp;
				</div>
				<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
					<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear ' . $username . '</div>
					
					<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your DingDatt Registration successfully completed.Your Login details are<br><br>Username: ' . $username . '<br>Password: ' . $pass . '
			 </div>
					
					<div style="margin:10px;"><a href="' . URL() . '"><img src="' . URL::to('assets/inner/images/vist_dingdatt.png') . '" width="120" height="30" /></a>
					</div>
				</div>
											
				<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
				
				</body>';



                $message->setBody($mail_body, 'text/html');
                $message->to($email);
                $message->subject('DingDatt Registration');
            });
            $labelname = ['txt_userdetaile_save_msg'];
            $languageDetails = languageModel::select($lantyp)->whereIn('ctrlCaptionId', $labelname)->get()->toArray();
            return View::make('user/register/userregister')->with('Message', $languageDetails[0][$lantyp]);

            //return Redirect::to('userregister')->with('Message', );
            // return Redirect::to('userregister')->with('Message', $languageDetails[0][$lantyp]);
        }
    }

//end of laravel register	
}

// end of the controller
?>