<?php
namespace Core\Collection;
//use Core\Collection\Collection;

use Core\Core;
use Core\Http\Request;
use Core\Blade\Blade;
//require_once 'core\Blade\blade_helper.php';
class test{

    public function test()
    {

        $array=[
            ['account_id' => 'account-x15', 'product' => 'Desk', 'price' => '50'],
            ['account_id' => 'account-x11', 'product' => 'Chair', 'price' => '50'],
            ['account_id' => 'account-x5411', 'product' => 'Bookcase', 'price' => '40'],
        ];

        $collection = collect($array);
        $collection= $collection->map(function ($item,$key){
            $item['key']=$key;
            return $item;
        });
       //dd($collection);
    }

    public function test_request()
    {


        $request = \Core()->Request();

        $request->validate([
            'employee_image_file'     => 'max:10',
            'name'     => 'between:2,100|required',
            'family'   => 'required',
            'melli'    => 'required|numeric',
            'username' => 'required',
            'password' => 'required',
        ]);
    }

    public function test_response(){
        $arr=[
            'a'=>10
        ];
        return redirect()->back()->with('a',$arr)->with('b',5);
    }

    public function test_response_link()
    {
        echo '<a href="https://fw.it/test_response">test</a><br>';
        echo '<a href="https://fw.it/test_redirect_route">test 25</a>';
       //dd(session()->all());
    }
    
    public function test_redirect_route()
    {
        return redirect()->route('test_555');
    }

    public function test_blade()
    {
        $template = new Blade(BASE_PATH.'/views/', []);
        echo $template->render('home.blade.php',['title1' => 'mmmmm']);
    }

}