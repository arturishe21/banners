<?php namespace  Vis\Banners;

use Eloquent;
use View;

class Banner extends Eloquent {

    protected $table = 'banners';

    public static $rules = array(
        'file' => 'required|mimes:jpg,jpeg,gif,png,swf|max:5000',
        'link'=> 'required',
        'id' => 'required|numeric',
        'title' => 'required',
        'banners_area_id' => 'required|numeric'
    );

    //show banner on site
    public static function show($slug)
    {
        if ($slug) {
            $area =  BannerArea::where("slug", $slug)->first();
            $banners = $area->banners();

            if ($banners != false) {
                $ban = Banner::find($banners['id'])->increment("hit_count");
                $target = $banners['is_target_blank'] ? "target='_blank'" : "";

                return View::make('banners::show')
                    ->with('target',$target)
                    ->with("banners",$banners)
                    ->with("area",$area);
            }
        }
    }

    /*
     * Возвращает дату с datetime
     */
    public static function fetchData($data)
    {
        if ($data) {
           $arr_data =  explode(" ", $data);
            return $arr_data[0];
        }
    } //end fetchData

    /*
     * Возвращает время с datetime
     */
    public static function fetchTime($data)
    {
        if ($data) {
            $arr_data = explode(" ", $data);
            return $arr_data[1];
        }
    } //end fetchData

    /*
     * get area
     */
    public function area()
    {
        return BannerArea::find($this->id_banners_platform);
    } // end area()


}