package com.bizarre.dingdatt.database;

import android.database.sqlite.SQLiteDatabase;

public class DDatabase {

	SQLiteDatabase db = null;

	public DDatabase() {

	}

	public void openDatabase() {
		db = SQLiteDatabase.openDatabase(DingDattDatabase.DATABASE, null,
				SQLiteDatabase.OPEN_READWRITE);
	}

	public void execute(String sql) {
		db.execSQL(sql);
	}

	public void close() {
		db.close();
	}
}
