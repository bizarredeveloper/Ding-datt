<?php
/* This controller search the user and also view the register member  */
class memberController extends BaseController
{
    public function addmembertogroup($data=Null)
    {	
        $searcheduser='';
        return View::make('user/view/registermember')->with('group_id',$data)->with('searcheduser',$searcheduser);	
    }
    public function usersearch($data=Null)
    {
        $inputs = Input::get();
        $groupmemberid = groupmemberModel::where('group_id',$data)->get()->lists('user_id');
        $groupmemberuserid = ProfileModel::whereNotIn('user.ID', $groupmemberid)->lists('ID');
        $searcheduser = Input::get('usersearch');	
        $groupmemberuseridlist=ProfileModel::select('profilepicture','firstname','lastname','username','ID')->where('username','like','%'.$searcheduser.'%')->whereIn('user.ID', $groupmemberuserid)->Orwhere('firstname','like','%'.$searcheduser.'%')->whereIn('user.ID', $groupmemberuserid)->where('user.status',1)->get();		
        return View::make('user/view/registermember')->with('group_id',$data)->with('searcheduser',$searcheduser)->with('savegroupmembers',$groupmemberuseridlist);
    }
	public function groupmemberback(){
	
	$contest_id = $_GET['contest_id'];
	$data = $_GET['group_id'];
	if($contest_id!='no')
	{
		//return View::make('group/groupmember')->with('group_id', $data)->with('showjoinbtn', 'no')->with('contest_id', $contest_id);
		return Redirect::to('contest_info/'.$contest_id);
	}else{
		 //return View::make('group/groupmember')->with('group_id', $data)->with('showjoinbtn', 'yes')->with('contest_id', 'no');
		 return Redirect::to('group');
	}
	
	
	}
	/* Auto complete search */
	public function getsearchdetails(){
	
		$term = $_GET[ "term" ];
		
		$group = groupModel::select('group.groupname','group.groupimage','group.ID as gid')->where('status',1)->where('groupname','like','%'.$term.'%')->take(5)->get();
		
		$profiledetail = ProfileModel::select('user.firstname','user.lastname','user.username','user.profilepicture','user.ID as uid' )->where('username','like','%'.$term.'%')->Orwhere('firstname','like','%'.$term.'%')->where('status', 1)->take(5)->get();
		
		
		//$detailed = json_encode(array_merge(json_decode($profiledetail, true),json_decode($group, true)));		

		
		$return_string = '<ul id="country-list">';
		
		if(count($profiledetail)>0) $return_string.='<li style="/*width:218px; height:40px;*/ padding: 7px 3px; color:#FFFFFF; background:#0896d6 ; font-size:14px;"><strong>Users</strong> </li>';
		for($k=0; $k<count($profiledetail);$k++){
		
		if($profiledetail[$k]['firstname']!=''){ $profiledetail[$k]['name']= $profiledetail[$k]['firstname'].' '.$profiledetail[$k]['lastname'];}else{ $profiledetail[$k]['name']= $profiledetail[$k]['username']; }
		
		if($profiledetail[$k]['profilepicture']!=''){ $profimg =url().'/public/assets/upload/profile/'.$profiledetail[$k]['profilepicture']; }else{
		$profimg =url().'/assets/inner/images/avator.png'; }

		$followercont = followModel::where('followerid',$profiledetail[$k]['uid'])->get()->count();
		
		$return_string .= '<li style="width:218px; height:40px;"><a href="'.url().'/other_profile/'.$profiledetail[$k]['uid'].'"><div style="float:left; width:30%"><img src="'.$profimg.'" style="max-width:30px; max-height:30px;"   /></div><div style="float:left; width:70%"><div style="width:100%;float:left; font-size:14px; color:#0896D6;">'.$profiledetail[$k]["name"].'</div><div style="width:100%;float:left; margin-top:3px; color:#51C117">Followers ('.$followercont.')</div></div></a></li>';
		
		}
		if(count($group)>0) $return_string.='<li style="/*width:218px; height:40px;*/padding: 7px 3px;  color:#FFFFFF; background:#0896d6 ; font-size:14px;"><strong>Groups</strong></li>';
		
		for($p=0; $p<count($group);$p++){
		
		$membercount = groupmemberModel::where('group_id',$group[$p]['gid'])->get()->count();
		
				if($group[$p]['groupimage']!=''){ $grpimg =url().'/public/assets/upload/group/'.$group[$p]['groupimage']; }else{
		$grpimg =url().'/assets/inner/img/default_groupimage.png'; }
		
		$return_string .= '<li style="width:218px; height:40px;"><a href="'.url().'/viewgroupmember/'.$group[$p]['gid'].'"><div style="float:left; width:30%"><img src="'.$grpimg.'" style="max-width:30px; max-height:30px;" /></div><div style="float:left; width:70%"><div style="width:100%;float:left; font-size:14px; color:#0896D6;">'.$group[$p]["groupname"].'</div><div style="width:100%;float:left; margin-top:3px; color:#51C117">'.$membercount.' members</div></div></a></li>';
		}
		return $return_string;
	
	}
}
?>