package pl.exkalibur.AppXkalibur;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.widget.DefaultItemAnimator;
import android.support.v7.widget.LinearLayoutManager;
import android.support.v7.widget.RecyclerView;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;

public class MainActivity extends Activity {

    RecyclerView mRecyclerView;
    RecyclerView.Adapter mAdapter;

    /**
     * Called when the activity is first created.
     */

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);

        mRecyclerView = (RecyclerView) findViewById(R.id.list);
        mRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        mRecyclerView.setItemAnimator(new DefaultItemAnimator());

        mAdapter = new BoxerAdapter(BoxerManager.getInstance().getBoxer(), R.layout.boxers_row, this);

        mRecyclerView.setAdapter(mAdapter);

    }

        public void showBoxer(View view){

            Intent intent = new Intent(this, BoxerInfo.class);
            startActivity(intent);
        }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {

        getMenuInflater().inflate(R.menu.boxer, menu);

        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {

        int id = item.getItemId();

        if (id == R.id.action_settings){

            return true;
        }

        return super.onOptionsItemSelected(item);
    }
}
