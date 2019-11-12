<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    public function __construct(){

        $this->middleware('api.auth',['except'=>[
            'index',
            'show',
            'getImage',
            'getPostByCategory',
            'getPostByUser'
            ]]);
    }

    public function index(){
        $posts = Post::all()->Load('category');
        return response()->json([
            'code'=> '200',
            'status'=> 'success',
            'post'=> $posts
        ],200);

    }

    public function show ($id){
        $post = Post::find($id)->Load('category');

        if(is_object($post)){

          $data=[
            'code'=> 200,
            'status'=> 'success',
            'post'=> $post
          ];
        }else{

            $data=[
                'code'=> 400,
                'status'=> 'error',
                'message'=> 'el post no existe'
            ];

        }
        return response()->json($data,$data['code']);
    }

    public function store(Request $request){
        //recoger datos por post 
        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);
        if(!empty($params_array)){
            $user=$this->getIdentity($request);

            $validate = \Validator::make($params_array,[
                'title'=> 'required',
                'content'=>'required',
                'category_id' =>'required',
                'image'=>'required'
            ]);
            

            if($validate->fails()){
                $data=[
                    'code'=>400,
                    'status'=> 'error',
                    'message'=> 'no se ha guardado el post,faltan datos'
                ];
            }else{
                //guardar el post
                $post = new Post();

                $post->user_id = $user->sub;
                $post->category_id =  $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image =$params->image;

                $post->save();

                $data=[ 
                    'code'=>200,
                    'status'=> 'success',
                    'message'=> $post
                ];
            }

            }else{
                   $data=[ 
                        'code'=>400,
                        'status'=> 'error',
                        'message'=> 'envia los datos correctamente'
                    ];

        }

        return response()->json($data,$data['code']);
    }
        

    public function update($id , Request $request){
        //recoger los datos por psot
        $user=$this->getIdentity($request);

        $json = $request->input('json',null);
        $params_array = json_decode($json,true);
        ///datos parra devolver
        $data=array(
            'code'=>400,
            'status'=>'error',
            'message'=>'datos enviados incorrectamente '
        );

    if(!empty($params_array)){


        
        //validar los datos 
        $validate = \Validator::make($params_array,[
            'title'=>'required',
            'content'=>'required',
            'category_id'=>'required'
        ]);
        if($validate->fails()){
            $data['errors']=$validate->errors();
            return  response()->json($data,$data['code']);
        }
        //eliminar lo que no queremos actulaizar 
        unset($params_array['id']);
        unset($params_array['user_id']);
        unset($params_array['created_at']);
        unset($params_array['user']);
      
        //bucar registro
        $post= Post::where('id',$id)
        ->where('user_id',$user->sub)
        ->first();

        if(!empty($post)&& is_object($post)){

        $post ->update($params_array);
                // devolver los datos 
                $data=array(
                    'code'=>200,
                    'status'=>'success',
                    'post'=>$post,
                    'changes'=>$params_array
                );
        }


        // actualizatr el registro 
        // $where =[
        //     'id'=>$id,
        //     'user_id'=> $user->sub
        // ];
        

   

        return response()->json($data,$data['code']);
    }

    }


    public function destroy($id , Request $request){
        //conseguir ususario identificado
        $user=$this->getIdentity($request);

        //conseguir  el registro 
        $post= Post::where('id',$id)
                    ->where('user_id',$user->sub)
                    ->first();

        if(!empty($post)){
        //borrarlo 
                $post->delete();
                //devolver datos 

                $data=[
                    'code'=>200,
                    'status'=>'success',
                    'post'=> $post
                ];
        }else{
                $data=[
                    'code'=>404,
                    'status'=>'error',
                    'message'=> 'el post no existe'
                ];

        }
        

        return response()->json($data,$data['code']);

    }

    private function getIdentity ($request){

        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization',null);

        $user = $jwtAuth->CheckToken($token,true);
        return $user;

    }
    
    
    public function upload( Request $request){
        //recoger la imagen de la peticion 
        $image = $request->file('file0');

        //validar imagen n
        $validate = \Validator::make($request->all(),[
            'file0'=> 'required|image|mimes:jpg,jpeg,png,gif',
        ]);
        //guardar imagen

        if(!$image || $validate->fails()){
            
            $data=array(
                'code'=>404,
                'status'=>'error',
                'message'=>'error añ subir la imagen'
            );

        }else{
            $image_name =time().$image->getClientOriginalName();

              \Storage::disk('image')->put($image_name,\File::get($image));
            
            $data=array(
                'code'=>200,
                'status'=>'success',
                'message'=> $image_name
            );
        }

        // devolver datos 
        return response()->json($data,$data['code']);

    }


    public function getImage($filename){

        $isset=\Storage::disk('image')->exists($filename);
        if($isset){
        $file= \Storage::disk('image')->get($filename);
        return new Response($file,200);

        }else{
            $data = array(
                'code'=> 404,
                'status'=>'error',
                'image'=> 'la imagen no existe',
               
             );

        }
      
        return response()->json($data,$data['code']);


    }


    public function getPostByCategory($id){
        $posts= Post::where('category_id',$id)->get();
        
        return response()->json([
            'status'=>'success',
            'posts'=>$posts,
        ],200);

    }

    public function getPostByUser($id){
        $posts= Post::where('user_id',$id)->get();
        
        return response()->json([
            'status'=>'success',
            'posts'=>$posts,
        ],200);

    }

}
