<?php

class commentController extends BaseController {

    public function viewcomment() {
        $participant_id = $_GET['participant_id'];
        $contest_id = $_GET['contest_id'];
        return Redirect::to("contest_info/" . $contest_id)->with('tab', 'gallery')->with('gallerytype', 'comment')->with('viewcommentforparticipant', $participant_id);
   }

    public function putcomment() {

        $inputdetails['userid'] = Auth::user()->ID;
        $inputdetails['contest_participant_id'] = $_GET['participantid'];
        $inputdetails['comment'] = $_GET['comment'];
        $curdate = date('Y-m-d h:i:s');
        $inputdetails['createddate'] = $curdate;
        $savecomment = commentModel::create($inputdetails);

        if ($savecomment)
            return Redirect::to("contest_info/" . $_GET['contest_id'])->with('tab', 'gallery')->with('gallerytype', 'comment')->with('viewcommentforparticipant', $_GET['participantid'])->with('Massage', 'Comment added successfully');
    }

    public function putreplycomment() {
        $inputdetails['comment_id'] = $_GET['comment_id'];
        $inputdetails['replycomment'] = $_GET['replycmt'];

        $curdate = date('Y-m-d h:i:s');
        $inputdetails['createddate'] = $curdate;
        $inputdetails['user_id'] = Auth::user()->ID;
        $participant_id = $_GET['participant_id'];
        $contest_id = $_GET['contest_id'];
        $save = replycommentModel::create($inputdetails);
        if ($save) {

            return Redirect::to("contest_info/" . $contest_id)->with('tab', 'gallery')->with('gallerytype', 'comment')->with('viewcommentforparticipant', $participant_id)->with('Massage', 'Reply Comment added successfully');
        }
    }
}
?>