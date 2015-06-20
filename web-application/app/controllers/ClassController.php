<?php

class ClassController extends BaseController
{
    
    public function ClassLayout()
    {
	$ClassDetails = ClassModel::all()->toArray();
      
        return View::make('class/class')->with('ClassDetails', $ClassDetails);
     
    }
    
    public function ClassProcess()
    {

       
        $ClassData = Input::all();
        $validation = Validator::make($ClassData, ClassModel::$rules);
        
        if ($validation->passes()) 
        {

            ClassModel::create($ClassData);
            return Redirect::to('class')->with('Message', 'Class Details Saved Succesfully');
        } else 
        {
            
            return Redirect::to('class')->withInput()->withErrors($validation->messages());
        }
    }
	public function ClassEdit($data=NULL)
    {
	    $editvehicle=$data;
		$ClassDetailsbyid = ClassModel::where('AutoID', $editvehicle)->get()->toArray();
        $ClassDetails = ClassModel::all()->toArray();
      
        return View::make('class/classupdate')->with('ClassDetails', $ClassDetails)->with('ClassDetailsbyid', $ClassDetailsbyid);
	}
	public function ClassupdateProcess($data=NULL)
    {
        $ClassEditData = array_filter(Input::except(array('_token')));
	
	  $validation = Validator::make($ClassEditData, ClassModel::$updaterules);        
        if ($validation->passes()) 
        {
		   $affectedRows = ClassModel::where('AutoID', $data)->update($ClassEditData);
            
            return Redirect::to('classedit/'.$data)->with('Message', 'Class Details Update Succesfully');
        } else 
        {
            return Redirect::to('classedit/'.$data)->withInput()->withErrors($validation->messages());
        }
    }
	public function ClassDelete($data=NULL)
    {
	    $editvehicle=$data;
		$affectedRows = ClassModel::where('AutoID', $editvehicle)->delete();		
       return Redirect::to('class')->with('Message', 'Class Details Delete Succesfully');
	}
	 public function Importprocess()
    {	
	$uploaddata=Array();
        $StudentAdmissionData = Input::all();

        $validation  = Validator::make($StudentAdmissionData, ClassModel::$importrules);        
        if ($validation->passes()) 
        {
		
		 if(!empty($StudentAdmissionData['importfile']))
	{
	Input::file('importfile')->move('assets/uploads/grade/','grade' . Input::file('importfile')->getClientOriginalName());
	$importfile='grade' . Input::file('importfile')->getClientOriginalName();
	
	}
$results=Excel::load('assets/uploads/grade/'.$importfile, function($reader) {

})->get()->toArray();
if(count(array_filter($results)) == 1)
{

$finaldata=$results[0];
} else {
$finaldata=$results;
}

foreach($finaldata as $final)
{
$GradeName=$final['grade'];	

if(!empty($GradeName))
{
   $count = ClassModel::where('GradeName', '=', $GradeName)->count();
     if($count==0)
	 {
	 
	 $ClassData['GradeName']=$GradeName;
	 ClassModel::create($ClassData);
	 }
	 }
}

 return Redirect::to('class')->with('Message', 'Class Details Saved Succesfully');
        } else 
        {
            
            return Redirect::to('class')->withInput()->withErrors($validation->messages());
        }
}
public function GradeExportLayout()
    {	

Excel::create('Grade', function($excel) {

    $excel->sheet('Sheetname', function($sheet) {
	$uploaddata=Array();
		$ClassDetails = ClassModel::all()->toArray();


foreach ($ClassDetails as $ClassDetailsvalue)
{
$uploaddata[]['Grade']=$ClassDetailsvalue['GradeName'];	

}

        $sheet->fromArray($uploaddata);

    });

})->export('xlsx');	

    return Redirect::to('class');		
       
    }
}