<?php namespace Vis\Banners\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

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

        if (Request::ajax()) {
            $view = "banners::part.banners_banner_center";
        } else {
            $view = 'banners::banners';
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
            $info = Banner::find($id);
            $bannerarea = BannerArea::orderBy("created_at", "DESC")->get();

            return View::make('banners::part.form_banner', compact("info", "bannerarea"));
        }
    } // end fetchEditBanners

    /*
     * save banner
     */
    public function doSaveBanner()
    {
        parse_str(Input::get('data'), $data);

        $isNotValidation = Banner::isNotValid($data);
        if ($isNotValidation) {
            return $isNotValidation;
        }

        $data = Banner::replaceParams($data);

        $uploadFile = Banner::uploadFile();

        if ($uploadFile) {
            $data['path_file'] = $uploadFile;
        }

        Banner::updateOrCreate(['id' => $data['id']], $data);

        Banner::flush();

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
        $bannerarea = BannerArea::orderBy("created_at", "DESC")->get();

        return View::make('banners::part.form_banner', compact("bannerarea"));
    }// end fetchCreateBanner

    /*
     * +1 statistic click
     */
    public function doIncrementClickCount()
    {
        $id = Input::get("id");
        if (is_numeric($id)) {
            Banner::find($id)->increment('click_count');
        }
    }//end doIncrementClickCount

    /*
     * delete banner
     */
    public function doDeleteBanner()
    {
        $id = Input::get("id");
        Banner::find($id)->delete();
        Banner::flush();

        return Response::json(array('status' => 'ok'));
    } //end doDeleteBanner
}