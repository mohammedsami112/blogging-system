<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\baseController as Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class categoriesController extends Controller {

    // Get All Categories
    public function index() {
        return $this->sendSuccess('All Categories', Category::all());
    }

    // Create Category
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'parent' => 'exists:categories,id|nullable'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => $request->slug ? str_replace(' ', '-', $request->slug) : str_replace(' ', '-', $request->name),
            'parent' => $request->parent
        ]);

        return $this->sendSuccess('Category Created Successfully', $category);



    }

    // Update Category
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required',
            'parent' => 'exists:categories,id|nullable'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $category = Category::find($request->category_id);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => $request->slug ? str_replace(' ', '-', $request->slug) : str_replace(' ', '-', $request->name),
            'parent' => $request->parent
        ]);

        return $this->sendSuccess('Category Updated Successfully', $category);
    }

    // Delete Category
    public function delete($categoryId) {

        Category::find($categoryId)->delete();

        return $this->sendSuccess('Category Deleted Successfully');
    }

}
