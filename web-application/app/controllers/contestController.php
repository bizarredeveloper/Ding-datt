<?php
require_once "dropbox-sdk/lib/Dropbox/autoload.php";

class contestController extends BaseController {

	//start of jquery register
	public function showcontest()
	{
		return View::make('contest/contest');
	}
	public function savecontest()
	{
	   
		$conteststart = Input::get('conteststartdate');
		$contestenddate = Input::get('contestenddate');
		$votingstartdate = Input::get('votingstartdate');
		$votingenddate = Input::get('votingenddate');
		$a=0;
		
		//if($conteststart<$contestenddate && $contestenddate<=$votingstartdate && $votingstartdate<$votingenddate){}else{  return "S"; }
		if($conteststart>$contestenddate)
		{ 
			$a=1;
			$er_data['contestenddate']='Contest end date should be greater than contest start date';
		}elseif(strtotime($contestenddate)>strtotime($votingstartdate)){
			 $a=1;
			 $er_data['votingstartdate']='Voting start date should be greater than or equal to contest end date';		
		}
		elseif($votingstartdate>$votingenddate){ 
			$a=1;
			$er_data['votingenddate']='Voting end date should be greater than voting start date';
		}
		
		if($a==1){ return Redirect::to('/contest')->withInput()->with('er_data', $er_data); }

	   $inputdetails = Input::except(array('_token','themephoto','sponsor','sponsorphoto','sponsorname'));
		if(Input::file('themephoto')!='')
		{
			$destinationPath_them = 'public/assets/upload/contest_theme_photo';
			$filename_them = Input::file('themephoto')->getClientOriginalName();
			$Image_them = str_random(8).'_'.$filename_them;
			$inputdetails['themephoto']= $Image_them;		
		}
		
		if(Auth::user()->ID==1) 
		{ 
				$inputdetails['sponsorname']=Input::get('sponsorname');		
				if(Input::file('sponsorphoto')!='')
				{
					$destinationPath_spons = 'public/assets/upload/sponsor_photo';
					$filename_spons = Input::file('sponsorphoto')->getClientOriginalName();
					$Image_spons = str_random(8).'_'.$filename_spons;
					$inputdetails['sponsorphoto']= $Image_spons;		
				}		
		}
		
		$lantyp = Session::get('language');
		if($lantyp=="")
		$lantyp="value_en";		
		$validation  = Validator::make($inputdetails, contestModel::$rules); 
		if ($validation->passes()) 
		{
			
			if(Auth::user()->ID==1){ 
			$admintimezone = User::where('ID',1)->get()->first();
			$inputdetails['conteststartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['conteststartdate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['contestenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['contestenddate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingstartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingstartdate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingenddate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			
			}else{
			$inputdetails['conteststartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['conteststartdate'])),Auth::user()->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['contestenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['contestenddate'])),Auth::user()->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingstartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingstartdate'])),Auth::user()->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingenddate'])),Auth::user()->timezone,'UTC', 'Y-m-d H:i:s');
			}
			
			
			$inputdetails['createdby']=Auth::user()->ID;
			if(Auth::user()->ID==1){ $inputdetails['visibility'] = Input::get('visibility'); }else{
			$inputdetails['visibility']="p";
			}
			
			$inputdetails['status']=1;
			$inputdetails['createddate']=$inputdetails['updateddate']=date('Y-m-d H:i:s');
			$file_them = Input::file('themephoto');
			$file_spons = Input::file('sponsorphoto');
			if(isset($inputdetails['sponsorphoto']))
			$uploadSuccess_spons = $file_spons->move($destinationPath_spons,$Image_spons);
			if(isset($inputdetails['themephoto']))
			$uploadSuccess_them = $file_them->move($destinationPath_them,$Image_them);
			$product = contestModel::create($inputdetails);
			$contest_id=$product->ID;
			$interest = Input::get('interest');
			$interest_length=sizeof(Input::get('interest'));
			if($interest_length > 0)
			{
				contestinterestModel::whereNotIn('category_id',$interest)->where('contest_id','=',$contest_id)->delete();
				for($i=0;$i<$interest_length;$i++)
				{	
					$interes['contest_id']=$contest_id;
					$interes['category_id']=$interest[$i];
					$userInterest=contestinterestModel::where('contest_id',$contest_id)->where('category_id',$interest[$i])->lists('category_id');
					if(count($userInterest) < 1)
					{
						contestinterestModel::create($interes);
					}
					unset($interes);
				}
			}
			
			$private_cont['contest_id']=$contest_id;
			$private_cont['user_id']=Auth::user()->ID;
			$private_cont['requesteddate']=date('Y-m-d H:i:s');
			$private_cont['status']=1;
			
			if(Auth::user()->ID==1){
				if(Input::get('visibility')=='p') {  privateusercontestModel::create($private_cont); }
			}
			else{			
				privateusercontestModel::create($private_cont);
			}
			return Redirect::to('contest_info/'.$product->ID);
		}
		else
		{	
			if($validation->messages()->first('contest_name')=="The contest name field is required.")
			$er_msg_con_name="The Contest Name field is required.";
			else
			$er_msg_con_name=$validation->messages()->first('contest_name');
			
			if($validation->messages()->first('conteststartdate')=="The conteststartdate field is required.")
			$er_msg_con_start="The Contest Start Date field is required.";
			else
			$er_msg_con_start=$validation->messages()->first('conteststartdate');
			
			if($validation->messages()->first('contestenddate')=="The contestenddate field is required.")
			$er_msg_con_end="The Contest End Date field is required.";
			else
			$er_msg_con_end=$validation->messages()->first('contestenddate');
			
			if($validation->messages()->first('votingstartdate')=="The votingstartdate field is required.")
			$er_msg_vote_start="The Voting Start Date field is required.";
			else
			$er_msg_vote_start=$validation->messages()->first('votingstartdate');
			
			if($validation->messages()->first('votingenddate')=="The votingenddate field is required.")
			$er_msg_vote_end="The Voting End Date field is required.";
			else
			$er_msg_vote_end=$validation->messages()->first('votingenddate');
			
			if($validation->messages()->first('noofparticipant')=="The noofparticipant field is required.")
			$er_msg_noof_part="The No of Participant field is required.";
			else
			$er_msg_noof_part=$validation->messages()->first('noofparticipant');
			
			if($validation->messages()->first('contesttype')=="The contesttype field is required.")
			$er_msg_con_type="The Contest Type field is required.";
			else
			$er_msg_con_type=$validation->messages()->first('contesttype');
			
			if($validation->messages()->first('themephoto')=="The themephoto field is required.")
			$er_msg_them="The Contest Image field is required.";
			else
			$er_msg_them=$validation->messages()->first('themephoto');
			
			$languageDetails = languageModel::select($lantyp,'ctrlCaptionId')->whereIn('value_en',[$er_msg_con_name,$er_msg_con_start,$er_msg_con_end,$er_msg_vote_start,$er_msg_vote_end,$er_msg_noof_part,$er_msg_con_type,$er_msg_them])->get()->toArray();
			//return $validation->messages();
			foreach ($languageDetails as $key=>$val)
			{
			
				if(in_array($val['ctrlCaptionId'],['alert_entercontestname','alert_alreadycontestname']))
					$er_data['contest_name']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_enterconteststartdate']))
					$er_data['conteststartdate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_contestenddate']))
					$er_data['contestenddate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_votingstartdate']))
					$er_data['votingstartdate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_votingenddate']))
					$er_data['votingenddate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_enternoofpartis']))
					$er_data['noofparticipant']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_entercontesttype']))
					$er_data['contesttype']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_themephoto']))
					$er_data['themephoto']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
			}
			return Redirect::to('/contest')->withInput()->with('er_data', $er_data);
		}	
	}
	public function edit_contest($id)
	{
		return View::make('contest/edit_contest')->with('contest_id',$id);
	}
	public function update_contest()
	{
		$inputdetails = Input::except(array('_token','themephoto','sponsor','sponsorphoto','interest','contest_id','client_login','enable'));
		$contest_id=Input::get('contest_id');
		
		if(Input::file('themephoto')!='')
		{
			$destinationPath_them = 'public/assets/upload/contest_theme_photo';
			$filename_them = Input::file('themephoto')->getClientOriginalName();
			$Image_them = str_random(8).'_'.$filename_them;
			$inputdetails['themephoto']= $Image_them;		
		}
		$lantyp = Session::get('language');
		if($lantyp=="")
		$lantyp="value_en";
		$rules = array(
                    'contest_name'       => 'required|unique:contest,contest_name,'.$contest_id,
					'conteststartdate'   => 'required',
					'contestenddate'     => 'required',
					'votingstartdate'    => 'required',
					'votingenddate'  => 'required',
					'noofparticipant' =>'required',
					'contesttype' =>'required',	
                	) ;
					
		if(Auth::user()->ID==1) 
		{ 
				
				 $inputdetails['sponsorname']=Input::get('sponsorname');
					//return Input::file('sponsorphoto');
				if(Input::file('sponsorphoto')!='')
				{
					$destinationPath_spons = 'public/assets/upload/sponsor_photo';
					$filename_spons = Input::file('sponsorphoto')->getClientOriginalName();
					$Image_spons = str_random(8).'_'.$filename_spons;
					$inputdetails['sponsorphoto']= $Image_spons;		
				}

				
		}
		
		$usertimezone = contestModel::select('user.email','user.timezone','user.firstname','user.lastname','user.username','contest.contest_name','contest.themephoto')->LeftJoin('user','user.ID','=','contest.createdby')->where('contest.ID',$contest_id)->first();
		
		$validation  = Validator::make($inputdetails, $rules);
		if ($validation->passes()) 
		{
			
			if(Auth::user()->ID==1){ 
			$admintimezone = User::where('ID',1)->get()->first();
			$inputdetails['conteststartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['conteststartdate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['contestenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['contestenddate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingstartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingstartdate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingenddate'])),$admintimezone->timezone,'UTC', 'Y-m-d H:i:s');
			
			}else{
			$inputdetails['conteststartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['conteststartdate'])),$usertimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['contestenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['contestenddate'])),$usertimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingstartdate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingstartdate'])),$usertimezone->timezone,'UTC', 'Y-m-d H:i:s');
			$inputdetails['votingenddate']=timezoneModel::convert(date('Y-m-d H:i:s',strtotime($inputdetails['votingenddate'])),$usertimezone->timezone,'UTC', 'Y-m-d H:i:s');
			}
			
			//$inputdetails['visibility']="p";
			
			if(Auth::user()->ID==1){ $inputdetails['visibility']=Input::get('visibility'); }else{ $inputdetails['visibility']="p"; }
			
			$interest = Input::get('interest');
			$interest_length=sizeof(Input::get('interest'));
			if($interest_length > 0)
			{
				contestinterestModel::whereNotIn('category_id',$interest)->where('contest_id','=',$contest_id)->delete();
				for($i=0;$i<$interest_length;$i++)
				{	
					$interes['contest_id']=$contest_id;
					$interes['category_id']=$interest[$i];
					$userInterest=contestinterestModel::where('contest_id',$contest_id)->where('category_id',$interest[$i])->lists('category_id');
					if(count($userInterest) < 1)
					{
						contestinterestModel::create($interes);
					}
					unset($interes);
				}
			}
			else
			{
				contestinterestModel::where('contest_id','=',$contest_id)->delete();
			}
			$file_them = Input::file('themephoto');
			$file_spons = Input::file('sponsorphoto');
			if(isset($inputdetails['sponsorphoto']))
			$uploadSuccess_spons = $file_spons->move($destinationPath_spons,$Image_spons);
			if(isset($inputdetails['themephoto']))
			$uploadSuccess_them = $file_them->move($destinationPath_them,$Image_them);
			contestModel::where('ID', $contest_id)->update($inputdetails);
			if(Auth::user()->ID==1){
			
				 if($usertimezone->firstname!=''){  $name = $usertimezone->firstname.' '.$usertimezone->lastname; } else{
				  $name = $usertimezone->username;
				 }

				$contestname = 	$usertimezone->contest_name;
				$themephoto = $usertimezone->themephoto;	
				$email = $usertimezone->email;				
				
					if($usertimezone->createdby!=1)
					{
					
					
					
						$contestdetailsformail = contestModel::select('user.email','user.timezone','user.firstname','user.lastname','user.username','contest.contest_name','contest.themephoto','contest.description','contest.noofparticipant','contest.conteststartdate','contest.contestenddate','contest.votingstartdate','contest.votingenddate','contest.contesttype','contest.visibility','contest.status','contest.sponsorname')->LeftJoin('user','user.ID','=','contest.createdby')->where('contest.ID',$contest_id)->first();

				     if($contestdetailsformail->status==1) $status ="Active"; else $status = "Inactive";
					 if($contestdetailsformail->visibility=="p") $visibility="Private"; else $visibility="Public";
					 $timezone = $contestdetailsformail->timezone;
					 if($contestdetailsformail->contesttype=="p") $contesttype = "Photo"; elseif($contestdetailsformail->contesttype=="v") $contesttype = "Video"; else $contesttype = "Topic";
					 
						$details ='<div styel"float:left;">
						<table width="500" height="95" border="1" style="margin-bottom:10px;float:left;font-family:Helvetica Neue, Helvetica, Arial, sans-serif;">
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Contest Name:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.$contestdetailsformail->contest_name.'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Contest Image:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;"><img src='. URL::to('public/assets/upload/contest_theme_photo/'.$contestdetailsformail->themephoto).' width="150" height="150" /></td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Contest Type:</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.$contesttype.'</td>
					  </tr>
					   <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Status </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.$status.'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Contest Start date </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.timezoneModel::convert($contestdetailsformail->conteststartdate, 'UTC',$timezone, 'd-m-Y H:i:s').'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Contest end date </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.timezoneModel::convert($contestdetailsformail->contestenddate, 'UTC',$timezone, 'd-m-Y H:i:s').'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Voting start date </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.timezoneModel::convert($contestdetailsformail->votingstartdate, 'UTC',$timezone, 'd-m-Y H:i:s').'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Voting end date </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.timezoneModel::convert($contestdetailsformail->votingenddate, 'UTC',$timezone, 'd-m-Y H:i:s').'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Visibility </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.$visibility.'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">Sponsor </td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.$contestdetailsformail->sponsorname.'</td>
					  </tr>
					  <tr>
						<td style="font-size:12px;color: #3BBA00;font-weight:bold;">No of participant</td>
						<td style="font-size:12px;color: #5d5d5d;font-weight:bold;">'.$contestdetailsformail->noofparticipant.'</td>
					  </tr>
					  					  				  
					</table>
					</div>';
				
					 
					 $this->editcontestmail($email,$name,$contestname,$themephoto,$contest_id,$details);
					}
					
				$er_data['Message']='Updated successfully';
			
			return Redirect::to('edit_contest/'.$contest_id)->with('er_data', $er_data); 
			}else{
			return Redirect::to('/contest_info/'.$contest_id); }
		}
		else
		{	
			if($validation->messages()->first('contest_name')=="The contest name field is required.")
			$er_msg_con_name="The Contest Name field is required.";
			else
			$er_msg_con_name=$validation->messages()->first('contest_name');
			
			if($validation->messages()->first('conteststartdate')=="The conteststartdate field is required.")
			$er_msg_con_start="The Contest Start Date field is required.";
			else
			$er_msg_con_start=$validation->messages()->first('conteststartdate');
			
			if($validation->messages()->first('contestenddate')=="The contestenddate field is required.")
			$er_msg_con_end="The Contest End Date field is required.";
			else
			$er_msg_con_end=$validation->messages()->first('contestenddate');
			
			if($validation->messages()->first('votingstartdate')=="The votingstartdate field is required.")
			$er_msg_vote_start="The Voting Start Date field is required.";
			else
			$er_msg_vote_start=$validation->messages()->first('votingstartdate');
			
			if($validation->messages()->first('votingenddate')=="The votingenddate field is required.")
			$er_msg_vote_end="The Voting End Date field is required.";
			else
			$er_msg_vote_end=$validation->messages()->first('votingenddate');
			
			if($validation->messages()->first('noofparticipant')=="The noofparticipant field is required.")
			$er_msg_noof_part="The No of Participant field is required.";
			else
			$er_msg_noof_part=$validation->messages()->first('noofparticipant');
			
			if($validation->messages()->first('contesttype')=="The contesttype field is required.")
			$er_msg_con_type="The Contest Type field is required.";
			else
			$er_msg_con_type=$validation->messages()->first('contesttype');
			
			$languageDetails = languageModel::select($lantyp,'ctrlCaptionId')->whereIn('value_en',[$er_msg_con_name,$er_msg_con_start,$er_msg_con_end,$er_msg_vote_start,$er_msg_vote_end,$er_msg_noof_part,$er_msg_con_type])->get()->toArray();
			//return $validation->messages();
			foreach ($languageDetails as $key=>$val)
			{
			
				if(in_array($val['ctrlCaptionId'],['alert_entercontestname','alert_alreadycontestname']))
					$er_data['contest_name']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_enterconteststartdate']))
					$er_data['conteststartdate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_contestenddate']))
					$er_data['contestenddate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_votingstartdate']))
					$er_data['votingstartdate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_votingenddate']))
					$er_data['votingenddate']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_enternoofpartis']))
					$er_data['noofparticipant']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
				elseif(in_array($val['ctrlCaptionId'],['alert_entercontesttype']))
					$er_data['contesttype']="<span id='".$val['ctrlCaptionId']."'>".$val[$lantyp]."</span>";
			}
			return Redirect::to('edit_contest/'.$contest_id)->with('er_data', $er_data)->with('old_data',$inputdetails);
		}	
	}
	public function showcontestinfo($id)
	{
		$contest_details=contestModel::where('ID', $id)->first();
		return View::make('contest/contestinfo')->with('contest_id',$id);
	}
	public function showmycontest()
	{
		$inputs= Input::all();
		return View::make('contest/my_contest')->with('inputs',$inputs);
	}
	public function showcontentlist()
	{
		return View::make('contest/contestlist');
	}
	public function join_contest()
	{
		$curdate = Carbon::now();
		$timezone = Input::get('timezone');
		$inputdetails = Input::except(array('_token','uploadfile','submitphoto','uploadtopic','timezone','topicvideo','topicphoto'));		
		$inputdetails['uploaddate']=$curdate;
		if(Input::file('uploadfile')!='')
		{
			$destinationPath = 'public/assets/upload/contest_participant_photo';
			$filename = Input::file('uploadfile')->getClientOriginalName();
			$Image = str_random(8).'_'.$filename;
			$inputdetails['uploadfile']= $Image;
			$validation  = Validator::make($inputdetails, contestparticipantModel::$rules);		
		}
		
		if(Input::get('uploadtopic')!='')
		{

			////////New requirements/////////////////				
			if(Input::file('topicphoto')!=''){
				$destinationtopicphoto='public/assets/upload/topicphotovideo';
				$topicphoto = Input::file('topicphoto')->getClientOriginalName();
				$topicImage = str_random(8).'_'.$topicphoto;
				$inputdetails['topicphoto']= $topicImage;			
			}
			
			
			if(Input::file('topicvideo')!=''){
				$destinationtopicvideo='public/assets/upload/topicphotovideo';
				$topicvideo = Input::file('topicvideo')->getClientOriginalName();
				$topicvideoname = str_random(8).'_'.$topicvideo;
				$inputdetails['topicvideo']= $topicvideoname;		
			
			}	

			
			$inputdetails['uploadtopic']=Input::get('uploadtopic'); //return $inputdetails;
			$validation  = Validator::make($inputdetails, contestparticipantModel::$topicrules);
		}
		//return $validation->messages();
		$user_id = Input::get('user_id');
		$contest_id = Input::get('contest_id');
		
		$verifyid = contestparticipantModel::where('contest_id',$contest_id)
				->where('user_id',$user_id)
			    ->get()->count();
		$contestcount = contestModel::select('noofparticipant')->where('ID',$contest_id)->get();	  
	    $participantcount = contestparticipantModel::where('contest_id',$contest_id)->get()->count();
		if($verifyid)
		{
			if ($validation->passes()) 
			{
				if(Input::file('uploadfile')!='') 
				{ 
					$file = Input::file('uploadfile');
					$uploadSuccess = $file->move($destinationPath,$Image);
					
					/// Dropbox //////////////
						/*$appInfo = \Dropbox\AppInfo::loadFromJsonFile("app_info.json");
						
						$webAuth = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
						$authorizeUrl = $webAuth->start();

						$accessToken = "LUu8h-uvauAAAAAAAAAAHbDw-bulVL7u8BoRAJtedc0-eCDY-Xj4Qxf1iGucUN7j";
						$dbxClient = new \Dropbox\Client($accessToken, "PHP-Example/1.0");

						$accountInfo = $dbxClient->getAccountInfo();
						
						//file_get_contents($file->getRealPath());
						
						
						$f = fopen($file->getRealPath(), "rb");
						$result = $dbxClient->uploadFile("/participant/".$Image, \Dropbox\WriteMode::add(), $f);
						fclose($f);
						
						$inputdetails['dropbox_path']=$this->fetchUrl('/participant/'.$inputdetails['uploadfile']); */
						
						/// Dropbox end ////////
										
				}
				
				if(Input::get('uploadtopic')!='')
		    {				
				if(Input::file('topicphoto')!=''){ 
					$topicphotofile = Input::file('topicphoto');
					$uploadSuccess = $topicphotofile->move($destinationtopicphoto,$topicImage);				
				}
				if(Input::file('topicvideo')!=''){ 
					$topicphotofile = Input::file('topicvideo');
					$uploadSuccess = $topicphotofile->move($destinationtopicvideo,$topicvideoname);				
				}
			}
			
				 $participant = contestparticipantModel::where('contest_id',$contest_id)
					->where('user_id',$user_id)
					->update($inputdetails);
				return Redirect::to("contest_info/".$contest_id)->with('tab','gallery');
			}
			else
			{
				return Redirect::to("contest_info/".$contest_id)->with('tab','join')->with('Massage',$validation->messages()->first());
			}
		}
		else
		{
			if($contestcount[0]['noofparticipant']==$participantcount && $contestcount[0]['noofparticipant']!=0)
			{
				return Redirect::to("contest_info/".$contest_id)->with('tab','join')->with('Massage','Participants Limit Exceeded');
			}
			else
			{			
				if ($validation->passes()) 
				{			
					if(Input::file('uploadfile')!='') 
					{ 
						$file = Input::file('uploadfile');
						$uploadSuccess = $file->move($destinationPath,$Image);
						
						/// Dropbox //////////////
						/* $appInfo = \Dropbox\AppInfo::loadFromJsonFile("app_info.json");
						
						$webAuth = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
						$authorizeUrl = $webAuth->start();

						$accessToken = "LUu8h-uvauAAAAAAAAAAHbDw-bulVL7u8BoRAJtedc0-eCDY-Xj4Qxf1iGucUN7j";
						$dbxClient = new \Dropbox\Client($accessToken, "PHP-Example/1.0");

						$accountInfo = $dbxClient->getAccountInfo();
						
						//file_get_contents($file->getRealPath());
						
						
						$f = fopen($file->getRealPath(), "rb");
						$result = $dbxClient->uploadFile("/participant/".$Image, \Dropbox\WriteMode::add(), $f);
						fclose($f);
						
						$inputdetails['dropbox_path']=$this->fetchUrl('/participant/'.$Image); */
						
						/// Dropbox end ////////
					}
					
							if(Input::get('uploadtopic')!='')
					{				
						if(Input::file('topicphoto')!=''){ 
							$topicphotofile = Input::file('topicphoto');
							$uploadSuccess = $topicphotofile->move($destinationtopicphoto,$topicImage);				
						}
						if(Input::file('topicvideo')!=''){ 
							$topicphotofile = Input::file('topicvideo');
							$uploadSuccess = $topicphotofile->move($destinationtopicvideo,$topicvideoname);				
						}
					}
					
					
					$participant = contestparticipantModel::create($inputdetails);
					return Redirect::to("contest_info/".$contest_id)->with('tab','gallery');	
				}
				else
				{	
					return Redirect::to("contest_info/".$contest_id)->with('tab','join')->with('Massage',$validation->messages()->first());
				}
			}
		}
	}
	public function showcontentlistdetails()
	{

	$contestname = Input::get('contestname');
	
	$inputdetails = Input::except(array('_token'));
	$contestlisttype =Input::get('contestlisttype');
	$contesttype=Input::get('contesttype');
	$loggeduserid = Input::get('loggeduserid');
	$currentdate = date('Y-m-d h:i:s');
	
if($contestlisttype=='current')
{
	$contestDetailscount = contestModel::where('conteststartdate', '<=', $currentdate)
	->where('contestenddate', '>=', $currentdate)
	->where('contesttype',$contesttype)
	->where('status','1')
	->where('visibility','u')
	->orWhere('votingstartdate', '<=', $currentdate)
	->where('votingenddate', '>=', $currentdate)
	->get()->count();
	
	
	$contestDetails = contestModel::where('conteststartdate', '<=', $currentdate)
	->where('contestenddate', '>=', $currentdate)
	->where('contesttype',$contesttype)
	->where('status','1')->where('visibility','u')
	->orWhere('votingstartdate', '<=', $currentdate)
	->where('votingenddate', '>=', $currentdate)
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
	->select('contestparticipant.contest_id as participatedcontest','contest.*')
	->get(); 
     
}
else if($contestlisttype=='upcoming')
{
	 
	$contestDetailscount = contestModel::where('conteststartdate', '>', $currentdate)->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get()->count();
		
	$contestDetails = contestModel::where('conteststartdate', '>', $currentdate)->where('contesttype',$contesttype)->where('status','1')->where('visibility','u')->get(); 
	
}
else if($contestlisttype=='archive')
{
	$contestDetailscount = contestModel::where('contestenddate', '<', $currentdate)
	->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('status','1')
	->where('visibility','u')
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
	->get()->count();
		
	$contestDetails = contestModel::where('contestenddate', '<', $currentdate)
	->select('contest.*','contestparticipant.ID as contestparticipantid')
	->where('votingenddate', '<', $currentdate)
	->where('contesttype',$contesttype)
	->where('status','1')
	->where('visibility','u')
	->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')
	->get();
	
}
else if($contestlisttype=='private')  
{

    $contestDetailscount = contestModel::where('visibility','p')
			->select('contest.ID','contest_name','description','noofparticipant','contestparticipant.ID as contestparticipantid')
			->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
			->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')			
            ->where('private_contest_users.user_id',$loggeduserid)
            ->get()->count();
			
	 $contestDetails = contestModel::where('visibility','p')
			->select('contest.ID','contest_name','description','noofparticipant','contestparticipant.ID as contestparticipantid')
			->leftJoin('private_contest_users', 'private_contest_users.contest_id', '=', 'contest.ID')
			->leftJoin('contestparticipant', 'contestparticipant.contest_id', '=', 'contest.ID')			
            ->where('private_contest_users.user_id',$loggeduserid)
            ->get();
}
}

public function contestgallery()
{
	return View::make('contest/contestgallery');

}	
public function showcontestinfo1($id)
{
	$contest_details=contestModel::where('ID', $id)->first();
	return View::make('contest/contestinfo1')->with('contest_id',$id);
}
public function contesttab()
{
$contest_id = Input::get('contest_id');
$contest_partipant_id = Input::get('contest_partipant_id');
return  Redirect::to("contest_info/".$contest_id)->with('tab','gallery')->with('contest_partipant_id',$contest_partipant_id);
}

public function contesttabeithreport(){

$contest_id = Input::get('contest_id');
$contest_partipant_id = Input::get('contest_partipant_id');

$contestparticipantid = Input::get('contestparticipantid');
		$reportflagid  = Input::get('reportflagid');
		$reportflagcnt = reportflagModel::where('ID',$reportflagid)->get()->count();
		$inputdetails['action_taken']= 1;
		if($reportflagcnt) { 
			reportflagModel::where('ID',$reportflagid)->update($inputdetails); 
			$data['message']='Action taken for this report';
			}	
			
return  Redirect::to("contest_info/".$contest_id)->with('tab','gallery')->with('contest_partipant_id',$contest_partipant_id);

}

public function contesttabwithoutid(){
$contest_id = Input::get('contest_id');

return  Redirect::to("contest_info/".$contest_id)->with('tab','gallery');

}
public function contestinforesponsive()
{
$contest_id = $_GET['contest_id'];
$tabname = $_GET['tabname'];
return  Redirect::to("contest_info/".$contest_id)->with('tab',$tabname);
}
public function contestinfosubtabresponsive()
{
$contest_id = $_GET['contest_id'];
$tabname = $_GET['tabname'];
$subtabresponsive =$_GET['subtab'];
return  Redirect::to("contest_info/".$contest_id)->with('tab',$tabname)->with('subtab',$subtabresponsive);
}
public function mycontestresponsive()
{
$tabname = $_GET['tabname'];

if($tabname=='participate')
{
$created_user=Auth::user()->ID;
	$participants=contestparticipantModel::where('user_id',$created_user)->lists('contest_id');
	if(isset($inputs['tsearch1'])&&$inputs['tsearch1']!='')
	$photocontest=contestModel::whereIn('ID',$participants)->where('contest_name','like',"%".$inputs['tsearch1']."%")->get();
	else
	$photocontest=contestModel::whereIn('ID',$participants)->get();
	$contestcount=count($photocontest);


}
else{ 

$created_user=Auth::user()->ID;
	if(isset($inputs['tsearch2'])&&$inputs['tsearch2']!='')
	$photocontest=contestModel::where('createdby',$created_user)->where('contest_name','like',"%".$inputs['tsearch2']."%")->get();
	else
	$photocontest=contestModel::where('createdby',$created_user)->get();
	$contestcount=count($photocontest);


}

    $contestcount=count($photocontest);
		$return_string ="";
		for($i=0;$i<$contestcount;$i++)
		{

			$return_string .= "<div class='crsl-item' >
			  <div class='thumbnail'>
				<a href='".URL::to('contest_info/'.$photocontest[$i]['ID'])."' >
					<img src='". URL::to('public/assets/upload/contest_theme_photo/'.$photocontest[$i]['themephoto'])."' alt='nyc subway'>
					</a>
				<span class='postdate'>Ends on : ". timezoneModel::convert($photocontest[$i]['contestenddate'],'UTC',Auth::user()->timezone, 'Y-m-d h:i a') ."</span>
			  </div>
			  <h3><a href='".URL::to('contest_info/'.$photocontest[$i]['ID'])."'>".$photocontest[$i]['contest_name']."</a></h3>
			</div>";
		}
		return $return_string."||".$contestcount;

}
 	
	function  report(){
		$reporteddata = Input::get('reporteddata');
		$participantid = Input::get('participantid');
		$authuserid = Auth::user()->ID;
		$description  = Input::get('');
		
		$participant_details = contestparticipantModel::select('user_id','contest_id')->where('ID',$participantid)->first();
		
		$inputdetails['contest_participant_id'] = $participantid;
		$inputdetails['report_description'] = $reporteddata;
		$inputdetails['report_userid']= $authuserid;
		$inputdetails['postedby_userid']= $participant_details['user_id'];
		$inputdetails['contest_id'] = $participant_details['contest_id'];
		$inputdetails['createddate'] = date('Y-m-d h:i:s');

		//reportflagModel::where()
		
		$validation  = Validator::make($inputdetails, reportflagModel::$rules); 
		if ($validation->passes()) 
			{			
			$created = reportflagModel::create($inputdetails);
			if($created) return 1;
			}
	}

	function fetchUrl($path)
    {
        //sadly, https doesn't work out of the box on windows for functions
        //like file_get_contents, so let's make this easy for devs

	$dropbox_root = 'dropbox';

	$dropbox_oauth_token = 'AAD8XwoPtQc0zcXn2Pv3adcMbVVe_pgroi-0NniA6dUF7w';

	$dropbox_oauth_consumer_key = 'z6tj74qaywh91i9';

	$dropbox_access_token = 'LUu8h-uvauAAAAAAAAAAHbDw-bulVL7u8BoRAJtedc0-eCDY-Xj4Qxf1iGucUN7j';
		
		
    $url = "https://api.dropbox.com/1/media/<root>/<path>";
	$fields = array('root'=>$dropbox_root,'path'=>$path,'oauth_token'=>$dropbox_oauth_token,'oauth_consumer_key'=>$dropbox_oauth_consumer_key,'access_token'=>$dropbox_access_token);
	$fields_string = '';
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result,true);
	return $result['url'];
	
	
	//// Preview link //////////////
	
$app_key = 'z6tj74qaywh91i9';
$app_secret = 'xxxxxxxxxxxxxxxxxxxx';
$user_oauth_access_token = 'AAD8XwoPtQc0zcXn2Pv3adcMbVVe_pgroi-0NniA6dUF7w';
$user_oauth_access_token_secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';    

$ch = curl_init(); 

$headers = array( 'Authorization: OAuth oauth_version="1.0", oauth_signature_method="PLAINTEXT"' );

$params = array('short_url' => 'false', 'oauth_consumer_key' => $app_key, 'oauth_token' => $user_oauth_access_token, 'oauth_signature' => $app_secret.'&'.$user_oauth_access_token_secret);

curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
curl_setopt( $ch, CURLOPT_URL, 'https://api.dropbox.com/1/shares/'.$dir );

/*
* To handle Dropbox's requirement for https requests, follow this:
* http://artur.ejsmont.org/blog/content/how-to-properly-secure-remote-api-calls-from-php-application
*/
curl_setopt( $ch, CURLOPT_CAINFO,getcwd() . "\dropboxphp\cacert.pem");
curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE);

curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
$api_response = curl_exec($ch);

if(curl_exec($ch) === false) {
    echo 'Curl error: ' . curl_error($ch);
}

$json_response = json_decode($api_response, true);

/* Finally end with the download link */
$download_url = $json_response['url'].'?dl=1';
       
    }
	/*   admin send to contest owner */
	function editcontestmail($email,$name,$contestname,$contestimage,$contest_id,$details){
	
		Mail::send([],
					array('email' => $email,'name'=>$name,'contestname'=>$contestname,'contest_id'=>$contest_id,'contestimage'=>$contestimage,'details'=>$details), function($message) use ($email,$name,$contestname,$contest_id,$contestimage,$details)
					{
						 $mail_body = '<style>.thank{text-align:center; width:100%;}
								.but_color{color:#ffffff;}
								.cont_name{width:100px;}
								.cont_value{width:500px;}
								
								</style>
						 <body style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; margin:0px auto; padding:0px;">

							<div style="margin:0px auto;background:#e5e5e5;float:left;	width:98%;	height:30px;margin:0px 1%;  border-bottom:#005377 1px solid;vertical-align: text-middle;">
								&nbsp;&nbsp;<a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/images/logo.png').'" style="margin-top:3px;width: 100px;height: 20px;line-height:20px;" /></a>&nbsp;&nbsp;
							</div>
							<div style="background:#ffffff;float:left;padding:10px 20px;margin:1px 1%;" >
								<div class="thank" style="font-size:16px;color: #078AC2;font-weight:bold;float:left;width:100%;margin-top:10px;text-align:left;">Dear '.$name.'</div>
								
								<div style="font-size:12px;	color: #000000;	float:left;padding:10px 2px;width:100%;margin:15px;">Your contest <b>"'.$contestname.'"</b> is changed by admin  </div>
								
								'.$details.'
								
								<div style="margin:10px;"><a href="'.URL::to('contest_info/'.$contest_id).'"><img src="'.URL::to('assets/inner/images/vist_dingdatt.png').'" width="120" height="30" /></a>
								</div>

								
							</div>
														
							<div style="font-size:12px; margin-top:10px;color: #5b5b5b;/*	background:#e5e5e5;*/width:95%;vertical-align: text-middle;height:30px;margin:0% 1%;padding:0px 15px; border-top:#005377 1px solid; border-bottom:5px solid background:#e5e5e5;line-height:25px; ">
							<span style="font-size:12px;color: #5b5b5b;padding:0px 10px;line-height:22px;text-align:center;">This is auto generated mail and do not reply to this mail.</span>
							</body>';
							
						$message->setBody($mail_body, 'text/html');
						$message->to($email);
						$message->subject('Dingdatt-contest information');
					});  
	}
}
?>