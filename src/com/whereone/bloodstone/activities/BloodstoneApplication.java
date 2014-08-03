package com.whereone.bloodstone.activities;

import android.app.Application;
import android.content.Context;
import android.content.SharedPreferences;

import com.whereone.bloodstone.controllers.DBhttpRequest;
import com.whereone.bloodstone.controllers.UsersController;
import com.whereone.bloodstone.models.Profile;
import com.whereone.bloodstone.users.LogOutCurrent;
import com.whereone.bloodstone.users.LogOutCurrent.CheckUserListener;

public class BloodstoneApplication extends Application{

	private SharedPreferences storedProfile;
	private Profile profile;
	
	@Override
	public void onCreate(){
		super.onCreate();
		
		storedProfile = this.getSharedPreferences("com.whereone.profile", Context.MODE_PRIVATE);
		
		initProfile();
		initSingletons();
		
	}
	
	public void initSingletons(){
		DBhttpRequest.init();
		UsersController.init(this);
	}
	
	public void clearData(){
		storedProfile.edit().clear();
		storedProfile.edit().commit();
	}
	
	public void checkLoginStatus(DBhttpRequest httpRequest, Profile profile){
		LogOutCurrent logOut = new LogOutCurrent(httpRequest, profile, this);
		
		logOut.setCheckUserListener(new CheckUserListener(){

			@Override
			public void checkResult(Boolean result, String reason) {
				System.out.println(result + reason);
			}
			
		});
		logOut.checkUser();
	}
	
	public void initProfile(){
		Profile.init();
		profile = Profile.getInstance();
		profile.setProfile(storedProfile.getInt("id", 0),
		storedProfile.getString("username", ""),
		storedProfile.getString("password", ""),
		storedProfile.getString("firstName", ""),
		storedProfile.getString("lastName", ""),
		storedProfile.getString("email", ""),
		storedProfile.getString("fbID", ""),
		storedProfile.getString("privateToken", ""),
		storedProfile.getString("publicToken", "") );
	}
	
}
