<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth')->only("index", "create", "store", "update", "destroy", "edit");
    }

    public function index()
    {
        $items = Category::orderBy('id')->get();
        $cate = new Category();
        $headers = $cate->getTableColumns();
        $constraints = $cate->getConstraints();
        $many = ['product' => []];

        foreach($items as $item)
        {
            $many['product'][$item->id] = $item->products()->get();
        }

        return view('categoryAdmin', compact('items', 'headers', 'many', 'constraints'));
    }

    public function filter(Request $request)
    {
        $query = Category::where("name_en", 'like', "%$request->name_en%");
        $query = $query->where("name_lv", 'like', "%$request->name_lv%");
        if($request->category_id!=null){
            if($request->category_id!=0){
                $query->where('category_id', $request->category_id);
            }
            else{
                $query->whereNull('category_id');
            }
        }
        $items = $query->orderBy('id')->get();
        $cate = new Category();
        $headers = $cate->getTableColumns();
        $constraints = $cate->getConstraints();
        $many = ['product' => []];

        foreach($items as $item)
        {
            $many['product'][$item->id] = $item->products()->get();
        }

        return view('categoryAdmin', compact('items', 'headers', 'many', 'constraints'));
    }

    public function indexMany(Request $request)
    {
        $ids = explode(',', $request->ids);
        $items = Category::whereIn('id', $ids)->orderBy('id')->get();
        $cate = new Category();
        $headers = $cate->getTableColumns();
        $constraints = $cate->getConstraints();
        $many = ['product' => []];

        foreach($items as $item)
        {
            $many['product'][$item->id] = $item->products()->get();
        }

        return view('categoryAdmin', compact('items', 'headers', 'many', 'constraints'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Category();
        $headers = $product->getTableColumns();
        $constraints = ['category_id' => Category::whereNull('category_id')->get()];
        $many = ['products' => Product::all()];
        return view('categoryAdminCreate', compact('headers' , 'many', 'constraints'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(isset($request['name'])){$request['slug'] = strtolower(str_replace([' '], [], $request->name));}
        $request->validate([
            'name_en' => ['required', 'unique:categories'],
            'name_lv' => ['required', 'unique:categories'],
        ]);

        $category = new Category();
        $category->name_en = $request->name_en;
        $category->name_lv = $request->name_lv;
        if($request->category_id!="blank") {
            $category->category_id = $request->category_id;
        }
        $category->save();

        $products = explode(', ',$request->products);
        foreach($products as $product)
        {
            $category->products()->attach(intval($product));
        }
        return redirect("/admin/category");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prev = Category::findOrFail($id);
        $constraints = ['category_id' => Category::whereNull('category_id')->get()];
        $headers = $prev->getTableColumns();
        $many = ['product' => Product::all()];
        $manyProducts = $prev->products()->get();
        $toStr = "";
        $count = count($manyProducts);
        $i = 0;
        foreach ($manyProducts as $other) {
            if(++$i == $count)
            {
                $toStr = $toStr."$other->id";
            }
            else $toStr = $toStr."$other->id, ";
        }
        $prev->{"product"} = $toStr;
        return view('categoryAdminEdit', compact('headers' , 'many', 'prev','constraints'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => ['required', "unique:categories,name_en,$id"],
            'name_lv' => ['required', "unique:categories,name_lv,$id"]

        ]);
        $query =  Category::where('id', $id);
        $query->update([
            'name_en' => $request->name_en,
            'name_lv' => $request->name_lv
        ]);
        if($request->category_id!="blank") {
            $query->update([
                'category_id' => $request->category_id
            ]);
        }
        $products = explode(', ',$request->product);
        $category = $query->first();
        $category->products()->detach();

        foreach($products as $product)
        {
            $category->products()->attach(intval($product));
        }
        return redirect("/admin/category");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect("/admin/category");
    }
}
