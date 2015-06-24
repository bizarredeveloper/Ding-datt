package com.bizarre.dingdatt.pojo;

import java.io.Serializable;

public class ContestDetails implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;

	private int participatedcontest;
	private int ID;
	private String contest_name;
	private String description;
	private String themephoto;
	private int noofparticipant;
	private String conteststartdate;
	private String contestenddate;
	private String votingstartdate;
	private String votingenddate;
	private String contesttype;
	private String createdby;
	private String visibility;
	private int status;
	private String contestcode;
	private int sponsor;
	private String sponsorphoto;
	private String sponsorname;
	private String prize;
	private String createddate;
	private String updateddate;
	private int leaderboard;
	private boolean contestparticipantid;

	public boolean getContestparticipantid() {
		return contestparticipantid;
	}

	public void setContestparticipantid(boolean contestparticipantid) {
		this.contestparticipantid = contestparticipantid;
	}

	public int getParticipatedcontest() {
		return participatedcontest;
	}

	public void setParticipatedcontest(int participatedcontest) {
		this.participatedcontest = participatedcontest;
	}

	public int getID() {
		return ID;
	}

	public void setID(int iD) {
		ID = iD;
	}

	public String getContest_name() {
		return contest_name;
	}

	public void setContest_name(String contest_name) {
		this.contest_name = contest_name;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public String getThemephoto() {
		return themephoto;
	}

	public void setThemephoto(String themephoto) {
		this.themephoto = themephoto;
	}

	public int getNoofparticipant() {
		return noofparticipant;
	}

	public void setNoofparticipant(int noofparticipant) {
		this.noofparticipant = noofparticipant;
	}

	public String getConteststartdate() {
		return conteststartdate;
	}

	public void setConteststartdate(String conteststartdate) {
		this.conteststartdate = conteststartdate;
	}

	public String getContestenddate() {
		return contestenddate;
	}

	public void setContestenddate(String contestenddate) {
		this.contestenddate = contestenddate;
	}

	public String getVotingstartdate() {
		return votingstartdate;
	}

	public void setVotingstartdate(String votingstartdate) {
		this.votingstartdate = votingstartdate;
	}

	public String getVotingenddate() {
		return votingenddate;
	}

	public void setVotingenddate(String votingenddate) {
		this.votingenddate = votingenddate;
	}

	public String getContesttype() {
		return contesttype;
	}

	public void setContesttype(String contesttype) {
		this.contesttype = contesttype;
	}

	public String getCreatedby() {
		return createdby;
	}

	public void setCreatedby(String createdby) {
		this.createdby = createdby;
	}

	public String getVisibility() {
		return visibility;
	}

	public void setVisibility(String visibility) {
		this.visibility = visibility;
	}

	public int getStatus() {
		return status;
	}

	public void setStatus(int status) {
		this.status = status;
	}

	public String getContestcode() {
		return contestcode;
	}

	public void setContestcode(String contestcode) {
		this.contestcode = contestcode;
	}

	public int getSponsor() {
		return sponsor;
	}

	public void setSponsor(int sponsor) {
		this.sponsor = sponsor;
	}

	public String getSponsorphoto() {
		return sponsorphoto;
	}

	public void setSponsorphoto(String sponsorphoto) {
		this.sponsorphoto = sponsorphoto;
	}

	public String getSponsorname() {
		return sponsorname;
	}

	public void setSponsorname(String sponsorname) {
		this.sponsorname = sponsorname;
	}

	public String getPrize() {
		return prize;
	}

	public void setPrize(String prize) {
		this.prize = prize;
	}

	public String getCreateddate() {
		return createddate;
	}

	public void setCreateddate(String createddate) {
		this.createddate = createddate;
	}

	public String getUpdateddate() {
		return updateddate;
	}

	public void setUpdateddate(String updateddate) {
		this.updateddate = updateddate;
	}

	public int getLeaderboard() {
		return leaderboard;
	}

	public void setLeaderboard(int leaderboard) {
		this.leaderboard = leaderboard;
	}
}
