/**
 * Author: Jonathan Harrison
 * Date: 8/25/13
 * Description: This class is an extension of the User class. It is intended to be
 * 				the class for the logged in user and provide additional functions.
 */

package com.whereone.bloodstone.models;

import java.util.Date;


public class Question{
	private Integer id;
	private Integer user_id;
	private String question;
	private Boolean active;
	private Date updated;
	private Date created;
	
	public Question(){
		this.id = 0;
		this.user_id = 0;
		this.question = "";
		this.active = true;
		this.updated = null;
		this.created = null;
	}
	
	public Question(Integer id, Integer user_id, String question, Boolean active, Date updated, Date created){
		this.id = id;
		this.user_id = user_id;
		this.question = question;
		this.active = active;
		this.updated = updated;
		this.created = created;
	}
	
	public Integer getId(){
		return id;
	}
	
	public Integer getUserId(){
		return user_id;
	}
	public String getQuestion(){
		return question;
	}
	public Boolean isActive(){
		return active;
	}
	public Date getDateUpdated(){
		return updated;
	}
	public Date getDateCreated(){
		return created;
	}
}
