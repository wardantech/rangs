<?php

namespace App\Http\Controllers\Ticket;

use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Category;
use App\Models\Ticket\ServiceType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ServiceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $services = ServiceType::with('category')->orderBy('id', 'desc');
            $categories = Category::where('status', 1)->orderBy('id', 'desc')->get();

            if (request()->ajax()) {
                return DataTables::of($services)

                    ->addColumn('categoryName', function ($services) {
                        if ($services->category != null) {
                            $category = $services->category->name;
                        } else {
                            $category = null;
                        }
                        return $category;
                    })

                    ->addColumn('serviceWarrenty', function ($services) {
                        if ($services->is_service_warranty == 1) {
                            return '<a class="badge badge-primary">Yes</a>';
                        } else {
                            return '<a class="badge badge-warning">No</a>';
                        }
                    })

                    ->addColumn('status', function ($services) {
                        if ($services->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('service-types.status', $services->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('service-types.status', $services->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($services) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center">
                                            <a href="' . route('service-types.edit', $services->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $services->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('service-types.edit', $services->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $services->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['categoryName', 'serviceWarrenty', 'status', 'action'])
                    ->make(true);
            }

            return view('ticket.service_type.index', compact('services', 'categories'));
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
        $this->validate($request, [
            // 'service_type' => 'required|unique:service_types,service_type,NULL,id,deleted_at,NULL',
            'service_type' => [
                'required',
                Rule::unique('service_types')
                    ->where('category_id', $request->category_id)
            ],
            'status' => 'required',
            'category_id' => 'required|numeric',
            'service_amount' => 'required|numeric'
        ]);

        try {
            $data = $request->all();
            $ServiceType = ServiceType::where('service_type', $request->service_type)->where('category_id', $request->category_id)->get();
            if (count($ServiceType) > 0) {
                return back()->with('error', "Sorry! Can't Create. The Service Type Name Is Available For The Selected Category");
            } else {
                ServiceType::create($data);
                return back()->with('success', 'Service Type Created Successfully');
            }
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
            $service = ServiceType::findOrFail($id);
            $categories = Category::where('status', 1)->orderBy('name')->get();
            return view('ticket.service_type.edit', compact('service', 'categories'));
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
            // 'service_type' => 'required|unique:service_types,service_type,' . $id,
            'service_type' => [
                'required',
                Rule::unique('service_types')
                    ->where('category_id', $request->category_id)
                    ->ignore($id)
            ],
            'status' => 'required',
            'category_id' => 'required|numeric',
            'service_amount' => 'required|numeric'
        ]);

        try {
            $service = ServiceType::findOrFail($id);
            // $ServiceType = ServiceType::where('service_type', $request->service_type)->where('category_id', $request->category_id)->get();
            // if (count($ServiceType) > 0) {
            //     return back()->with('error', "Sorry! Can't Create. The Service Type Name Is Available For The Selected Category");
            // } else {
                $service->service_type = $request->service_type;
                $service->service_amount = $request->service_amount;
                $service->category_id = $request->category_id;
                if (isset($request->is_service_warranty)) {
                    $service->is_service_warranty = $request->is_service_warranty;
                } else {
                    $service->is_service_warranty = 0;
                }
                $service->status = $request->status;
                $service->update();

                return redirect()->route('service-types.index')
                    ->with('success', __('label.NEW_SERVICE_TYPE_UPDATED'));
            // }
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
            $serviceType = ServiceType::findOrFail($id);
            $Ticket = DB::table('tickets')
                ->where('deleted_at', NULL)
                ->where('service_type_id', 'LIKE', '%' . $serviceType->id . '%')
                ->get();
            if (count($Ticket) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Service Type is used in Ticket Management",
                ]);
            } else {
                $serviceType->delete();
                return response()->json([
                    'success' => true,
                    'message' => "This Service Type deleted successfully",
                ]);
            }
            // return back()->with('success', __('label.SERVICE_TYPE_DELETED'));
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
            $serviceType = ServiceType::findOrFail($id);

            if ($serviceType->status == false) {
                $serviceType->update([
                    'status' => true
                ]);

                return back()->with('success', __('Service type active now'));
            } elseif ($serviceType->status == true) {
                $serviceType->update([
                    'status' => false
                ]);

                return back()->with('success', __('Service type inactive now'));
            }

            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
