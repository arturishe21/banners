<?php namespace Vis\Banners\Controllers;

use Config;
use Input;
use Request;
use Response;
use View;
use Validator;
use Vis\Banners\BannerArea;

class PlatformsController extends \BaseController
{
    /*
     * start page in cms
     */
    public function fetchIndex()
    {
        $allpage = BannerArea::orderBy('created_at', "desc")->paginate(20);
        $view = 'banners::banners_area';
        if (Request::ajax()) {
            $view = "banners::part.banners_area_center";
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

        $rules =  BannerArea::$rules;
        $rules['slug'] .= $data['id'];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return Response::json(
                array(
                    'status'            => 'error',
                    "errors_messages"   => $validator->messages()
                )
            );
        }

        if ($data['id'] != 0) {
            $resource = BannerArea::find($data['id']);
        } else {
            $resource = new BannerArea;
        }

        $resource->fill($data);
        $resource->save();

        $ok_messages = $data['id'] !=0 ?"Площадка изменена":"Площадка добавлена";

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
        $page = BannerArea::find($id_page)->delete();

        return Response::json(array('status' => 'ok'));
    } //end doDeleteArea

}