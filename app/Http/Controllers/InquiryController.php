<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\Product;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only("index", "create", "store");
        $this->middleware('auth.admin')->only("destroy", "edit", "update");
    }

    public function ask(Request $request, $productId)
    {
        $inquiry = new Inquiry();
        $inquiry->question = $request->question;
        $inquiry->product_id = $productId;
        $inquiry->save();

        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Inquiry::orderBy('id')->get();
        $inquiry = new Inquiry();
        $headers = $inquiry->getTableColumns();
        $constraints = $inquiry->getConstraints();
        return view('inquiryAdmin', compact('items', 'headers', 'constraints'));
    }

    public function filter(Request $request)
    {
        $query = Inquiry::where('question', 'like', "%$request->question%");
        if($request->product_id!=null)
        {
            $query->where('product_id', $request->product_id);
        }
        $items = $query->orderBy('id')->get();
        $inquiry = new Inquiry();
        $headers = $inquiry->getTableColumns();
        $constraints = $inquiry->getConstraints();
        return view('inquiryAdmin', compact('items', 'headers', 'constraints'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inq = new Inquiry();
        $headers = $inq->getTableColumns();
        $constraints = ['product_id' => Product::all()];
        return view('inquiryAdminCreate', compact('headers', 'constraints'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => ['required', 'max:200'],
            'product_id' => ['required']
        ]);

        $inquiry = new Inquiry();
        $inquiry->question = $request->question;
        $inquiry->product_id = $request->product_id;
        $inquiry->save();

        return redirect("/admin/inquiry");
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
        $prev = Inquiry::findOrFail($id);
        $headers = $prev->getTableColumns();
        $constraints = ['product_id' => Product::all()];
        return view('inquiryAdminEdit', compact('headers', 'constraints', 'prev'));
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
            'question' => ['required', 'max:200'],
            'product_id' => ['required']
        ]);
        $query =  Inquiry::where('id', $id);
        $query->update([
            'question' => $request->question,
            'product_id' => $request->product_id,
        ]);

        return redirect("/admin/inquiry");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Inquiry::findOrFail($id)->delete();
        return redirect("/inquiry/product");
    }
}
