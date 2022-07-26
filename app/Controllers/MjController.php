<?php

namespace App\Controllers;

use Core\Blade\Blade;
use Core\Blade\View;
use Core\Database\DB;
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

        //$person=(object)['age'=>150];
        //dd(view('home',compact('person')));
        return view('home')->with('name','ahmad')->with('family','ahmasd');


    }

    public function test_table()
    {

//        $users=
//            [
//                ["id"=>2,"full_name"=>"mojtaba","username"=>187,"last_visit"=>"1400/06/10 11:43:46","employment_date"=>"0920699278","termination_date"=>"09154074776","is_locked"=>1]
//                ,["id"=>3,"full_name"=>"ahmad","username"=>184,"last_visit"=>"1399/11/02 10:51:14","employment_date"=>"0923036997","termination_date"=>"09396853635","is_locked"=>0]
//                ,["id"=>5,"full_name"=>"hamid","username"=>2,"last_visit"=>"1400/03/16 16:27:36","employment_date"=>"0941400451","termination_date"=>"09151189747","is_locked"=>1]
//            ];
//        $users= collect($users);
        $users = DB::table('accounts')->select(['id','full_name','username','last_visit','employment_date','termination_date','is_locked']);

        return Datatables::of($users)
//            ->editColumn('full_name', function ($DATA) {
//
//                return 'sss';
//            })
            ->addColumn('childroon', function ($DATA)
            {
                return '1';
            })
            ->editColumn('last_visit', function ($DATA)
            {
                return '<center>' . $DATA->last_visit . '<center>';
            })
            ->editColumn('is_locked', function ($DATA)
            {
                return ($DATA->is_locked == 1) ? '<span>enable</span>' : '<span>disable</span>';
            })
            ->rawColumns([ 'is_locked', 'section_id', 'marriage', 'children', 'last_visit', 'full_name'])
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
