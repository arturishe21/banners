<?php namespace Vis\Banners\Controllers;

use Config;
use Input;
use Request;
use Response;
use View;
use Validator;
use Vis\Banners\Banner;
use Vis\Banners\BannerArea;

class BannersController extends \BaseController
{
    /*
     * start page in cms
     */
    public function fetchIndex()
    {
        $allpage = Banner::orderBy('created_at', "desc")->paginate(20);

        $view = 'banners::banners';
        if (Request::ajax()) {
            $view = "banners::part.banners_banner_center";
        }

        return View::make($view)
                ->with('title', Config::get('banners::banners.title_page'))
                ->with("data", $allpage);
    }  // end fetchIndex

    /*
     * window edit banner
     */
    public function fetchEditBanner()
    {
        $id = Input::get("id");
        if (is_numeric($id)) {
            $page = Banner::find($id);
            $BannerArea = BannerArea::orderBy("created_at", "DESC")->get();

            return View::make('banners::part.form_banner')
                    ->with('info', $page)
                    ->with('bannerarea', $BannerArea);
        }
    } // end fetchEditBanners

    /*
     * save banner
     */
    public function doSaveBanner()
    {
        $banner_file = Input::file('file');
        parse_str(Input::get('data'), $data);

        if ($banner_file) {
            $data['file'] = $banner_file;
        } else {
            Banner::$rules['file'] = "";
        }

        $validator = Validator::make($data, Banner::$rules);
        if ($validator->fails()) {
            return Response::json( array('status' => 'error', "errors_messages"=>$validator->messages()));
        }

        if ($data['id']==0) {
            $banner = new Banner;
        } else {
            $banner = Banner::find($data['id']);
        }

        $banner->title = $data['title'];
        $banner->link =  $data['link'];
        $banner->id_banners_platform = $data['banners_area_id'];
        $banner->is_show = $data['status'];
        $banner->is_target_blank = $data['target_blank'];
        $banner->is_show_all = $data['is_show_all'];

        $banner->show_start = str_replace(".",":",$data['show_start_data'])." ".$data['show_start_time'];
        $banner->show_finish = str_replace(".",":",$data['show_finish_data'])." ".$data['show_finish_time'];

        if ($banner_file) {
            $destinationPath = "storage/banner";
            $ext = $banner_file->getClientOriginalExtension();
            $hashname = md5(time()) . '.' . $ext;
            $full_path_img = "/" . $destinationPath . '/' . $hashname;
            $upload_success = $banner_file->move($destinationPath, $hashname);
            $banner->path_file = $full_path_img;
        }

        $banner->save();

        return Response::json(
                            array(
                                'status' => 'ok',
                                'ok_messages' => "Баннер сохранен"
                            )
        );
    } // end doSaveBanner

    /*
     * create banner window
     */
    public function fetchCreateBanner()
    {
        $BannerArea = BannerArea::orderBy("created_at", "DESC")->get();

        return View::make('banners::part.form_banner')
                ->with('bannerarea', $BannerArea);
    }// end fetchCreateBanner

    /*
     * +1 statistic click
     */
    public function doIncrementClickCount()
    {
        $id = Input::get("id");
        if (is_numeric($id)) {
            $ban = Banner::find($id)->increment('click_count');
        }
    }//end doIncrementClickCount

    /*
     * delete banner
     */
    public function doDeleteBanner()
    {
        $id = Input::get("id");
        $page = Banner::find($id)->delete();

        return Response::json(array('status' => 'ok'));
    } //end doDeleteBanner
}