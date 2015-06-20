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
}
?>