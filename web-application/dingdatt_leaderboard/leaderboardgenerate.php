<?php 
/*** this code for generating the leader board through cron ****/

$conn = mysql_connect("localhost","root","");
$db = mysql_select_db('dingdatt',$conn) ; 

date_default_timezone_set("UTC");
$curdate = date("Y-m-d H:i:s", time());

$sql = mysql_query("select ID as contest_id from contest where votingenddate<'$curdate' and leaderboard=0");
$sqlcnt = mysql_num_rows($sql);
if($sqlcnt)
{
	
	while($rows=mysql_fetch_object($sql))
	{ 

		 $contest_id = $rows->contest_id; //echo "</br>";
		$leaderboarddata = mysql_query("select count(*) as votecount, b.id as contestparticipant_id,b.user_id from voting a
		LEFT JOIN contestparticipant b ON a.contest_participant_id=b.ID	
		Left Join contest c ON b.contest_id=c.ID
		where a.vote='L' and c.ID=$contest_id GROUP BY b.id order by votecount desc");

		$leaderboarddatacount = mysql_num_rows($leaderboarddata);
		if($leaderboarddata){ 
			$i=1;
			while($leaderboarddatarows = mysql_fetch_object($leaderboarddata))
			{
				$insertdata = mysql_query("insert into leaderboard(contest_id,user_id,votes) values($contest_id,$leaderboarddatarows->user_id,$leaderboarddatarows->votecount)");
				$i++;
			}
			$j=1;
			$sqlpositionapply = mysql_query("SELECT DISTINCT votes FROM `leaderboard` WHERE contest_id=$contest_id");
			while($sqlfetch=mysql_fetch_object($sqlpositionapply)){ 
			$updatequery  = mysql_query("update leaderboard set position=$j where contest_id=$contest_id and votes=$sqlfetch->votes");			
			$j++;
			}		
			$updatecontest = mysql_query("update contest set leaderboard=1 where ID=$contest_id");
		}
	}
}
?>