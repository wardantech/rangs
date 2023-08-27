<?php

namespace App\Http\Controllers\Group;

use DB;
use Session;
use Redirect;
use Validator;
use App\Models\Group\Group;
use Illuminate\Http\Request;
use App\Models\Inventory\Region;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Category;
use App\Models\Employee\TeamLeader;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $groups = Group::with('region')->orderBy('id', 'desc');
            $categories = Category::where('status', 1)->orderBy('id', 'desc')->get();

            if (request()->ajax()) {
                return DataTables::of($groups)

                    ->addColumn('categoryName', function ($groups) use ($categories) {
                        $data = [];
                        $category_name = '';
                        $selected_categories = json_decode($groups->category_id);
                        //return $selected_categories;
                        foreach ($categories as $category) {
                            if (in_array($category->id, $selected_categories)) {
                                $data[] =  $category->name;
                            }
                        }

                        foreach ($data as $key => $result) {
                            $total = count($data);

                            if($total == 1){
                               $category_name .= $result ;
                            }else{
                                $category_name .= $result . '&nbsp, ';
                            }
                        };

                        return rtrim($category_name, ', ');
                    })

                    ->addColumn('regionName', function ($groups) {
                        $data = isset($groups->region) ? $groups->region->name : null;
                        return $data;
                    })

                    ->addColumn('status', function ($groups) {

                        if ($groups->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('general.group.status', $groups->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('general.group.status', $groups->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($groups) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center"  style="display: flex;">
                                            <a href="' . route('general.group.edit', $groups->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $groups->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('general.group.edit', $groups->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $groups->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action', 'categoryName', 'regionName'])
                    ->make(true);
            }

            return view('group.index', compact('groups', 'categories'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create()
    {
        try {
            $categories = Category::where('status', 1)->orderBy('name')->get();
            $regions = Region::where('status', 1)->orderBy('name')->get();

            return view('group.create', compact('categories', 'regions'));
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
            'category_id' => 'required|array',
            'region_id' => 'required',
            'name' => 'required|string|unique:groups,name,NULL,id,deleted_at,NULL',
        ]);

        DB::beginTransaction();

        try {
            Group::create([
                'category_id' =>  json_encode($request->category_id),
                'region_id' =>  $request->region_id,
                'name' =>  $request->name,
                'created_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('general.group.index')
                ->with('success', __('label.GROUP_CREATED'));
        } catch (\Exception $e) {
            DB::rollback();
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
            $regions = Region::where('status', 1)->orderBy('name')->get();
            $categories = Category::where('status', 1)->orderBy('name')->get();
            $group = Group::findOrFail($id);
            return view('group.edit', compact('group', 'categories', 'regions'));
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
            'category_id' => 'required|array',
            'region_id' => 'required',
            'name' => 'required|string|unique:groups,name,' . $id,
        ]);

        DB::beginTransaction();
        try {
            $group = Group::findOrFail($id);
            $group->update([
                'category_id' => json_encode($request->category_id),
                'region_id' =>  $request->region_id,
                'name' =>  $request->name,
                'updated_by' => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('general.group.index')
                ->with('success', __('label.GROUP_UPDATED'));
        } catch (\Exception $e) {
            DB::rollback();
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
            $group = Group::findOrFail($id);
            $teamLeader = TeamLeader::where('group_id', $group->id)->get();
            if (count($teamLeader) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Group is used in Team Leader Management",
                ]);
            } else {
                $group->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Group deleted successfully",
                ]);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' =>  $bug,
            ]);
        }
    }

    public function activeInactive($id)
    {
        try {
            $group = Group::findOrFail($id);

            if ($group->status == false) {
                $group->update([
                    'status' => true
                ]);

                return back()->with('success', __('Group active now'));
            } elseif ($group->status == true) {
                $group->update([
                    'status' => false
                ]);
                return back()->with('success', __('Group inactive now'));
            }
            return back()->with('error', __('Action decline'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
