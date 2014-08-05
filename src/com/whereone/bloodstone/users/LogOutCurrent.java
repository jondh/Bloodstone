package com.whereone.bloodstone.users;

import android.content.Intent;

import com.facebook.Session;
import com.whereone.bloodstone.R;
import com.whereone.bloodstone.activities.BloodstoneApplication;
import com.whereone.bloodstone.activities.LoginActivity;
import com.whereone.bloodstone.controllers.DBhttpRequest;
import com.whereone.bloodstone.models.Profile;
import com.whereone.bloodstone.users.LogInStatus.LogInStatusListener;
import com.whereone.bloodstone.users.LogOut.LogOutListener;

public class LogOutCurrent {
	private BloodstoneApplication app;
	private DBhttpRequest httpRequest;
	private Profile profile;
	private CheckUserListener listener;

	public LogOutCurrent(DBhttpRequest httpRequest, Profile profile, BloodstoneApplication app){
		this.app = app;
		this.httpRequest = httpRequest;
		this.profile = profile;
	}
	
	public void setCheckUserListener(CheckUserListener listener){
		this.listener = listener;
	}

	public interface CheckUserListener{
		public void checkResult(Boolean result, String reason);
	}
	
	
	public void checkUser(){
		if((profile.getId() <= 0) || (profile.getUsername().contentEquals(""))){
			listener.checkResult(false, "bad stored profile");
			return;
		}
		
		LogInStatus status = new LogInStatus(httpRequest, profile);
		status.setLogInStatusListener(new LogInStatusListener(){

			@Override
			public void LogInStatusComplete(Boolean result, String resultString) {
				if(result){
					listener.checkResult(true, "success");
				}
				else listener.checkResult(false, resultString);
			}

			@Override
			public void LogInStatusCancelled() {
				listener.checkResult(true, "cancelled");
			}
			
		});
		status.execute(app.getString(R.string.checkLogInURL));
	}
	
	
	public void logOut(){
		
		LogOut LogOut = new LogOut(httpRequest, profile);
	   	LogOut.setLogOutListener(new LogOutListener(){
	   		@Override
	   		public void LogOutComplete(Boolean result){
	   			if(result){
	   				Session session = Session.getActiveSession();
	   				if(session != null){
	   					session.closeAndClearTokenInformation(); 
	   				}
		   			app.clearData();
		   			Intent intent = new Intent(app, LoginActivity.class);
		   			intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		   			app.startActivity(intent);
	   			}
	   		}
	   		@Override
	   		public void LogOutCancelled(){
	   		
	   		}
	   	});
	   	LogOut.execute(app.getString(R.string.logOutURL));
	}
}
