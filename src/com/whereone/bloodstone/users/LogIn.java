/**
 *  Author: Jonathan Harrison
 *  Date: 9/21/13
 *  Description: This class is used to log in to the where one server
 *  Input: An instance of DBhttpRequest
 *		   A String for user name
 *		   A String for password
 *  Output: Integer amount for the logged in userID -> 0 or negative if log in failed
 *  Implementation:
 *  
	   	LogIn logIn = new LogIn(DBhttpRequest, String userName, String password);
	   	logIn.setLogInListener(new LogInListener(){
	   		@Override
	   		public void logInComplete(Profile _user){
	   		
	   		}
	   		@Override
	   		public void logInCancelled(){
	   		
	   		}
	   	});
	   	logIn.execute("<url>");
 * 
 * 
 *  SERVER input (name value pairs): "userName" -> String for user name
 * 									 "password" -> String for password
 * 
 *  SERVER output: returns a JSON object with the following name value pair:
 *  				"userID" -> Integer for userID (0 or negative for failure)
 */

package com.whereone.bloodstone.users;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;

import com.whereone.bloodstone.controllers.DBhttpRequest;
import com.whereone.bloodstone.models.Profile;


public class LogIn extends AsyncTask<String, Void, String> {
	private DBhttpRequest httpRequest;
	private LogInListener listener;
	private String userName;
	private String password;
	
	
	public LogIn(DBhttpRequest _httpRequest, String _userName, String _password){
		httpRequest = _httpRequest;
		userName = _userName;
		password = _password;
	}
	
	public void setLogInListener(LogInListener _listener) {
        this.listener = _listener;
    }
	
	@Override
	protected String doInBackground(String... arg0) {
		
		String url = arg0[0];
		ArrayList<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
		nameValuePairs.add(new BasicNameValuePair("username",userName));
		nameValuePairs.add(new BasicNameValuePair("password",password));
		String result = httpRequest.sendRequest(nameValuePairs, url);
		
		System.out.println(result);
		
		try {
			JSONObject jObj = new JSONObject(result);
			if(jObj.has("result")){
				String jResult = jObj.getString("result");
				if( jResult.contains("success") ){
					JSONObject userJSON = jObj.getJSONObject("User");
					Profile.getInstance().setProfile(
							userJSON.getInt("id"),
							userJSON.getString("username"),
							"",
							userJSON.getString("firstName"),
							userJSON.getString("lastName"),
							userJSON.getString("email"),
							userJSON.getBoolean("aquamarine"),
							userJSON.getBoolean("bloodstone"),
							userJSON.getString("fbID"),
							userJSON.getString("private_access_token"),
							userJSON.getString("public_access_token"),
							getDateFromSqlString( userJSON.getString("updated") ),
							getDateFromSqlString( userJSON.getString("dateTime") )
					);
					return "success";
				}
				else if( jResult.contains("timeout") ){
					return "timeout";
				}
				else if( jResult.contains("unknownHost") ){
					return "unknownHost";
				}
			}
			return "failure";
			
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return "failure";
		}
	}

	@Override
	protected void onPostExecute(String result) {
		listener.logInComplete(result);
	}

	@Override
	protected void onCancelled() {
		listener.logInCancelled();
	}
	
	public interface LogInListener{
		public void logInComplete(String result);
		public void logInCancelled();
	}
	
	public Date getDateFromSqlString(String sql){
		DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd hh:mm:ss", Locale.US);
		try {
			return formatter.parse(sql);
		} catch (ParseException e) {
			e.printStackTrace();
			return null;
		}
	}
}
