package com.bizarre.dingdatt;

import com.bizarre.dingdatt.imageloader.ImageLoader;

import android.app.Activity;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.MediaController;
import android.widget.VideoView;

public class ViewGalleryActivity extends Activity {

	String url = "";
	String type = "";
	ImageView imageView;
	VideoView video;
	private MediaController mediaControls;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_gallery);

		Bundle bundle = getIntent().getExtras();

		if (bundle != null) {

			url = bundle.getString("loc");
			type = bundle.getString("type");
		}

		imageView = (ImageView) findViewById(R.id.image);
		video = (VideoView) findViewById(R.id.video);

		Button back = (Button) findViewById(R.id.back);

		back.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub

				finish();
			}
		});

		if (type.equalsIgnoreCase("p")) {
			imageView.setVisibility(View.VISIBLE);
			video.setVisibility(View.GONE);

			int loader = R.drawable.loader;
			ImageLoader imgLoader = new ImageLoader(this);
			imgLoader.DisplayImage(url, loader, imageView);
		} else if (type.equalsIgnoreCase("v")) {

			imageView.setVisibility(View.GONE);
			video.setVisibility(View.VISIBLE);

			if (mediaControls == null) {
				mediaControls = new MediaController(this);
			}

			video.setMediaController(mediaControls);
			video.setVideoURI(Uri.parse(url));
			video.start();
		}
	}
}
