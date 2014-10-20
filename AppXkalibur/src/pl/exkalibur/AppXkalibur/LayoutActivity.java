package pl.exkalibur.AppXkalibur;

import android.app.ActionBar;
import android.app.ActionBar.Tab;
import android.app.Activity;
import android.app.Fragment;
import android.app.FragmentTransaction;
import android.os.Bundle;
import android.util.Log;

import java.util.ArrayList;
import java.util.Arrays;

public class LayoutActivity extends Activity {

    private static final String DIAS_TABSTRING = "Don Dias";
    private static final String ELIZA_TABSTRING = "Eliza";
    protected static final String THUMBNAILS_IDS = "thumbnailsIDs";

    private ArrayList<Integer> mThumnailsDias = new ArrayList<Integer>(
            Arrays.asList(R.drawable.image11, R.drawable.image12, R.drawable.image13, R.drawable.image14, R.drawable.image15, R.drawable.image16, R.drawable.image17, R.drawable.image18, R.drawable.image18, R.drawable.image19, R.drawable.image20 )
    );

    private ArrayList<Integer> mThumnailsEliza = new ArrayList<Integer>(
            Arrays.asList(R.drawable.eliza11, R.drawable.eliza12, R.drawable.eliza13, R.drawable.eliza14, R.drawable.eliza15, R.drawable.eliza16,R.drawable.eliza17, R.drawable.eliza18, R.drawable.eliza19, R.drawable.eliza19, R.drawable.eliza20)
    );

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);

        final ActionBar tabBar = getActionBar();
        tabBar.setNavigationMode(ActionBar.NAVIGATION_MODE_TABS);

        //creat a GridFragment for Dias thumbnail
        GridFragment diasFrag = new GridFragment();

        Bundle args = new Bundle();
        args.putIntegerArrayList(THUMBNAILS_IDS, mThumnailsDias);
        diasFrag.setArguments(args);

        tabBar.addTab(tabBar.newTab().setText(DIAS_TABSTRING).setTabListener(new TabListener(diasFrag)));

        //create a GridFragment for Eliza  thumbnail
        GridFragment elizaFrag = new GridFragment();

        args = new Bundle();
        args.putIntegerArrayList(THUMBNAILS_IDS, mThumnailsEliza);
        elizaFrag.setArguments(args);

        tabBar.addTab(tabBar.newTab().setText(ELIZA_TABSTRING).setTabListener(new TabListener(elizaFrag)));
    }

    //class which will handle user interaction with tab
    public static class TabListener implements ActionBar.TabListener {

        private static final String TAG = "TabListener";
        private final Fragment mFragment;

        public TabListener(Fragment fragment) {
            mFragment = fragment;
        }

        //when tab is selected, changing visibility fragment
        @Override
        public void onTabSelected(Tab tab, FragmentTransaction ft) {

            Log.i(TAG, "onSelectedTab");

            if(null != mFragment){

                ft.replace(R.id.fragment_container, mFragment);
            }
        }

        //when tab is unselected, remove currently visible fragment
        @Override
        public void onTabUnselected(Tab tab, FragmentTransaction ft) {

            if(null != mFragment){

                ft.remove(mFragment);
            }
        }

        @Override
        public void onTabReselected(Tab tab, FragmentTransaction ft) {

        }
    }
}
