<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth.admin')->only("index", "create", "destroy", "store",  "edit", "update");
    }

    public function index()
    {
        $items = User::orderBy('id')->get();
        $user = new User();
        $headers = $user->getTableColumns();
        return view('userAdmin', compact('items', 'headers',));
    }

    public function filter(Request $request)
    {
        $query = User::where('name', 'like', "%$request->name%");
        $query = $query->where('email', 'like', "%$request->email%");
        if($request->password)
        {
            $query = $query->where('password', $request->password);
        }
        $query = $query->where('privileged', $request->has('privileged'));

        $items = $query->get();
        $user = new User();
        $headers = $user->getTableColumns();
        return view('userAdmin', compact('items', 'headers',));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        $headers = $user->getTableColumns();
        return view('userAdminCreate', compact('headers'));
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
            'name' => ['required', 'unique:users'],
            'email' => ['required', 'unique:users', 'email'],
            'password' =>  ['required', 'min:8']
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->privileged = $request->privileged;
        $user->save();

        return redirect("/admin/user");
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
        $prev = User::findOrFail($id);
        $prev->password = "";
        $headers = $prev->getTableColumns();
        return view('userAdminEdit', compact('headers', 'prev'));
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
            'name' => ['required', "unique:users,name,$id"],
            'email' => ['required', "unique:users,email,$id", 'email'],
            'password' =>  ['required', 'min:8']
        ]);
        $query = User::where('id', $id);
        $query->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'privileged' => $request->has('privileged')
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
        User::findOrFail($id)->delete();
        return redirect("/admin/user");
    }
}
