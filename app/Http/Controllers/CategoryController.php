<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('services')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories',
        ], [
            'name.required' => 'Название категории обязательно для заполнения',
            'name.max' => 'Название категории не должно превышать 100 символов',
            'name.unique' => 'Категория с таким названием уже существует'
        ]);

        try {
            DB::beginTransaction();
            
            Category::create($request->only('name'));
            
            DB::commit();
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно создана');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Произошла ошибка при создании категории: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,'.$category->id,
        ], [
            'name.required' => 'Название категории обязательно для заполнения',
            'name.max' => 'Название категории не должно превышать 100 символов',
            'name.unique' => 'Категория с таким названием уже существует'
        ]);

        try {
            DB::beginTransaction();
            
            $category->update($request->only('name'));
            
            DB::commit();
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно обновлена');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Произошла ошибка при обновлении категории: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        if ($category->services()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Нельзя удалить категорию, так как к ней привязаны услуги. Сначала измените категорию для этих услуг.');
        }

        try {
            DB::beginTransaction();
            
            $category->delete();
            
            DB::commit();
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Категория успешно удалена');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Произошла ошибка при удалении категории: ' . $e->getMessage());
        }
    }
}