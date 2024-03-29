<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    function index(){
        $post = new Post;
        $category = new Category;
        $option = [
            "limit" => 10,
            "search" => [
                "category_id" => "all",
                "state" => "all"
            ]
        ];
        if(isset(\Request::query()['category_id']))  $option['search']['category_id']=\Request::query()['category_id'];
        if(isset(\Request::query()['state']))  $option['search']['state']=\Request::query()['state'];
        return view("post")->with([
            "categories" => $category->getAllCategory(),
            "posts" => $post->getAllPost($option)
        ]);
    }
    function create(){
        $category = new Category;
        return view("add-post")->with(
            "categories" , $category->getAllcategory()
        );
    }
    function store(PostRequest $request){
        $target_dir = "uploads/image/".time()."-";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ){
            return redirect()->back()->withErrors(["errors"=>"Sorry, only JPG, JPEG, PNG & GIF files are allowed."]); 
        }
        //...
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $post = new Post();
            $post->title = $request->input("title");
            $post->category_id = $request->input("category_id");
            $post->image = $target_file;
            $post->snapshort = $request->input("snapshort");
            $post->content = $request->input("content");
            $post->state = $request->input("state");
            $post->createPost();
            return redirect()->route("post");
        }
    }
    function edit($id){
        $post = new Post;
        $category = new Category;
        $post->id = $id;
        return view("update-post")->with([
            'post'=> $post->getPost(), 
            "categories" => $category->getAllCategory(),]);
    }
    function update(PostRequest $request, $id){
        $target_dir = "uploads/image/".time()."-";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        
        if($target_file == $target_dir){
            //not update image file
            $post = new Post();
            $post->id = $id;
            $post = $post->getPost();
            $post->title = $request->input("title");
            $post->category_id = $request->input("category_id");
            $post->snapshort = $request->input("snapshort");
            $post->content = $request->input("content");
            $post->state = $request->input("state");
            $post->updatePost();
            return redirect()->route("post");
        }
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ){
            return redirect()->back()->withErrors(["errors"=>"Sorry, only JPG, JPEG, PNG & GIF files are allowed."]); 
        }
        //...
        if (move_uploaded_file($_FILES["image"]["tmp_name"], time()."-".$target_file)) {
            $post = new Post();
            $post->id = $id;
            $post->title = $request->input("title");
            $post->category_id = $request->input("category_id");
            $post->image = $target_file;
            $post->snapshort = $request->input("snapshort");
            $post->content = $request->input("content");
            $post->state = $request->input("state");
            $post->updatePost();
            return redirect()->route("post");
        }
    }
    function delete($id){
        $post = new Post();
        $post->id = $id;
        $post->deletePost();
        return redirect()->route("post"); 
    }
}
