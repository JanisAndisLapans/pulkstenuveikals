<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prophecy\Doubler\DoubleInterface;
use App\Models\Product;
use App\Models\Order;
use Mail;
use Log;
use SebastianBergmann\CodeCoverage\Report\PHP;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only("index", "create", "store", "update", "destroy", "edit");

    }

    public function makeOrder(Request $request, $itemcounts)
    {
        $request->validate([
            'phone' => ['numeric', 'digits:8', 'required'],
            'email' => [ 'email', 'required'],
            'city' => ['required'],
            'street' => ['required'],
            'zip' => "required|regex:/^LV-[0-9]+$/"
        ], [
            'phone.numeric' => 'Telefons drīkst saturēt tikai ciparus',
            'phone.digits' => 'Telefona garums ir 8 cipari. Atļauti tikai Latvijas telefoni',
            'phone.required' => "Telefona numurs ir obligāts",
            'email.email' => 'Nederīgs e-pasta formāts',
            'email.required' => 'E-pasts ir nepieciešams',
            'city.required' => 'Pilsēta ir nepieciešama',
            'street.required' => 'Iela ir nepieciešama',
            'zip.required' => 'ZIP kods ir nepieciešams',
            'zip.regex' => 'ZIP koda formāts : LV-{numurs}, piem., LV-1040'
        ]);

        $order = new Order();
        $order->client_phone = $request->phone;
        $order->client_email = $request->email;
        $order->order_address = "$request->city, $request->street/$request->apartament";
        $order->zip_code = $request->zip;
        $order->save();
        $body = "Pirkums #$order->id veikts veiksmīgi uz adresi: $order->order_address".PHP_EOL.
            "Nesaprašanu gadījumā mēs ar Jums sazināsimies: $order->client_phone".PHP_EOL.
            "Pasūtījuma sastāvā: ".PHP_EOL;
        $totalPrice = 0;

        $itemcounts = json_decode($itemcounts, true);

        $items = Product::whereIn('id', array_keys($itemcounts))->get();

        foreach ($items as $item)
        {
            $price = $item->price*$itemcounts[$item->id];
            $body = $body."$item->count $item->name kopā: $price €".PHP_EOL;
            $totalPrice += $price;
            $order->products()->attach($item->id, ['count' => $itemcounts[$item->id]]);
        }
        $body = $body."Kopējā veiktā apmaksa: $totalPrice €";

        Mail::raw($body, function($message) use ($request) {
            $message->from('pulkstenu@veikals.lv', 'pulkstenuveikals');
            $message->to($request->email, 'Cienījamais klients')->subject
            ('Veiksmīgi veikts pirkums Pulksteņu Veikalā');
        });

        return redirect("cart/success");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $items = Order::orderBy('id')->get();
        $order = new Order();
        $headers = $order->getTableColumns();
        $many = ['product' => []];

        foreach($items as $item)
        {
            $many['product'][$item->id] = $item->products()->get();
            foreach($many['product'][$item->id] as $other)
            {
                $other->{'prop'} = $other->pivot->count;
            }
        }

        return view('orderAdmin', compact('items', 'headers', 'many'));
    }

    public function indexMany(Request $request)
    {
        $idsWithProp = explode(',', str_replace(' ', '', $request->ids));

        $ids = [];
        $props = [];

        foreach ($idsWithProp as $iwp)
        {
            $propStart = strrpos($iwp,'(');
            $ind = intval(substr($iwp, -strlen($iwp) - $propStart-1));
            $prop = intval(substr($iwp, $propStart+1));
            array_push($ids, $ind);
            array_push($props, $prop);
        }
        $items = Order::whereIn('id', $ids)->orderBy('id')->get();
        for($i = 0; $i<$items->count(); $i++)
        {
            $items[$i]->{'prop'} = $props[$i];
        }

        $order = new Order();
        $headers = $order->getTableColumns();
        $headers['prop'] = 'integer';
        $many = ['product' => []];
        foreach($items as $item)
        {
            $many['product'][$item->id] = $item->products()->take(count($ids))->get();
            foreach($many['product'][$item->id] as $other)
            {
                $other->{'prop'} = $other->pivot->count;
            }
        }
        return view('orderAdmin', compact('items', 'headers', 'many'));
    }

    public function filter(Request $request)
    {
        $query = Order::where('client_email', 'like', "%$request->client_email%");
        $query = $query->where('client_phone', 'like', "%$request->client_phone%");
        $query = $query->where('order_address', 'like', "%$request->order_address%");
        $query = $query->where('zip_code', 'like', "%$request->zip_code%");
        $items = $query->orderBy('id')->get();

        $order = new Order();
        $headers = $order->getTableColumns();
        $many = ['product' => []];
        foreach($items as $item)
        {
            $many['product'][$item->id] = $item->products()->get();
            foreach($many['product'][$item->id] as $other)
            {
                $other->{'prop'} = $other->pivot->count;
            }
        }
        return view('orderAdmin', compact('items', 'headers', 'many'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $order = new Order();
        $headers = $order->getTableColumns();
        $many = ['product' => Product::all(),
            'productProp'=>['name' => 'count', 'type' => 'number']];
        return view('orderAdminCreate', compact('headers' , 'many'));
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
            'client_email' => ['required','email'],
            'client_phone' => ['required', 'numeric', 'digits:8'],
            'order_address' => [ 'required'],
            'zip_code' => ['required', 'regex:/^LV-[0-9]+$/']
        ]);

        $order = new Order();
        $order->client_email = $request->client_email;
        $order->client_phone = $request->client_phone;
        $order->order_address = $request->order_address;
        $order->zip_code = $request->zip_code;
        $order->save();

        $products = explode(', ',$request->product);
        foreach ($products as $product)
        {
            $propStart = strrpos($product,'{');
            $ind = intval(substr($product, -strlen($product) - $propStart-1));
            $prop = intval(substr($product, $propStart+1));
            $order->products()->attach($ind, ['count' => $prop]);
        }
        return redirect("/admin/order");
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
        $prev = Order::findOrFail($id);
        $headers = $prev->getTableColumns();
        $many = ['product' => Product::all(),
            'productProp'=>['name' => 'count', 'type' => 'number']];
        $manyProducts = $prev->products()->get();
        $toStr = "";
        $count = count($manyProducts);
        $i = 0;
        foreach ($manyProducts as $other) {
            $numberOf = $other->pivot->count;
            if(++$i == $count)
            {
                $toStr = $toStr."$other->id{"."$numberOf".'}';
            }
            else $toStr = $toStr."$other->id{"."$numberOf".'}, ';
        }
        $prev->{"product"} = $toStr;
        return view('orderAdminEdit', compact('headers' , 'many', 'prev'));
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
            'client_email' => ['required','email'],
            'client_phone' => ['required', 'numeric', 'digits:8'],
            'order_address' => [ 'required'],
            'zip_code' => ['required', 'regex:/^LV-[0-9]+$/']
        ]);
        $query =  Order::where('id', $id);
        $query->update([
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'order_address' => $request->order_address,
            'zip_code' => $request->zip_code
        ]);

        $products = explode(', ',$request->product);
        $order = $query->first();
        $order->products()->detach();

        foreach ($products as $product)
        {
            $propStart = strrpos($product,'{');
            $ind = intval(substr($product, -strlen($product) - $propStart-1));
            $prop = intval(substr($product, $propStart+1));
            $order->products()->attach($ind, ['count' => $prop]);
        }
        return redirect("/admin/order");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return redirect("/admin/order");
    }
}
