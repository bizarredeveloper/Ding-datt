package com.bizarre.dingdatt.pojo;

public class GroupFollower {

	private int groupid = 0;
	private String groupname = "";
	private String grouptype = "";
	private int createdby = 0;
	private String owner = "";
	private String groupimage = "";
	private int invite = -1;
	private int followerprimaryid = 0;
	private String profilepicture = "";
	private String firstname = "";
	private int followerid = 0;

	public int getFollowerprimaryid() {
		return followerprimaryid;
	}

	public void setFollowerprimaryid(int followerprimaryid) {
		this.followerprimaryid = followerprimaryid;
	}

	public String getProfilepicture() {
		return profilepicture;
	}

	public void setProfilepicture(String profilepicture) {
		this.profilepicture = profilepicture;
	}

	public String getFirstname() {
		return firstname;
	}

	public void setFirstname(String firstname) {
		this.firstname = firstname;
	}

	public int getFollowerid() {
		return followerid;
	}

	public void setFollowerid(int followerid) {
		this.followerid = followerid;
	}

	public int getGroupid() {
		return groupid;
	}

	public void setGroupid(int groupid) {
		this.groupid = groupid;
	}

	public String getGroupname() {
		return groupname;
	}

	public void setGroupname(String groupname) {
		this.groupname = groupname;
	}

	public String getGrouptype() {
		return grouptype;
	}

	public void setGrouptype(String grouptype) {
		this.grouptype = grouptype;
	}

	public int getCreatedby() {
		return createdby;
	}

	public void setCreatedby(int createdby) {
		this.createdby = createdby;
	}

	public String getOwner() {
		return owner;
	}

	public void setOwner(String owner) {
		this.owner = owner;
	}

	public String getGroupimage() {
		return groupimage;
	}

	public void setGroupimage(String groupimage) {
		this.groupimage = groupimage;
	}

	public int getInvite() {
		return invite;
	}

	public void setInvite(int invite) {
		this.invite = invite;
	}
}
