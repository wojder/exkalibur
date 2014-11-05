package pl.exkalibur.AppXkalibur;

import android.content.Context;

/**
 * Created by wojder on 05.11.14.
 */
public class Boxer {

    public String name;
    public String description;
    public String imageName;

    public int getImageResourceId(Context context) {

        try {
            return context.getResources().getIdentifier(this.imageName, "drawable", context.getPackageName());
        } catch (Exception e) {
            e.printStackTrace();
            return -1;
        }
    }
}
