<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Answer;

class AnswerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only("index", "create", "store", "update", "destroy", "edit");

    }

    public function answer(Request $request, $inqId)
    {
        $request->validate(['ans' => 'required']);
        $answer = new Answer();
        $answer->ans = $request->ans;
        $answer->inq_id = $inqId;
        $answer->save();

        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Answer::orderBy('id')->get();
        $answer = new Answer();
        $headers = $answer->getTableColumns();
        $constraints = $answer->getConstraints();
        return view('answerAdmin', compact('items', 'headers', 'constraints'));
    }

    public function filter(Request $request)
    {
        $query = Answer::where('ans', 'like', "%$request->ans%");
        if($request->inq_id!=null)
        {
            $query->where('inq_id', $request->inq_id);
        }
        $items = $query->orderBy('id')->get();

        $answer = new Answer();
        $headers = $answer->getTableColumns();
        $constraints = $answer->getConstraints();
        return view('answerAdmin', compact('items', 'headers', 'constraints'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $answer = new Answer();
        $headers = $answer->getTableColumns();
        $constraints = ['inq_id' => Inquiry::all()];
        return view('answerAdminCreate', compact('headers', 'constraints'));
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
            'inq_id' => ['required'],
            'ans' => ['required', 'max:200']
        ]);

        $answer = new Answer();
        $answer->inq_id = $request->inq_id;
        $answer->ans = $request->ans;
        $answer->save();

        return redirect("/admin/answer");
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
        $prev = Answer::findOrFail($id);
        $headers = $prev->getTableColumns();
        $constraints = ['inq_id' => Inquiry::all()];
        return view('answerAdminEdit', compact('headers', 'constraints', 'prev'));
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
            'inq_id' => ['required'],
            'ans' => ['required', 'max:200']
        ]);
        $query =  Answer::where('id', $id);
        $query->update([
            'inq_id' => $request->inq_id,
            'ans' => $request->ans,
        ]);

        return redirect("/admin/answer");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Answer::findOrFail($id)->delete();
        return redirect("/admin/answer");
    }
}
