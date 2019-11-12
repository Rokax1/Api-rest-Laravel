<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{

    public function __construct(){

        $this->middleware('api.auth',['except'=>['index','show']]);
    }
  

    public function index(){
        $categories = Category::all();
        return Response()->json([
            'code'=>200,
            'status'=> 'success',
            'categories' => $categories
        ]);

    }

    public function show($id){

       $category=Category::find($id);

       if(is_object($category)){
        $data=[
            'code'=>200,
            'status'=> 'success',
            'categories' => $category
        ];
       }else{
        $data=[
            'code'=>404,
            'status'=> 'error',
            'message' => 'no existe la categoria'
        ];

       }
       return response()->json($data,$data['code']);
    }


    public function store(Request $request){

        //recoger los datos por post 
        $json=$request->input('json',null);
        $params_array= json_decode($json,true);

        if(!empty($params_array)){

        //validar los datos 
        $validate = \Validator::make($params_array,[
            'name'=> 'required'
        ]);
        //guardar lacategoria
        if($validate->fails()){
            $data =array(
                'code'=>400,
                'status' => 'error',
                'message'=> 'no se ha guardado la categoria'
            );

        }else{
            $category = new Category();
            $category->name = $params_array['name'];
            $category->save();

        }
        //devolver el resultado 
        $data =array(
            'code'=>400,
            'status' => 'success',
            'category'=> $category
        );
    }else{
        $data =array(
            'code'=>400,
            'status' => 'success',
            'message'=> 'no has enviado una categoria'
        );



    }

        return response()->json($data,$data['code']);

    }

    public function update($id , Request $request){

        //regoger los datos que llegan por post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if(!empty($params_array)){
            //validar los datos 
            $validate = \Validator::make($params_array,[
                'name'=>'required'
            ]);

        //quitar lo que no quiero actualizar 
        unset($params_array['id']);
        unset($params_array['created_at']);
        //actualizar el registro 
        $category = Category::where('id',$id)->update($params_array);

            $data =array(
                'code'=>200,
                'status' => 'success',
                'category'=> $params_array
            );

        }else{
            $data =array(
                'code'=>400,
                'status' => 'error',
                'message'=> 'no has enviado una categoria'
            );
        }
        
        //devolever los datos 
        return response()->json($data,$data['code']);

    }
}
