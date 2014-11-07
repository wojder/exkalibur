package pl.exkalibur.AppXkalibur;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by wojder on 05.11.14.
 */
public class BoxerManager {

    private static String[] boxerArray = {"Dias Exkalibur", "Eliza", "Odwet", "Utopia","Italia", "Ofra", "Amanda"};
    private static String loremIpsum ="Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut ";
    private static BoxerManager mInstance;
    private ArrayList<Boxer> boxers;

    public static BoxerManager getInstance() {

        if (mInstance == null) {

            mInstance = new BoxerManager();
        }
        return mInstance;
    }

    public List<Boxer> getBoxer(){

    if (boxers == null) {

        boxers = new ArrayList<Boxer>();

        for(String boxerName : boxerArray) {

            Boxer boxer = new Boxer();
            boxer.name = boxerName;
            boxer.description = loremIpsum;
            boxer.imageName = boxerName.replaceAll("\\s+","").toLowerCase();
            boxers.add(boxer);
        }
    }
        return boxers;
    }
}
