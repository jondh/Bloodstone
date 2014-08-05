/**
 * Author: Jonathan Harrison
 * Date: 8/25/13
 * Description: This class is an extension of the User class. It is intended to be
 * 				the class for the logged in user and provide additional functions.
 */

package com.whereone.bloodstone.models;

import java.util.Date;


public class Response{
	private Integer id;
	private Integer question_id;
	private Integer user_id;
	private Boolean answer;
	private Date created;
	
	public Response(){
		this.id = 0;
		this.question_id = 0;
		this.user_id = 0;
		this.answer = false;
		this.created = null;
	}
	
	public Response(Integer id, Integer question_id, Integer user_id, Boolean answer, Date created){
		this.id = id;
		this.question_id = question_id;
		this.user_id = user_id;
		this.answer = answer;
		this.created = created;
	}
	
	public Integer getId(){
		return id;
	}
	public Integer getQuestionId(){
		return question_id;
	}
	public Integer getUserId(){
		return user_id;
	}
	public Boolean getAnswer(){
		return answer;
	}
	public Date getDateCreated(){
		return created;
	}
}
