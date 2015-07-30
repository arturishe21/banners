<?php namespace Vis\Banners\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

use Vis\Banners\BannerArea;
use Vis\Banners\Banner;

class PlatformsController extends \BaseController
{
    /*
     * start page in cms
     */
    public function fetchIndex()
    {
        $allpage = BannerArea::orderBy('created_at', "desc")->paginate(20);

        if (Request::ajax()) {
            $view = "banners::part.banners_area_center";
        } else {
            $view = 'banners::banners_area';
        }

        return View::make($view)
            ->with('title', Config::get('banners::ploshad.title_page'))
            ->with("data", $allpage);
    } // end fetchIndex

    /*
     * save banner area
     */
    public function doSaveArea()
    {
        parse_str(Input::get('data'), $data);

        $isValidation = BannerArea::isValid($data, $data['id']);
        if ($isValidation) {
            return $isValidation;
        }

        BannerArea::updateOrCreate(['id'=>$data['id']], $data);
        Banner::flush();

        $ok_messages = $data['id'] !=0 ? "Площадка изменена" : "Площадка добавлена";

        return Response::json(
            array(
                'status'        => 'ok',
                'ok_messages'   => $ok_messages
            )
        );
    } // end doSaveArea

    /*
     * edit area window
     */
    public function fetchEditArea()
    {
        $id = Input::get("id");
        if (is_numeric($id)) {
            $page = BannerArea::find($id);

            return View::make('banners::part.form_area')->with('info', $page);
        }
    } // end fetchEditArea

    //create area window
    public function fetchCreateArea()
    {
        return View::make('banners::part.form_area');
    } // end fetchCreateArea

    /*
     * delete area
     */
    public function doDeleteArea()
    {
        $id_page = Input::get("id");
        BannerArea::find($id_page)->delete();
        Banner::flush();

        return Response::json(array('status' => 'ok'));
    } //end doDeleteArea

}