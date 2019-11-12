<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function testORM (){
     /*   $posts= Post::all();
        foreach($posts as $post){
          echo" <h1>". $post->title ."</h1>";
          echo "<span>{$post->user->name} - {$post->category->name} </span>";
          echo "<br><span>".$post->user->email."</span>";
          echo" <p>". $post->content ."</p>";
          echo"<hr>";


        }*/
        $categories = Category::all();
        foreach($categories as $cat){
            echo"<h1>{$cat->name}</h1>";
            foreach($cat->posts as $post){
                echo" <h1>". $post->title ."</h1>";
                echo "<span>{$post->user->name} - {$post->category->name} </span>";
                //echo "<br><span>".$post->user->email."</span>";
                echo" <p>". $post->content ."</p>";
               
      
      
              }
              echo"<hr>";
        }

        die();
    }
}
