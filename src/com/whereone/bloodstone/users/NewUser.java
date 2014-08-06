/**
 *  Author: Jonathan Harrison
 *  Date: 9/21/13
 *  Description: This class is used to create a new user on the where one server
 *  Input: An instance of DBhttpRequest
 *		   A String for user name
 *		   A String for password
 *		   A String for email
 *		   A String for first name
 *		   A String for last name
 *         A String for app ("" or "GroupWallet" or "Pace")
 *  Output: Integer amount for the logged in userID -> 0 or negative if log in / account creation failed
 *  Implementation:
 *  
	   	NewUser newUser = new NewUser(DBhttpRequest, String _userName, String _password, String _email,
			String _firstName, String _lastName, String _app);
	   	newUser.setnewUserListener(new newUserListener(){
	   		@Override
	   		public void newUserPreExecute(){
	   		
	   		}
	   		@Override
	   		public void newUserComplete(Integer _userID){
	   		
	   		}
	   		@Override
	   		public void newUserCancelled(){
	   		
	   		}
	   	});
	   	newUser.execute();
 * 
 * 
 *  SERVER input (name value pairs): 
 *  				"userName"  -> String for user name
 * 					"password"  -> String for password
 * 					"email"     -> String for email
 * 					"firstName" -> String for first name
 *					"lastName"  -> String for last name
 * 					"app"       -> String for which app account is being created for ("" (none) or "GroupWallet" etc)
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

public class NewUser extends AsyncTask<String, Void, String> {
	private DBhttpRequest httpRequest;
	private NewUserListener listener;
	private String userName;
	private String password;
	private String email;
	private String firstName;
	private String lastName;
	
	public NewUser(DBhttpRequest _httpRequest, String _userName, String _password, String _email,
			String _firstName, String _lastName){
		httpRequest = _httpRequest;
		userName = _userName;
		password = _password;
		email = _email;
		firstName = _firstName;
		lastName = _lastName;
	}

	public void setNewUserListener(NewUserListener _listener) {
        this.listener = _listener;
    }
	
	@Override
	protected void onPreExecute(){
		listener.newUserPreExecute();
	}
	
	@Override
	protected String doInBackground(String... arg0) {
		String url = arg0[0];
		ArrayList<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
		nameValuePairs.add(new BasicNameValuePair("username",userName));
		nameValuePairs.add(new BasicNameValuePair("password",password));
		nameValuePairs.add(new BasicNameValuePair("email",email));
		nameValuePairs.add(new BasicNameValuePair("firstName",firstName));
		nameValuePairs.add(new BasicNameValuePair("lastName",lastName));
		nameValuePairs.add(new BasicNameValuePair("bloodstone","1"));
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
				else if( jObj.has("errors") ){
					String errorReturn = "";
					JSONObject valErrors = jObj.getJSONObject("errors");
					if(valErrors.has("email")){
						String emailResult = valErrors.getString("email");
						if(emailResult.contains("already in use")){
							errorReturn += "not unique email";
						}
						else{
							errorReturn += "bad email";
						}
					}
					if(valErrors.has("username")){
						String usernameResult = valErrors.getString("username");
						if(usernameResult.contains("already in use")){
							errorReturn += "not unique username";
						}
						else{
							errorReturn += "bad username";
						}
					}
					return errorReturn;
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
		listener.newUserComplete(result);
	}

	@Override
	protected void onCancelled() {
		listener.newUserCancelled();
	}
	
	public interface NewUserListener{
		public void newUserPreExecute();
		public void newUserComplete(String _userID);
		public void newUserCancelled();
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