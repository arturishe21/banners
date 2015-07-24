<?php namespace Vis\Banners;

use Eloquent;
use DB;

class BannerArea extends Eloquent {

    protected $table = 'banners_platforms';

    public static $rules = array(
        'slug' => 'required|unique:banners_platforms,slug,',
        'title'=> 'required',
        'width'=> 'required|numeric|max:2000',
        'height'=> 'required|numeric|max:2000',
    );
    protected $fillable = array('title', 'slug', 'width','height');

    //get banner
    public function banners()
    {
        $this_time = date("Y-m-d G:i:s");
        $results = DB::select(
                        DB::raw("SELECT banners.* FROM
                                  banners

                                   WHERE
                                  id_banners_platform = '".$this->id."' and
                                  path_file != '' and
                                  is_show = 1 and
                                    ((show_start < '$this_time' or show_finish> '$this_time') or show_finish = '0000-00-00 00:00:00' or is_show_all='1') ")
                    );
         shuffle($results);

        if (isset($results[0])) {
            return $results[0];
        }

        return false;
    }
}