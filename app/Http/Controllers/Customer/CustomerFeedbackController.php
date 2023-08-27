<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\Ticket\Ticket;
use App\Http\Controllers\Controller;
use App\Models\Customer\CustomerFeedback;
use App\Models\Customer\FeedbackQuestion;

class CustomerFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customer.customer_feedback.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $questions=FeedbackQuestion::where('status', 1)->get();
            return view('customer.customer_feedback.create', compact('questions'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'question_id'        => 'required',
        //     'question_feedback'  => 'required',

        // ]);
        // dd($request->all());
        try{
            $customerFeedback=new CustomerFeedback();
            foreach($request->question_id as $key => $queId){
                $question_feedback="question".$request->question_id[$key];
                $feedbackDetails['ticket_id'] = $request->ticket_id;
                $feedbackDetails['question_id'] = $request->question_id[$key];
                $feedbackDetails['question_feedback'] = $request->$question_feedback;
                $feedbackDetails['remark'] = $request->remark;

                CustomerFeedback::create($feedbackDetails);
            }

            $ticket = Ticket::find($request->ticket_id);
            $ticket->update([
                    'is_closed'=>1,
                    'status'=>12,
                ]);
            return redirect()->back()->with('success', __('Ticket CLosed Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
