/**
 * Author: Jonathan Harrison
 * Date: 8/25/13
 * Description: This class is used to represent a user. It contains all the necessary information
 * 				about a user.
 */

package com.whereone.bloodstone.models;

import java.util.Date;

public class User {
	protected Integer id;
	protected String username;
	protected String firstName;
	protected String lastName;
	protected String email;
	protected Boolean aquamarine;
	protected Boolean bloodstone;
	protected String fbID;
	protected Date updated;
	protected Date created;
	
	public User(){
		id = 0;
		username = "";
		firstName = "";
		lastName = "";
		email = "";
		aquamarine = false;
		bloodstone = false;
		fbID = "";
		updated = null;
		created = null;
	}
	
	public User(Integer id, String username, String firstName, String lastName, String email, Boolean aquamarine, Boolean bloodstone, String fbID, Date updated, Date created){
		this.id = id;
		this.username = username;
		this.firstName = firstName;
		this.lastName = lastName;
		this.email = email;
		this.aquamarine = aquamarine;
		this.bloodstone = bloodstone;
		this.fbID = fbID;
		this.updated = updated;
		this.created = created;
	}
	
	public void setId(Integer id){
		this.id = id;
	}
	public void setUsername(String username){
		this.username = username;
	}
	public void setFirstName(String firstName){
		this.firstName = firstName;
	}
	public void setLastName(String lastName){
		this.lastName = lastName;
	}
	
	public Integer getId(){
		return id;
	}
	public String getUsername(){
		return username;
	}
	public String getFirstName(){
		return firstName;
	}
	public String getLastName(){
		return lastName;
	}
	public String getName(){
		return firstName + " " + lastName;
	}
	public String getEmail(){
		return email;
	}
	public Boolean usedAquamarine(){
		return aquamarine;
	}
	public Boolean usedBloodstone(){
		return bloodstone;
	}
	public String getFbId(){
		return fbID;
	}
	public Date getDateUpdated(){
		return updated;
	}
	public Date getDateCreated(){
		return created;
	}
}
