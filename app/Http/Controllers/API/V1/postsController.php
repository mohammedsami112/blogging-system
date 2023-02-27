<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\baseController as Controller;
use App\Http\Traits\createPostSummary;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class postsController extends Controller {
    use createPostSummary;
    public function index() {
        $posts = Post::with(['writer' => function($query) {$query->select('name', 'id');}, 'category' => function($query) {$query->select('name', 'id');}])->get();

        return $this->sendSuccess('All Posts', $posts);
    }

    // Create Post
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'category' => 'required|exists:categories,id',
            'writer' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        // Upload Thumbnail
        $thumbnailNewName = time() . '.' . $request->thumbnail->extension();

        $request->thumbnail->move(public_path('thumbnails'), $thumbnailNewName);

        // Create Post

        $post = Post::create([
            'title' => $request->title,
            'summary' => $this->createSummary($request->content, 200),
            'content' => $request->content,
            'thumbnail' => $thumbnailNewName,
            'slug' => $request->slug ? str_replace(' ', '-', $request->slug) : str_replace(' ', '-', $request->title),
            'category' => $request->category,
            'writer' => $request->writer
        ]);

        return $this->sendSuccess('Post Created Successfully', $post);


    }


    // Update Post
    public function updatePost(Request $request) {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'category' => 'required|exists:categories,id',
            'writer' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $post = Post::find($request->post_id);

        // Handel Thumbnail
        
        if ($request->thumbnail) {
            $thumbnailNewName = time() . '.' . $request->thumbnail->extension();
            $request->thumbnail->move(public_path('thumbnails'), $thumbnailNewName);
            unlink(public_path('thumbnails') . '\\' . $post->thumbnail);
        }

        $post->update([
            'title' => $request->title,
            'summary' => $this->createSummary($request->content, 200),
            'content' => $request->content,
            'thumbnail' => $request->thumbnail ? $thumbnailNewName : $post->thumbnail,
            'slug' => $request->slug ? str_replace(' ', '-', $request->slug) : str_replace(' ', '-', $request->title),
            'category' => $request->category,
            'writer' => $request->writer
        ]);

        return $this->sendSuccess('Post Updated Successfully', $request->all());

    }


    // Get Post
    public function getPost($postId) {
        $post = Post::where('id', $postId)->with(['writer' => function($query) {$query->select('name', 'id');}, 'category' => function($query) {$query->select('name', 'id');}, 'comments' => function($query) {$query->select('name', 'post_id', 'content');}])->firstOrFail();

        return $this->sendSuccess('Get Post', $post);
    }


    // Create Comment
    public function createComment(Request $request) {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'name' => "required",
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $comment = Comment::create([
            'name' => $request->name,
            'content' => $request->content,
            'post_id' => $request->post_id
        ]);

        return $this->sendSuccess('Comment Created Successfully', $comment);

    }

    // Get Comments
    public function getComments($postId) {
        $comments = Comment::where('post_id', $postId)->get();

        return $this->sendSuccess('Post Comments', $comments);
    }

}
