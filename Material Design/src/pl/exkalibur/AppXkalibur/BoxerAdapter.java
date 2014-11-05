package pl.exkalibur.AppXkalibur;

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import java.util.List;

/**
 * Created by wojder on 04.11.14.
 */
public class BoxerAdapter extends RecyclerView.Adapter<BoxerAdapter.ViewHolder> {

    private List<Boxer> boxers;
    private Context mContext;
    private int rowLayout;

    public BoxerAdapter(List<Boxer> boxers, int rowLayout, Context context) {

        this.boxers = boxers;
        this.rowLayout = rowLayout;
        this.mContext = context;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(rowLayout, viewGroup, false);
        return new ViewHolder(v);
    }

    @Override
    public void onBindViewHolder(ViewHolder viewHolder, int i) {

        Boxer boxer = boxers.get(i);
        viewHolder.boxerName.setText(boxer.name);
        viewHolder.boxerImage.setImageDrawable(mContext.getDrawable(boxer.getImageResourceId(mContext)));

    }

    @Override
    public int getItemCount() {

        return boxers==null ? 0:boxers.size();

    }
        public static class ViewHolder extends RecyclerView.ViewHolder {

        public TextView boxerName;
        public ImageView boxerImage;

        public ViewHolder(View itemView) {
            super(itemView);

            boxerName = (TextView) itemView.findViewById(R.id.boxerName);
            boxerImage = (ImageView) itemView.findViewById(R.id.boxerImage);
        }
    }

}
