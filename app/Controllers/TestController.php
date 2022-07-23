<?php

namespace App\Controllers;

use Core\Database\DB;
use Core\Http\Request;
use Core\Database\Builder;
use Morilog\Jalali\Jalalian;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('Auth');
    }


    function pingAddress($ip) {
        $fp = fSockOpen($ip,80);
        if($fp) { $status=0; fclose($fp); } else { $status=1; }
    }


    /**
     * @throws \Exception
     */
    public function myTest(Request $request)
    {
//        $q = DB::table('employees')
//            ->where('name','like','%حمiید%')
//            ->doesntExist();

        //$res = DB::table('employees')->whereId(11)->decrement('family_info_update_ts');


//        $res = DB::table('employees')->insertGetId(
//            [
//                'gender'          => '1',
//                'name'            => '2',
//                'family'          => '3',
//                'full_name'       => '4',
//                'shenasnameh'     => '5',
//                'melli'           => '6',
//                'birthday'        => '7',
//                'birthplace'      => '8',
//                'father_name'     => '9',
//                'mobile'          => '10',
//                'phone'           => '11',
//                'email'           => '12',
//                'address'         => '13',
//                'skills'          => '14',
//                'reg_date_time'   => Jalalian::forge('now')->format('Y/m/d H:i:s')
//            ]
//        );


        $res = DB::table('employees')->updateOrInsert(
            [
                'melli'           => '44448',
                'mobile'          => '10'
            ],
            [
                'gender'          => '1',
                'name'            => 'ff',
                'family'          => 'ff',
                'full_name'       => 'ff',
                'shenasnameh'     => 'ff',
                'melli'           => '4444',
                'birthday'        => '76',
                'birthplace'      => '86',
                'father_name'     => '96',
                'mobile'          => '10',
                'phone'           => '116',
                'email'           => '126',
                'address'         => '136',
                'skills'          => '146',
                'reg_date_time'   => Jalalian::forge('now')->format('Y/m/d H:i:s')
            ]
        );





//        echo DB::table('employees')->LastInsertedId();

        var_dump($res);

//        foreach ($q as $a)
//            echo $a->id;

        /*dd($id);

        //dd($Request->all());

        //app('Session')->get('test');
        Session()->put('test','11');
        Session()->put('test2','11');

        print_r(Session()->all());*/


    }


}
