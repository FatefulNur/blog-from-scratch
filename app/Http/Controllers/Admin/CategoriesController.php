<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
        $this->categoryRepository = $categoryRepository;
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // validating data
        $request->validate([
            'name' => 'required|regex:/^([a-zA-z]){3,}(\s[\w]*)*$/|unique:categories,name',
        ], [
            'regex' => ':attribute do not allow any special character.'
        ], [
            'name' => 'Category Name'
        ]);

        $depth = 0;
        $parent = 0;

        // prepare depth values
        if ($request->has('parent_id')) {
            $parent = Category::find($request->input('parent_id'));

            if ($parent) {
                $depth = ($parent->depth + 1);
            }
        }

        // creating data
        Category::create([
            'name' => $request->input('name'),
            'parent_id' => $parent ? $parent->id : null,
            'depth' => $depth,
            'icon' => $request->input('icon')
        ]);

        return redirect('/admin/categories/create')->with('action', 'Category has been created');
    }

    public function delete(Category $category)
    {
        if ($category->delete()) {
            return redirect('/admin/categories/create')->with('action', 'Category has been deleted');
        }
    }

    public function edit(Category $category)
    {
        $categories = Category::all();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $this->validatedRequests($request, $category);

        // fetching some parent data
        $requestedParentId = $request->input('parent_id');
        $requestedParentCategory = Category::find($requestedParentId);
        $parentCategory = Category::find($category->parent_id);

        if($category->id == $request->parent_id) {
            return back()->withErrors([
                'category' => 'Category cannot be same as parent'
            ]);
        }

        // updating category without depth
        $category->update([
            'name' => $request->input('name'),
            'parent_id' => $requestedParentId,
            'icon' => $request->input('icon') ?: ""
        ]);

        // updating childrens when parent id set to null
        if (is_null($category->parent_id)) {
            $this->categoryRepository->updateChildrensIfParentIdSetToNull($category);
        } else {
            if ($category->depth < $requestedParentCategory->depth) {

                // updating childrens when parent id set children id
                $this->categoryRepository->updateChildrensIfParentIdSetToChildrenId($category, $parentCategory);

                // updating category that parent has been changed
                $thisParent = Category::find($category->parent_id);
                $category->depth = ($thisParent->depth + 1);
                $category->save();
            } else {

                // updating childrens when children id set parent id
                $thisParent = Category::find($category->parent_id);
                $category->parent_id = $thisParent->id;
                $category->depth = ($thisParent->depth + 1);
                $category->save();

                $this->categoryRepository->updateChildrensIfChildrenIdSetToParentId($category);
            }
        }

        return redirect('/admin/categories/create')->with('action', 'Category has been updated');
    }

    private function validatedRequests(Request $request, Category $category)
    {
        return $request->validate([
            "name" => "required|regex:/^([a-zA-z]){3,}(\s[\w]*)*$/|unique:categories,name,{$category->id}",
        ], [
            'name.regex' => ':attribute do not allow any special character.'
        ], [
            'name' => 'Category Name'
        ]);
    }
}
