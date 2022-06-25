<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Answer;
use App\Models\Review;
use App\Models\Order;
use Cookie;
use Illuminate\Validation\Rule;
use Log;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller
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
        $items = Product::orderBy('id')->get();
        $product = new Product();
        $headers = $product->getTableColumns();
        $many = ['order' => [], 'category' => []];

        foreach($items as $item)
        {
            $many['order'][$item->id] = $item->orders()->get();
            foreach ($many['order'][$item->id] as $other) {
                $other->{'prop'} = $other->pivot->count;
            }
            $many['category'][$item->id] = $item->categories()->get();
        }

        return view('productAdmin', compact('items', 'headers', 'many'));
    }

    public function filter(Request $request)
    {
        $query = Product::where('name', 'like', "%$request->name%");
        $query = $query->where('desc', 'like', "%$request->desc%");
        $query = $query->where('active', $request->has('active'));

        if($request->price!=null)
        {
            $query->where('price', $request->price);
        }
        $items = $query->orderBy('id')->orderBy('id')->get();
        $product = new Product();
        $headers = $product->getTableColumns();
        $many = ['order' => [], 'category' => []];

        foreach($items as $item)
        {
            $many['order'][$item->id] = $item->orders()->get();
            foreach ($many['order'][$item->id] as $other) {
                $other->{'prop'} = $other->pivot->count;
            }
            $many['category'][$item->id] = $item->categories()->get();
        }

        return view('productAdmin', compact('items', 'headers', 'many'));
    }

    public function indexMany(Request $request)
    {
        $product = new Product();
        $headers = $product->getTableColumns();

        if(str_contains($request->ids, '(')) {
            $headers['prop'] = 'integer';
            $idsWithProp = explode(',', str_replace(' ', '', $request->ids));
            $ids = [];
            $props = [];
            foreach ($idsWithProp as $iwp) {
                $propStart = strrpos($iwp, '(');
                $ind = intval(substr($iwp, -strlen($iwp) - $propStart - 1));
                $prop = intval(substr($iwp, $propStart + 1));
                array_push($ids, $ind);
                array_push($props, $prop);
            }
            $items = Product::whereIn('id', $ids)->orderBy('id')->get();
            for($i = 0; $i<$items->count(); $i++)
            {
                $items[$i]->{'prop'} = $props[$i];
            }
        }
        else{
            $ids = explode(',', $request->ids);
            $items = Product::whereIn('id', $ids)->orderBy('id')->get();
        }

        $many = ['order' => [], 'category' => []];

        foreach($items as $item)
        {
            $many['order'][$item->id] = $item->orders()->take(count($ids))->get();
            foreach ($many['order'][$item->id] as $other) {
                $other->{'prop'} = $other->pivot->count;
            }
            $many['category'][$item->id] = $item->categories()->take(count($ids))->get();
        }
        return view('productAdmin', compact('items', 'headers', 'many'));
    }

    public function indexListing()
    {
        $items = Product::orderByDesc('created_at')->get();
        $categoriesRaw = Category::all();

        $categories = [];
        foreach($categoriesRaw as $cate)
        {
            if($cate->category_id!=null)
            {
                $parent = Category::firstWhere("id", "=", $cate->category_id);
                if(!array_key_exists($parent->name, $categories))
                {
                    $categories[$parent->name] = [];
                }
                array_push($categories[$parent->name], $cate->name);
            }
        }

        return view('listing', ['items' => $items, 'categories' => $categories]);
    }

    public function indexListingFilter(Request $request)
    {
        $search_query = Product::where('name', 'LIKE', '%'.$request->name.'%');

        $lowPrice = $request->lowPrice;
        $highPrice = $request->highPrice;

        $search_query = $search_query->where('price', '>=' , $lowPrice)
            ->where('price', '<=', $highPrice);

        $parentCates = Category::whereNull('category_id')->get();
        foreach($parentCates as $pcate){

            $spcate = str_replace(' ', '', $pcate->name);
            if(!$request->{$spcate.'All'}) {
                $childCates = Category::where('category_id', $pcate->id)->get();
                $search_query = $search_query->whereHas("categories" , function ($q) use ($childCates, $request) {
                    $cateNeeded = [];
                    foreach($childCates as $ccate)
                    {
                        $sccate = str_replace(' ', '', $ccate->name);
                        if($request->{$sccate})
                        {
                            array_push($cateNeeded, $ccate->name);
                        }
                    }
                    $q->whereIn('name', $cateNeeded);
                });
            }
        }
        $items = $search_query->get();
        if($request->sort == 'new'){
            $items = $items->sortBy('created_at');
        }
        if($request->sort == 'cheap')
        {
            $items = $items->sortBy('price');
        }
        else if($request->sort = 'expensive')
        {
            $items = $items->sortByDesc('price');
        }

        $categoriesRaw = Category::all();

        $categories = [];
        foreach($categoriesRaw as $cate)
        {
            if($cate->category_id!=null)
            {
                $parent = Category::firstWhere("id", "=", $cate->category_id);
                if(!array_key_exists($parent->name, $categories))
                {
                    $categories[$parent->name] = [];
                }
                array_push($categories[$parent->name], $cate->name);
            }
        }

        return view('listing', ['items' => $items, 'categories' => $categories]);
    }
        /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();
        $headers = $product->getTableColumns();
        $many = ['order' => Order::all(), 'category' => Category::whereNotNull('category_id')->get(),
            'orderProp'=>['name' => 'count', 'type' => 'number']];
        return view('productAdminCreate', compact('headers' , 'many'));
    }

    public function test(){
        $review = new Review();
        $text = "";
        foreach($review->getTableColumns() as $col => $type)
        {
            $text = $text."$col => $type".PHP_EOL;
        }
        return response($text);
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
            'image' => ['required'],
            'name' => ['required', 'min:5' , 'max:50', 'unique:products'],
            'price' => [ 'required', 'numeric'],
            'desc' => ['max:500', 'required'],
            'active' => ['boolean'],
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->desc = $request->desc;
        $product->active = $request->has('active');
        $product->slug = $request->slug;
        $file = $request->file('image') ;
        $fileName = str_replace(['-',' ',':'],[],Carbon::now()->toDateTimeString().'.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        $product->image = $fileName;
        $destinationPath = public_path();
        $file->move($destinationPath,$fileName);
        $orders = explode(', ',$request->order);
        $product->save();
        foreach ($orders as $order)
        {
            $propStart = strrpos($order,'{');
            $ind = intval(substr($order, -strlen($order) - $propStart-1));
            $prop = intval(substr($order, $propStart+1));
            $product->orders()->attach($ind, ['count' => $prop]);
        }
        $categories = explode(', ', $request->category);
        foreach($categories as $cate)
        {
            $product->categories()->attach(intval($cate));
        }
        return redirect("/admin/product");
    }

    public function show($slug)
    {
        $item = Product::firstWhere('slug' , '=', $slug);
        $categoriesAssociated = Category::whereHas('products', function($q) use ($item) {
            $q->where('product_id', "=", $item->id);
        }
        )->get();


        $categories = [];

        foreach($categoriesAssociated as $cate)
        {
            $parent = Category::firstWhere("id", "=", $cate->category_id);
            if(!isset($categories[$parent->name])) $categories[$parent->name] = [];

            array_push($categories[$parent->name], $cate->name);
        }

        $inquiries = Inquiry::where('product_id', '=', $item->id)->get();
        $answersRaw = Answer::all();
        $answers = [];
        foreach($answersRaw as $ans)
        {
            if(!isset($answers[$ans->inq_id])) $answers[$ans->inq_id] = [];
            array_push($answers[$ans->inq_id], $ans);
        }
        $reviews = Review::where('product_id', '=', $item->id)->get();
        return view('product', compact('item', 'categories', 'answers', 'inquiries', 'reviews'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prev = Product::findOrFail($id);
        $headers = $prev->getTableColumns();
        $many = ['order' => Order::all(), 'category' => Category::whereNotNull('category_id')->get(),
            'orderProp'=>['name' => 'count', 'type' => 'number']];
        $manyOrders = $prev->orders()->get();
        $toStr = "";
        $count = count($manyOrders);
        $i = 0;
        foreach ($manyOrders as $other) {
            $numberOf = $other->pivot->count;
            if(++$i == $count)
            {
                $toStr = $toStr."$other->id{"."$numberOf".'}';
            }
            else $toStr = $toStr."$other->id{"."$numberOf".'}, ';
        }
        $prev->{"order"} = $toStr;
        $manyCates = $prev->categories()->get();
        $prev->{"category"} = $manyCates->pluck('id')->implode(", ");
        return view('productAdminEdit', compact('headers' , 'many', 'prev'));
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
        if(isset($request['name'])){$request['slug'] = strtolower(str_replace([' '], [], $request->name));}
        $request->validate([
            'name' => ['required', 'min:5' , 'max:50', "unique:products,name,$id"],
            'price' => [ 'required', 'numeric'],
            'desc' => ['max:500', 'required']
        ]);
        $query = Product::where('id', $id);
        $query->update([
            'name' => $request->name,
            'price' => $request->price,
            'desc' => $request->desc,
            'active' => $request->has('active'),
            'slug' => $request->slug
        ]);
        if(!$request->has('imageKeep'))
        {
            $file = $request->file('image') ;
            $fileName = str_replace(['-',' ',':'],[],Carbon::now()->toDateTimeString().'.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
            $query->update([
                'image' => $fileName
            ]);
            $destinationPath = public_path();
            $file->move($destinationPath,$fileName);
        }
        $orders = explode(', ',$request->order);
        $product = $query->first();

        $product->orders()->detach();
        $product->categories()->detach();

        foreach ($orders as $order)
        {
            $propStart = strrpos($order,'{');
            $ind = intval(substr($order, -strlen($order) - $propStart-1));
            $prop = intval(substr($order, $propStart+1));
            $product->orders()->attach($ind, ['count' => $prop]);
        }
        $categories = explode(', ', $request->category);
        foreach($categories as $cate)
        {
            $product->categories()->attach(intval($cate));
        }
        return redirect("/admin/product");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return redirect("/admin/product");
    }
}
