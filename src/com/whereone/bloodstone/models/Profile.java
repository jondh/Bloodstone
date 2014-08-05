/**
 * Author: Jonathan Harrison
 * Date: 8/25/13
 * Description: This class is an extension of the User class. It is intended to be
 * 				the class for the logged in user and provide additional functions.
 */

package com.whereone.bloodstone.models;

import java.io.UnsupportedEncodingException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;


public class Profile extends User{
	private String password;
	private String private_token;
	private String public_token;
	
	private String currentDate;

	private static Profile instance;
	
	private Profile(){
		
	}
	
	public static void init(){
		if(instance == null){
			instance = new Profile();
			instance.id = 0;
			instance.username = "";
			instance.password = "";
			instance.firstName = "";
			instance.lastName = "";
			instance.email = "";
			instance.aquamarine = false;
			instance.bloodstone = false;
			instance.fbID = "";
			instance.private_token = "";
			instance.public_token = "";
		}
	}
	
	public static Profile getInstance(){
		return instance;
	}
	
	public void setProfile(Integer id, String username, String password, String firstName, String lastName, String email, Boolean aquamarine, Boolean bloodstone, String fbID, String private_token, String public_token, Date updated, Date created){
		if(instance == null) init();
		instance.id = id;
		instance.username = username;
		instance.password = password;
		instance.email = email;
		instance.firstName = firstName;
		instance.lastName = lastName;
		instance.aquamarine = aquamarine;
		instance.bloodstone = bloodstone;
		instance.fbID = fbID;
		instance.private_token = private_token;
		instance.public_token = public_token;
		instance.updated = updated;
		instance.created = created;
	}
	
	public void setPassword(String _password){
		if(instance == null) init();
		instance.password = _password;
	}
	
	public String getPassword(){
		return password;
	}
	public String getPrivateToken(){
		return private_token;
	}
	public String getPublicToken(){
		return public_token;
	}
	
	public String getCurrentDate(){
		if(currentDate == null) return "";
		return currentDate;
	}
	
	public String hashedPrivate(){
		SimpleDateFormat s = new SimpleDateFormat("ddMMyyyyhhmmss", Locale.US);
		currentDate = s.format(new Date());
		try {
			return computeHash(this.private_token + currentDate);
		} catch (NoSuchAlgorithmException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return "";
	}
	
	public String computeHash(String input) throws NoSuchAlgorithmException, UnsupportedEncodingException{
	    MessageDigest digest = MessageDigest.getInstance("SHA-256");
	    digest.reset();

	    byte[] byteData = digest.digest(input.getBytes("UTF-8"));
	    StringBuffer sb = new StringBuffer();

	    for (int i = 0; i < byteData.length; i++){
	      sb.append(Integer.toString((byteData[i] & 0xff) + 0x100, 16).substring(1));
	    }
	    return sb.toString();
	}
}
