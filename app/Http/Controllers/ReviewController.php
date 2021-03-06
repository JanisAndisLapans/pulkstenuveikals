<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReviewVerificationCode;
use Mail;
use Cookie;

class ReviewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only("index", "create", "store", "update", "destroy", "edit");
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function startVerification(Request $request, $productId)
    {
        $request->validate([
            'email' => ['required'],
            'id' => ['required']
        ]);

        $order = Order::where('id', '=', $request->id)->whereHas('products', function ($q) use($productId) {
            $q->where('product_id', $productId);
        })->first();

        if($order == null)
        {
            return back()->with('verifyStart','failId')->withInput();
        }
        else if($order->client_email!=$request->email)
        {
            return back()->with('verifyStart','failEmail')->with('email', $request->email)->withInput();
        }
        else if(Review::firstWhere('order_id', $order->id))
        {
            return back()->with('verifyStart','failRepeat')->withInput();
        }
        do {
            $code = $this->generateRandomString();
        }while(ReviewVerificationCode::firstWhere('code', '=', $code)!=null);
        $verificationCodeInst = new ReviewVerificationCode();
        $verificationCodeInst->product_id = $productId;
        $verificationCodeInst->code = $code;
        $verificationCodeInst->order_id = $order->id;
        $verificationCodeInst->save();
        $lang = Cookie::get('lang');
        if($lang=='lv') {
            Mail::raw("Spiediet: www.pulkstenuveikals.lv/product/review/$code", function ($message) use ($request) {
                $message->to($request->email, 'Cien??jamais klients')->subject
                ('Pulkste??u Veikala atsauces public????anas apstiprin??jums');
            });
        }else{
            Mail::raw("Click: www.pulkstenuveikals.lv/product/review/$code", function ($message) use ($request) {
                $message->to($request->email, 'Valued Customer')->subject
                ('Watch Store review verification');
            });
        }

        return back()->with('verifyStart','ok')->with('email', $request->email)->withInput();
    }

    public function reviewPage($code)
    {
        $lang = Cookie::get('lang');
        $rewVer = ReviewVerificationCode::firstWhere('code', $code);
        $item = Product::firstWhere('id', $rewVer->product_id);
        $item->{'name'} = $item->{"name_$lang"};
        $orderId = $rewVer->order_id;
        return view('review', compact('item', 'orderId', 'code'));
    }

    public function review(Request $request, $productId, $orderId, $code)
    {
        $lang = Cookie::get('lang');
        $ver_erros_lang = ['lv' => "Neder??gs apstiprin??jums vai jau nov??rt??ts", 'en' => "Invalid verification or already rated"];
        $rewVer = ReviewVerificationCode::firstWhere('code', $code);
        if($rewVer==null){
            return back()->withErrors('Error', $ver_erros_lang[$lang]);
        }
        $lang_errors = [
            'lv' => [
                'review.required_unless' => 'L??dzu, pievienojiet aprakstu vai nov??rt??jiet maksim??li!',
                'rating.required' => 'L??dzu, nov??rt??jiet produktu ar 0-5 zvaigzn??m!'
            ],
            'en' => [
                'review.required_unless' => 'Please, add a descrition or rate with 5 stars',
                'rating.required' => 'Please, rate the product with 0-5 stars'
            ],
        ];
        $request->validate([
            'rating' => ['required'],
            'review' => ['required_unless:rating,5'],
        ],
            $lang_errors[$lang]
        );

        $review = new Review();
        $review->content = $request->review;
        $review->stars = $request->rating;
        $review->product_id = $productId;
        $review->order_id = $orderId;
        $review->save();

        $item = Product::firstWhere('id', $productId);
        ReviewVerificationCode::findOrFail($rewVer->id)->delete();
        return redirect('/product/'.$item->slug);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Review::orderBy('id')->get();
        $review = new Review();
        $headers = $review->getTableColumns();
        $constraints = $review->getConstraints();
        return view('reviewAdmin', compact('items', 'headers', 'constraints'));
    }

    public function filter(Request $request)
    {
        $content = $request->{"content"};
        $query = Review::where('content', 'like', "%$content%");
        if($request->stars!=null)
        {
            $query->where('stars', $request->stars);
        }
        if($request->product_id!=null)
        {
            $query->where('product_id', $request->product_id);
        }
        if($request->order_id!=null)
        {
            $query->where('order_id', $request->order_id);
        }
        $items = $query->orderBy('id')->get();
        $review = new Review();
        $headers = $review->getTableColumns();
        $constraints = $review->getConstraints();
        return view('reviewAdmin', compact('items', 'headers', 'constraints'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $review = new Review();
        $headers = $review->getTableColumns();
        $constraints = ['order_id' => Order::all(), 'product_id' => Product::all()];
        return view('reviewAdminCreate', compact('headers', 'constraints'));
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
            'order_id' => ['required'],
            'product_id' => ['required'],
            'content' =>  ['required_unless:stars,5'],
            'stars' => [ 'required', 'min:0', 'max:5'],
        ]);

        $review = new review();
        $review->content = $request->{'content'};
        $review->stars = $request->stars;
        $review->order_id = $request->order_id;
        $review->product_id = $request->product_id;
        $review->save();

        return redirect("/admin/review");
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
        $prev = Review::findOrFail($id);
        $headers = $prev->getTableColumns();
        $constraints = ['product_id' => Product::all(), 'order_id' => Order::all()];
        return view('reviewAdminEdit', compact('headers', 'constraints', 'prev'));
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
            'order_id' => ['required'],
            'product_id' => ['required'],
            'content' =>  ['required_unless:stars,5'],
            'stars' => [ 'required', 'min:0', 'max:5'],
        ]);
        $query =  Review::where('id', $id);
        $query->update([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'content' => $request->{"content"},
            'stars' => $request->stars
        ]);

        return redirect("/admin/review");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        return redirect("/admin/review");
    }
}
