package com.bizarre.dingdatt;

import android.app.Activity;

import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

public class TopicDialogActivity extends Activity {

	EditText topic;
	Button submit;
	String stopic = "";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_topic_dialog);

		topic = (EditText) findViewById(R.id.topic);
		submit = (Button) findViewById(R.id.submit);

		Bundle bundle = getIntent().getExtras();

		if (bundle != null) {

			stopic = bundle.getString("topic");
			topic.setText(stopic);
		}

		topic.setTextColor(Color.rgb(0, 0, 0));
		submit.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				if (topic.getText().toString().length() != 0) {
					Intent intent = new Intent();
					intent.putExtra("topic", topic.getText().toString().trim());
					setResult(RESULT_OK, intent);
					finish();
				} else {
					Toast.makeText(TopicDialogActivity.this,
							getString(R.string.enter_valid_topic),
							Toast.LENGTH_SHORT).show();
				}
			}
		});
	}
}
