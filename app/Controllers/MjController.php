<?php

namespace App\Controllers;

use Core\Blade\Blade;
use Core\Blade\View;
use Core\Database\DB;
use Core\DataTables\DataTables;
use Core\FileSystem\FileSystem;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;
use Exception;

class MjController extends Controller
{
    public function __construct()
    {
        $this->middleware('Auth')->guard('Account')->except('test_blade','do_login')->makeSafe();
    }


    /**
     * @throws Exception
     */
    public function do_login($id, Request $req)
    {
        $x = Auth::guard('Account')->attempt(['username'=>'801204','password'=>'989796']);
        print_r($x);
    }


    public function logout()
    {
        $x = Auth::guard('Account')->logout();
        print_r($x);
    }

    public function test_blade()
    {
        echo csrf_field();
        $x = Auth::guard('Account')->check();
        var_dump($x);

        print_r(Auth::user());

        die('hello');
        return view('home')->with('name','ahmad')->with('family','khaliq');
    }

    public function test_table()
    {

        $users = DB::table('accounts')->select(['id','full_name','username','last_visit','employment_date','termination_date','is_locked']);

        return Datatables::of($users)
            ->addColumn('test', function ($DATA)
            {
                return $DATA->id+1;
            })
            ->editColumn('last_visit', function ($DATA)
            {
                return '<center>' . $DATA->last_visit . '<center>';
            })
            ->editColumn('is_locked', function ($DATA)
            {
                return ($DATA->is_locked == 1) ? '<span>enable</span>' : '<span>disable</span>';
            })
            ->rawColumns([ 'is_locked', 'section_id', 'marriage', 'children',  'full_name']) //'last_visit',
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
