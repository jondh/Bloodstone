package com.whereone.bloodstone.activities;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;

import com.whereone.bloodstone.R;
import com.whereone.bloodstone.controllers.DBhttpRequest;
import com.whereone.bloodstone.models.Profile;
import com.whereone.bloodstone.users.LogOutCurrent;
import com.whereone.bloodstone.users.LogOutCurrent.CheckUserListener;

/**
 * An example full-screen activity that shows and hides the system UI (i.e.
 * status bar and navigation/system bar) with user interaction.
 * 
 * @see SystemUiHider
 */
public class StartupActivity extends Activity {
	
	protected BloodstoneApplication application;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_startup);
		
		Intent prevIntent = getIntent();
		final Boolean newUser = prevIntent.getBooleanExtra("NewUser", false);
		
		application = (BloodstoneApplication) getApplication();
		final Context context = this;
		
		final ImageView mySpinner = (ImageView) findViewById(R.id.startup_spinner);
		
		final Spin spinImage = new Spin(context, mySpinner, 100);
		
		
		final DBhttpRequest httpRequest = DBhttpRequest.getInstance();
		
		final Profile profileL = Profile.getInstance();
		
		final Intent mainIntent = new Intent(this, MainActivity.class);
		final Intent loginIntent = new Intent(this, LoginActivity.class);
	
		
		LogOutCurrent logOut = new LogOutCurrent(httpRequest, profileL, application);

		logOut.setCheckUserListener(new CheckUserListener(){

			@Override
			public void checkResult(Boolean result, String reason) {
				System.out.println(result + reason);
				if(result){
					startActivity(mainIntent);		     
				}
				else{
					startActivity(loginIntent);
				}
			}
			
		});
		logOut.checkUser();
		
	}

	@Override
	protected void onPostCreate(Bundle savedInstanceState) {
		super.onPostCreate(savedInstanceState);

	}
	
	private class MyInt{
		public Integer myInt;
	}
	
	private class Spin extends Thread{
		private Context context;
		private ImageView imageView;
		private Integer intervalMillis;
		private Boolean start;

		public Spin(Context context, ImageView mySpinner, Integer intervalMillis){
			this.context = context;
			this.imageView = mySpinner;
			this.intervalMillis = intervalMillis;
			((Activity) context).runOnUiThread(new Runnable(){

				@Override
				public void run() {
					imageView.setVisibility(View.INVISIBLE);
				}
				
			});
		}
		
		@Override
		public void run() {
			if(context != null && imageView != null){
				((Activity) context).runOnUiThread(new Runnable(){

					@Override
					public void run() {
						imageView.setVisibility(View.VISIBLE);
					}
					
				});
				start = true;
				final MyInt myInt = new MyInt();
				for(;;){
					if(!start) break;
					for(float i = 0; i < 360; i += 10){
						if(!start) break;
						myInt.myInt = (int) i;
						
				        ((Activity) context).runOnUiThread(new Runnable(){
		
							@Override
							public void run() {
								imageView.setRotation(myInt.myInt);
							}
				        	
				        });
			        
						try {
							Thread.sleep(intervalMillis);
						} catch (InterruptedException e) {
							// TODO Auto-generated catch block
							e.printStackTrace();
						}
					}
				}
			}
		}
		
		public void stopRunning(){
			start = false;
			((Activity) context).runOnUiThread(new Runnable(){

				@Override
				public void run() {
					imageView.setVisibility(View.INVISIBLE);
				}
				
			});
			
		}
	}

}
