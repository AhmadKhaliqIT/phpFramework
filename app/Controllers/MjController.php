<?php

namespace App\Controllers;

use Core\Blade\Blade;
use Core\Blade\View;
use Core\DataTables\DataTables;
use Core\FileSystem\FileSystem;
use Core\Http\Response;

class MjController extends Controller
{
    public function __construct()
    {

     }

    public function test_blade()
    {

        $person=(object)['age'=>150];
        dd(view('home',compact('person')));
        return view('home',compact('person'))->with();

    }

    public function test_table()
    {

        $users=
            [
                ["id"=>2,"full_name"=>"mojtaba","username"=>187,"last_visit"=>"1400/06/10 11:43:46","melli"=>"0920699278","mobile"=>"09154074776","active"=>1]
                ,["id"=>3,"full_name"=>"ahmad","username"=>184,"last_visit"=>"1399/11/02 10:51:14","melli"=>"0923036997","mobile"=>"09396853635","active"=>0]
                ,["id"=>5,"full_name"=>"hamid","username"=>2,"last_visit"=>"1400/03/16 16:27:36","melli"=>"0941400451","mobile"=>"09151189747","active"=>1]
            ];
        return Datatables::of(collect($users))


            ->addColumn('actions', function ($DATA)
            {
                return $DATA['id'];
            })
            ->editColumn('full_name', function ($DATA) {

                return 'sss';
            })
            ->addColumn('marriage', function ($DATA)
            {
                $marriage_count=0;
                return ($marriage_count==0? 'مجرد' : 'متاهل');
            })
            ->addColumn('children', function ($DATA)
            {
                return '1';
            })
            ->editColumn('last_visit', function ($DATA)
            {
                return '<center>' . $DATA['last_visit'] . '<center>';
            })
            ->editColumn('active', function ($DATA)
            {
                return ($DATA['active'] == 1) ? '<span>enable</span>' : '<span>disable</span>';
            })
            ->rawColumns([ 'active', 'section_id', 'marriage', 'children', 'last_visit', 'full_name'])

            ->make();


    }


    public function test_response_image()
    {

        $path='ae847d71a5cc7d457a7fff7f14e99943d154c179_1607951697.jpg';




        $file = FileSystem::get($path);
        $type = FileSystem::mimeType($path);




        return (new Response($file, 200))
            ->header('Content-Type', $type);



    }


    public function test_response_json()
    {


        return response()->json(['a'=>'ff']);



    }


}
