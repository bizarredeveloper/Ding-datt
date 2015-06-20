<?php

class votingController extends BaseController {

    public function voting() {

        $contestparticipant_id = $_GET['contestparticipant_id'];
        $votingstatus = $_GET['votingstatus'];
        $curdate = date('Y-m-d h:i:s');
        $inputdetails['user_id'] = Auth::user()->ID;
        $inputdetails['contest_participant_id'] = $contestparticipant_id;
        $inputdetails['vote'] = $votingstatus;
        $inputdetails['votingdate'] = $curdate;

        $VERIFY = votingModel::where('user_id', Auth::user()->ID)->where('contest_participant_id', $contestparticipant_id)->get()->count();
        if (!$VERIFY) {
            $validation = Validator::make($inputdetails, votingModel::$rules);
            if ($validation->passes()) {
                $ok = votingModel::create($inputdetails);
                if ($ok)
                    return "Saved";
            }
            else {
                return $validation->messages();
            }
        } else {
            return "You are already saved";
        }
    }

}

?>