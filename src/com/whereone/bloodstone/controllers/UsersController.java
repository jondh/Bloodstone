package com.whereone.bloodstone.controllers;

import java.util.ArrayList;
import java.util.concurrent.Semaphore;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteConstraintException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.util.Log;

import com.whereone.bloodstone.models.User;

public class UsersController extends SQLiteOpenHelper {
	private static UsersController instance;
	
	private final Semaphore insertAccess = new Semaphore(1, true);
	private final Semaphore checkAccess = new Semaphore(1, true);
	
	private UsersGetListener getListener;
	private UsersGetWRListener getWRListener;
	
	public static final String TABLE_USERS = "users";
	public static final String COLUMN_ID = "id";
	public static final String COLUMN_USERNAME = "username";
	public static final String COLUMN_FIRSTNAME = "firstname";
	public static final String COLUMN_LASTNAME = "lastname";
	public static final String COLUMN_EMAIL = "email";
	public static final String COLUMN_FBID = "fbID";
	
	private Context context;

	private static final String DATABASE_NAME = "users.db";
	private static final int DATABASE_VERSION = 1;

	// Database creation sql statement
	private static final String DATABASE_CREATE = "create table "
	      + TABLE_USERS + "(" + COLUMN_ID
	      + " integer primary key, " + COLUMN_USERNAME
	      + " text not null," + COLUMN_FIRSTNAME
	      + " text not null," + COLUMN_LASTNAME
	      + " text not null," + COLUMN_EMAIL
	      + " text not null," + COLUMN_FBID 
	      + " text);";

	private UsersController(Context _context) {
		super(_context, DATABASE_NAME, null, DATABASE_VERSION);
		
	}
	
	public static void init(Context _context){
		if(instance == null){
			instance = new UsersController(_context);
			instance.context = _context;
		}
	}
	
	public static UsersController getInstance(){
		return instance;
	}

	@Override
	public void onCreate(SQLiteDatabase database) {
		database.execSQL(DATABASE_CREATE);
	}

	@Override
	public void onUpgrade(SQLiteDatabase database, int oldVersion, int newVersion) {
		Log.w(UsersController.class.getName(),
		        "Upgrading database from version " + oldVersion + " to "
		            + newVersion + ", which will destroy all old data");
		database.execSQL("DROP TABLE IF EXISTS " + TABLE_USERS);
		onCreate(database);
	}
	
	public void setUsersGetListener(UsersGetListener _listener) {
        instance.getListener = _listener;
    }
	
	public interface UsersGetListener{
		// > 0 == Success => user id
		// 0 == Empty Result
		// -1 == Null Result -> due to bad access token(s)
		// -2 == timeout
		// -3 == unknown host
		// -4 == cancelled
		public void getUserComplete(Integer result);
	}
	
	public void setUsersGetWRListener(UsersGetWRListener listener){
		instance.getWRListener = listener;
	}
	
	public interface UsersGetWRListener{
		public void getWRUsersComplete(Integer result, ArrayList<Integer> resultUsers);
	}
	
	public void removeAll(){
		SQLiteDatabase databaseW = this.getWritableDatabase();
		databaseW.delete(TABLE_USERS, null, null);
		databaseW.close();
	}
	
	public User getUserFromUserName(String username){
		SQLiteDatabase databaseR = this.getReadableDatabase();
		String[] columns = {
				COLUMN_ID,
				COLUMN_USERNAME,
				COLUMN_FIRSTNAME,
				COLUMN_LASTNAME
		};
		
		String whereClause = "username = ?";
		String[] whereArgs = new String[]{
				username
		};
		
		Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
		
		if(cursor.getCount() >0)
        {
			cursor.moveToNext();
			User user = new User();
			user.setFirstName(cursor.getString(cursor.getColumnIndex(COLUMN_FIRSTNAME)));
			user.setLastName(cursor.getString(cursor.getColumnIndex(COLUMN_LASTNAME)));
			user.setUsername(cursor.getString(cursor.getColumnIndex(COLUMN_USERNAME)));
			user.setId(cursor.getInt(cursor.getColumnIndex(COLUMN_ID)));
			databaseR.close();
			return user;  
        }
        else
        {
        	databaseR.close();
            return null;
        }
	}
	
	public Integer getIdFromUserName(String username){
		SQLiteDatabase databaseR = this.getReadableDatabase();
		String[] columns = {
				COLUMN_ID
		};
		
		String whereClause = "username = ?";
		String[] whereArgs = new String[]{
				username
		};
		
		Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
		
		if(cursor.getCount() >0)
        {
			cursor.moveToNext();
			databaseR.close();
			return cursor.getInt(cursor.getColumnIndex(COLUMN_ID)) ;  
        }
        else
        {
        	databaseR.close();
            return 0;
        }
	}
	
	public String getUserNameFromId(Integer userID){
		SQLiteDatabase databaseR = this.getReadableDatabase();
		String[] columns = {
				COLUMN_USERNAME
		};
		
		String whereClause = "id = ?";
		String[] whereArgs = new String[]{
				userID.toString()
		};
			
		Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
			
		if(cursor.getCount() >0)
        {
            cursor.moveToNext();
            databaseR.close();
           return cursor.getString(cursor.getColumnIndex(COLUMN_USERNAME));
             
        }
		databaseR.close();
		return "";
	}
	
	public ArrayList<String> getUsersFromIds(ArrayList<Integer> userIDs){
		SQLiteDatabase databaseR = this.getReadableDatabase();
		String[] columns = {
				COLUMN_USERNAME
		};
		
		ArrayList<String> user_name = new ArrayList<String>();
		
		for(int i = 0; i < userIDs.size(); i++){
		
			String whereClause = "id = ?";
			String[] whereArgs = new String[]{
					userIDs.get(i).toString()
			};
			
			Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
			
			if(cursor.getCount() >0)
	        {
	            while (cursor.moveToNext())
	            {
	                 user_name.add( cursor.getString(cursor.getColumnIndex(COLUMN_USERNAME)) );
	             }
	        }
		}
		databaseR.close();
		return user_name;
	}
	
	public ArrayList<User> getUserssFromIds(ArrayList<Integer> userIDs){
		SQLiteDatabase databaseR = this.getReadableDatabase();
		if(userIDs == null) return null;
		String[] columns = {
				COLUMN_ID,
				COLUMN_USERNAME,
				COLUMN_FIRSTNAME,
				COLUMN_LASTNAME,
				COLUMN_EMAIL
		};
		
		ArrayList<User> users = new ArrayList<User>();
		
		for(int i = 0; i < userIDs.size(); i++){
		
			String whereClause = "id = ?";
			String[] whereArgs = new String[]{
					userIDs.get(i).toString()
			};
			
			Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
			
			if(cursor.getCount() >0)
	        {
	            while (cursor.moveToNext())
	            {
	                 users.add( new User(
	                		 cursor.getInt(cursor.getColumnIndex(COLUMN_ID)),
	                		 cursor.getString(cursor.getColumnIndex(COLUMN_USERNAME)),
	                		 cursor.getString(cursor.getColumnIndex(COLUMN_FIRSTNAME)),
	                		 cursor.getString(cursor.getColumnIndex(COLUMN_LASTNAME)),
	                		 cursor.getString(cursor.getColumnIndex(COLUMN_EMAIL)),
	                		 false,
	                		 false,
	                		 "",
	                		 null,
	                		 null
	                	));
	             }
	        }
		}
		databaseR.close();
		return users;
	}
	
	public User getUserFromId(Integer userID){
		SQLiteDatabase databaseR = this.getReadableDatabase();

		String[] columns = {
				COLUMN_ID,
				COLUMN_USERNAME,
				COLUMN_FIRSTNAME,
				COLUMN_LASTNAME,
				COLUMN_EMAIL
		};
		
		String whereClause = "id = ?";
		String[] whereArgs = new String[]{
				userID.toString()
		};
		
		Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
		
		if(cursor.getCount() >0)
        {
            cursor.moveToNext();
            databaseR.close();
            return ( new User(
        		 cursor.getInt(cursor.getColumnIndex(COLUMN_ID)),
        		 cursor.getString(cursor.getColumnIndex(COLUMN_USERNAME)),
        		 cursor.getString(cursor.getColumnIndex(COLUMN_FIRSTNAME)),
        		 cursor.getString(cursor.getColumnIndex(COLUMN_LASTNAME)),
        		 cursor.getString(cursor.getColumnIndex(COLUMN_EMAIL)),
        		 false,
        		 false,
        		 "",
        		 null,
        		 null
        	));
             
        }
		
		databaseR.close();
		return null;
	}
	
	public void insertUser(User user){
		try {
			insertAccess.acquire();
		} catch (InterruptedException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		SQLiteDatabase databaseW = this.getWritableDatabase();
		ContentValues values = new ContentValues();
		values.put("id", user.getId());
		values.put("username", user.getUsername());
		values.put("firstname", user.getFirstName());
		values.put("lastname", user.getLastName());
		values.put("email", user.getEmail());
		try{
			databaseW.insertOrThrow(TABLE_USERS, null, values);
		}
		catch (SQLiteConstraintException e){
			Log.i("Wallet Constraint Exception", user.getId() + " duplicate");
		}
		catch (NullPointerException e){
			Log.i("UserCont insertUser", "NullPointerException " + user.getId() + " " + user.getName());
		}
		databaseW.close();
		insertAccess.release();
	}
	
	public void insertUsers(ArrayList<User> users){
		SQLiteDatabase databaseW = this.getWritableDatabase();
		for(Integer i = 0; i < users.size(); i++){
			User curUser = users.get(i);
			ContentValues values = new ContentValues();
			values.put("id", curUser.getId());
			values.put("username", curUser.getUsername());
			values.put("firstname", curUser.getFirstName());
			values.put("lastname", curUser.getLastName());
			values.put("email", curUser.getEmail());
			try{
				databaseW.insertOrThrow(TABLE_USERS, null, values);
			}
			catch (SQLiteConstraintException e){
				Log.i("Wallet Constraint Exception", curUser.getId() + " duplicate");
			}
		}
		databaseW.close();
	}
	
	public ArrayList<String> getUserNames(){
		SQLiteDatabase databaseR = this.getReadableDatabase();
		String[] columns = {
				COLUMN_USERNAME
		};
		
		Cursor cursor = databaseR.query(TABLE_USERS, columns, null, null, null, null, null);
		
		if(cursor.getCount() >0)
        {
            ArrayList<String> str = new ArrayList<String>();
            
            while (cursor.moveToNext())
            {
                 str.add( cursor.getString(cursor.getColumnIndex(COLUMN_USERNAME)) );
             }
            databaseR.close();
            return str;
        }
        else
        {
        	databaseR.close();
            return null;
        }
	}
	
	public Boolean containsId(Integer id){
		try {
			checkAccess.acquire();
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		SQLiteDatabase databaseR = this.getReadableDatabase();
		String[] columns = {
				COLUMN_ID
		};
		
		String whereClause = "id = ?";
		String[] whereArgs = new String[]{
				id.toString()
		};
		
		Cursor cursor = databaseR.query(TABLE_USERS, columns, whereClause, whereArgs, null, null, null);
		
		if(cursor.getCount() > 0){
			databaseR.close();
			checkAccess.release();
			return true;
		}
		else{
			databaseR.close();
			checkAccess.release();
			return false;
		}
	}

	
}
