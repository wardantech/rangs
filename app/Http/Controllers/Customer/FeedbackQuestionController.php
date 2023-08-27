<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\CustomerFeedback;
use App\Models\Customer\FeedbackQuestion;

class FeedbackQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $questions = FeedbackQuestion::orderBY('id', 'desc');
            if (request()->ajax()) {
                return DataTables::of($questions)

                    ->addColumn('status', function ($questions) {

                        if ($questions->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('call-center.customer-feedback-question.status', $questions->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('call-center.customer-feedback-question.status', $questions->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($questions) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('call-center.customer-feedback-question.edit', $questions->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $questions->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('call-center.customer-feedback-question.edit', $questions->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $questions->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('customer.feedback_question.index', compact('questions'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.feedback_question.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
        ]);
        try {
            FeedbackQuestion::create($request->all());
            return redirect('call-center/customer-feedback-question')->with('success', __('New Question Created Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $question = FeedbackQuestion::find($id);
            return view('customer.feedback_question.edit', compact('question'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
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
        $this->validate($request, [
            'question' => 'required',
        ]);

        try {
            FeedbackQuestion::find($id)->update($request->all());
            return redirect('call-center/customer-feedback-question')->with('success', __('Question Updated Successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $feedbackQuestion = FeedbackQuestion::find($id);

            if ($feedbackQuestion) {
                $customerFeedback = CustomerFeedback::where('question_id', $feedbackQuestion->id)->get();
                if (count($customerFeedback) > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sorry! Can't Delete. This Feedback Question is Used Already",
                    ]);
                } else {
                    $feedbackQuestion->delete();
                    return response()->json([
                        'success' => true,
                        'message' => 'Question Deleted Successfully.',
                    ]);
                }
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }

    public function activeInactive($id)
    {
        try {
            $feedbackQuestion = FeedbackQuestion::findOrFail($id);

            if ($feedbackQuestion->status == false) {
                $feedbackQuestion->update([
                    'status' => true
                ]);

                return back()->with('success', __('Feedback question active now'));
            } elseif ($feedbackQuestion->status == true) {
                $feedbackQuestion->update([
                    'status' => false
                ]);

                return back()->with('success', __('Feedback question inactive now'));
            }

            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
