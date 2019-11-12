<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\User;

class UserController extends Controller
{
    //
    public function pruebas (Request $request){

        return "accion de pruebas de user controller";
    }

    public function register(Request $request){
        //recoger los datos del ususario por POST 
        $json = $request->input('json',null);

        $params=json_decode($json);//objeto
        $params_array=json_decode($json,true);//array
        //limpiar datos 

        if(!empty($params) && !empty($params_array)){
        $params_array=array_map('trim',$params_array);
       
        //Validar datos 
        $validate = \Validator::make($params_array,[
            'name'      =>  'required|alpha',
            'surname'   =>  'required|alpha',
            'email'     =>  'required|email|unique:users',  //existe el ususario  ? unique
            'password'  =>  'required'

        ]);

        if($validate->fails()){
            $data= array(
                'status'=> 'error',
                'code'=> 404,
                'message'=> 'El usuario no se a creado',
                'errors' => $validate->errors()
           );
        
        }else{
            //Validacion pasada con exito 

                
                //cifrar la contraceña
                $pwd=hash('sha256',$params->password);
                //Crear el usuario 
                $user= new User();
                $user->name=$params_array['name'];
                $user->surname=$params_array['surname'];
                $user->email=$params_array['email'];
                $user->password=$pwd;
                $user->role='ROLE_USER';
                //guardar usuario
                $user->save();


            $data= array(
                'status'=> 'success',
                'code'=> 200,
                'message'=> 'el ususario se a creado correctamente'
                
            );
        }
    }else{ 
         $data= array(
        'status'=> 'error',
        'code'=> 404,
        'message'=> 'los datos no son correctos',
       
         );
    }
       return response()->json($data,$data['code']);
    }



    public function login(Request $request){
        $jwtAuth = new \JwtAuth();
        //recibir el post
        $json=$request->input('json',null);
        $params = json_decode($json);
        $params_array=json_decode($json,true);
        //validar los datos 
        $validate = \Validator::make($params_array,[
            'email'     =>  'required|email',  //existe el ususario  ? unique
            'password'  =>  'required'

        ]);

        if($validate->fails()){
            $signup= array(
                'status'=> 'error',
                'code'=> 404,
                'message'=> 'El usuario no se a podido logear',
                'errors' => $validate->errors()
           );
        
        }else{
            //cifrar la contraseña
            $pwd=hash('sha256',$params->password);
            //devolver token  o datos
             $signup = $jwtAuth->signup($params->email,$pwd);
            if(!empty($params->gettoken)){
                $signup = $jwtAuth->signup($params->email,$pwd,true);
            }
        }

        return response()->json($signup,200);
    }

    public function update(Request $request){
        //comprobar la autorsacion 
        $token = $request->header('Authorization');
        
        $jwtAuth= new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
               //recoger los datos 
         $json =$request->input('json',null);
         $params_array = json_decode($json,true);

        if($checkToken && !empty($params_array)){

            //actualizar eñ ususario 

     
            //sacar ususario autentificado 
            $user=$jwtAuth->checkToken($token,true);
            // validar los datos 
           
            $validate = \Validator::make($params_array,[
                'name'      =>  'required|alpha',
                'surname'   =>  'required|alpha',
                'email'     =>  'required|email|unique:users'.$user->sub  //existe el ususario  ? unique
                
    
    
            ]);
            //quitar campos que no quiero se actualizaran 
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            //actualizar ususario
            $user_update= User::where('id',$user->sub)->update($params_array);


            //devolver el array con los datos
            $data = array(
                'code'=> 200,
                'status'=>'success',
                'user'=> $user,
                'changes'=> $params_array
               );

        }else{
            $data = array(
                    'code'=> 400,
                    'status'=>'error',
                    'message'=> 'El usuario no se identificado'
            );

        }
      return response()->json($data,$data['code']);
    }

    public function upload(Request $request){

        //recoer datos de la peticion 
        $image= $request->file('file0');
        //validar imagen 
        $validate = \Validator::make($request->all(),[
            'file0'=> 'required|image|mimes:jpg,jpeg,png,gif',
        ]);

        //guardar imagen 
        if(!$image || $validate->fails()){
             $data = array(
                'code'=> 400,
                'status'=>'error',
                'message'=> 'error al subir la imagen'
             );

        }else{
         
             $image_name=time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name,\File::get($image));

            $data = array(
                'code'=> 200,
                'status'=>'success',
                'image'=>$image_name,
               
             );
        }
        
    return response()->json($data,$data['code']);
    }

    public function getImage($filename){

        $isset=\Storage::disk('users')->exists($filename);
        if($isset){
        $file= \Storage::disk('users')->get($filename);
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

    public function detail($id){
        $user = User::find($id);

        if(is_object($user)){
            $data=array(
                'code'=> 200,
                'status'=> 'success',
                'user'=> $user
            );

        }else{
            $data = array(
                'code'=> 404,
                'status'=>'error',
                'image'=> 'el ususario no existe',
               
             );


        }
        return response()->json($data,$data['code']);

    }
}
