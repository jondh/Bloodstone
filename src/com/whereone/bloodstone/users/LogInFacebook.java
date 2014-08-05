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

import com.facebook.model.GraphUser;
import com.whereone.bloodstone.controllers.DBhttpRequest;
import com.whereone.bloodstone.models.Profile;

public class LogInFacebook extends AsyncTask<String, Void, String> {
	private DBhttpRequest httpRequest;
	private GraphUser user;
	private String accessToken;
	private LoginFBListener listener;
	private Boolean addNew;
	
	public LogInFacebook(DBhttpRequest httpRequest, GraphUser user, String accessToken, Boolean addNew){
		this.httpRequest = httpRequest;
		this.user = user;
		this.accessToken = accessToken;
		this.addNew = addNew;
	}

	public void setLoginFBListener(LoginFBListener _listener) {
        this.listener = _listener;
    }
	
	@Override
	protected void onPreExecute(){
		
	}
	
	@Override
	protected String doInBackground(String... arg0) {
		String url = arg0[0];
		ArrayList<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
		
		nameValuePairs.add(new BasicNameValuePair("fbID", user.getId()));
		nameValuePairs.add(new BasicNameValuePair("token", accessToken ));
		nameValuePairs.add(new BasicNameValuePair("new", addNew.toString() ));
		String result = httpRequest.sendRequest(nameValuePairs, url);
		System.out.println(result);
			
		try{
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
				else if( jResult.contains("none") ){
					return "none";
				}
				else if( jResult.contains("failure") ){
					if(jResult.contains("Token")){
						return "badFbToken";
					}
					return "failure";
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
		listener.LoginComplete(result);
	}
	
	@Override
	protected void onCancelled() {
		listener.LoginCancelled();
	}

	public interface LoginFBListener{
		public void LoginComplete(String result);
		public void LoginCancelled();
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
