<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/pruebas',function(){
//     return '<h2>XDDDDDDDDDDDDDDDDd </h2>';
// });

// Route::get('/ORM','PruebasController@testORM');


// Route::get('/usuario/pruebas','UserController@pruebas');
// Route::get('/categoria/pruebas','CategoryController@pruebas');
// Route::get('/post/pruebas','PostController@pruebas');

/* METODOS HTTP
    GET -> conseguir datos y recursos 
    POST -> Guardar datos o recursos hacer logica desde un form
    PUT -> actualizar datos o recursos 
    DELETE ->eliminar datos o recursos 


*/
//Rutas oficiles
Route::post('/api/register','UserController@register');
Route::post('/api/login','UserController@login');
Route::put('/api/update','UserController@update');
Route::post('/api/upload','UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}','UserController@getImage');
Route::get('/api/user/detail/{id}','UserController@detail');


//rutas de controlador categorias
Route::resource('/api/Category', 'CategoryController');

//rutas de controlador de post 
Route::resource('/api/Post', 'PostController');
Route::post('/api/Post/upload','PostController@upload');
Route::get('/api/Post/image/{filename}','PostController@getImage');
Route::get('/api/Post/Category/{id}','PostController@getPostByCategory');
Route::get('/api/Post/User/{id}','PostController@getPostByUser');

