package pl.exkalibur.AppXkalibur;

import android.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.GridView;

import java.util.List;

/**
 * Created by wojder on 20.10.14.
 */
public class GridFragment extends Fragment {
    protected static final String EXTRA_RES_ID = "POS";

    private List<Integer> mThumbnailsIDs;
    private GridView mGridView;

    @Override
    public void onCreate (Bundle saveInstanceState) {
        super.onCreate(saveInstanceState);

        mThumbnailsIDs = getArguments().getIntegerArrayList(LayoutActivity.THUMBNAILS_IDS);
    }

    public void onActivityCreated(Bundle saveInstanceState){
        super.onActivityCreated(saveInstanceState);

        mGridView.setAdapter(new ImageAdapter(getActivity(), mThumbnailsIDs));
        mGridView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                Intent intent = new Intent(getActivity(), ImageViewActivity.class);
                intent.putExtra(EXTRA_RES_ID, (int) id);
                startActivity(intent);
            }
        });
    }

    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle saveInstanceState) {
        View view = inflater.inflate(R.layout.grid_fragment, container, false);
        mGridView=  (GridView) view.findViewById(R.id.gridview);
        return view;
        }
    }

