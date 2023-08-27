<?php

namespace App\Http\Controllers\Inventory;

use DB;
use Session;
use App\Models\Group\Group;
use Illuminate\Http\Request;
use App\Models\Inventory\Thana;
use App\Models\Inventory\Region;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\District;
use App\Models\Inventory\Division;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegionController extends Controller
{
    public function index()
    {
        try{
            $regions=DB::table('regions')
                        ->where('deleted_at', NULL)
                        ->join('divisions', 'divisions.id', '=', 'regions.division_id')
                        ->select('regions.*', 'divisions.name as division_name')
                        ->orderBy('id','desc');
            $divisions = Division::orderBy('name')->get();
            $districts = District::orderBy('name')->get();
            $thanas = Thana::orderBy('name')->get();

            if (request()->ajax()) {
                return DataTables::of($regions)

                    ->addColumn('districtName', function ($regions) use ($districts) {
                        $data = [];
                        $district_name = '';
                        $districtId = json_decode($regions->district_id);
                                        foreach($districts as $district){
                                            if (in_array($district->id, $districtId)){
                                                $data[] = $district->name;
                                            }
                                        }

                        foreach ($data as $key => $result) {
                            $total = count($data);
                            if ($total == 1) {
                                $district_name .= $result;
                            } else {
                                $district_name .= $result . '&nbsp, ';
                            }
                        };

                        return rtrim($district_name, ', ');
                    })

                    ->addColumn('thanaName', function ($regions) use ($thanas) {
                        $data = [];
                        $thana_name = '';
                        $thanaId = json_decode($regions->thana_id);
                        foreach ($thanas as $thana) {
                            if ($thanaId != null) {
                                if (in_array($thana->id, $thanaId)) {
                                    $data[] = $thana->name;
                                }
                            }
                        }

                        foreach ($data as $key => $result) {
                            $total = count($data);
                            if ($total == 1) {
                                $thana_name .= $result;
                            } else {
                                $thana_name .= $result . '&nbsp, ';
                            }
                        };

                        return rtrim($thana_name, ', ');
                    })

                    ->addColumn('status', function ($regions) {

                        if ($regions->status == true) {
                            $status = '<div class="text-center">
                                            <a href="' . route('general.region.status', $regions->id) . '" title="Status" class="btn btn-sm btn-success">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                        </div>';
                        } else {
                            $status = '<div class="text-center">
                                        <a href="' . route('general.region.status', $regions->id) . '" title="Status" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    </div>';
                        }
                        return $status;
                    })

                    ->addColumn('action', function ($regions) {
                        if (Auth::user()->can('edit') && Auth::user()->can('delete')) {
                            return '<div class="table-actions text-center" style="display: flex;">
                                            <a href="' . route('general.region.edit', $regions->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            <a type="submit" onclick="showDeleteConfirm(' . $regions->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('edit')) {
                            return '<div class="table-actions">
                                            <a href="' . route('general.region.edit', $regions->id) . '" title="Edit"><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                            </div>';
                        } elseif (Auth::user()->can('delete')) {
                            return '<div class="table-actions">
                                            <a type="submit" onclick="showDeleteConfirm(' . $regions->id . ')" title="Delete"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                                            </div>';
                        }
                    })
                    ->addIndexColumn()
                    ->rawColumns(['status', 'action', 'districtName', 'thanaName'])
                    ->make(true);
            }

            return view('inventory.region.index', [
                'regions'   => $regions,
                'divisions' => $divisions,
                'districts' => $districts,
                'thanas'    => $thanas
            ]);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function create(Request $request)
    {
        try{
            $divisions = Division::orderBy('name')->get();
            $districts = District::select('id', 'name')->get();
            return view('inventory.region.create', compact('divisions', 'districts'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:regions,name,NULL,id,deleted_at,NULL',
            'code' => 'required|numeric|unique:regions,code,NULL,id,deleted_at,NULL',
            'division_id' => 'required|numeric',
            'district_id' => 'required|array',
            'thana_id' => 'required|array',
        ]);

//        Session::forget('districtIds');

        try {
            $region=$request->all();
            $region['district_id']=json_encode($request->district_id);
            $region['thana_id']=json_encode($request->thana_id);
            Region::create($region);
            return redirect()->route('general.region.index')
                    ->with('success', __('New Region created successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function edit($id)
    {
        try{
            Session::forget('tahanaIds');

            $region = Region::findOrFail($id);
            $divisions = Division::orderBy('name')->get();
            $districts = District::orderBy('name')->get();
            $districtId = json_decode($region->district_id);
            $thanaId = json_decode($region->thana_id);
            $thanas = Thana::whereIn('district_id', $districtId)->get();
            if (!empty($thanaId)) {
                foreach($thanaId as $id) {
                    Session::push('tahanaIds', $id);
                }
            }
            
            return view('inventory.region.edit', compact('region', 'divisions', 'districts', 'thanas', 'thanaId'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:regions,name,' . $id,
            'code' => 'required|unique:regions,code,' . $id,
            'division_id' => 'required|numeric',
            'district_id' => 'required|array',
            'thana_id' => 'required|array',
        ]);

        try {
            $updatedRegion=$request->all();
            $updatedRegion['district_id']=json_encode($request->district_id);
            $region['thana_id']=json_encode($request->thana_id);
            $region=Region::findOrFail($id);
            $region->update($updatedRegion);

            Session::forget('tahanaIds');
            return redirect()->route('general.region.index')
                ->with('success', __('Region updated successfully.'));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $region=Region::findOrFail($id);
            $group=Group::where('region_id', $region->id)->get();
            if(count($group) > 0){
                return response()->json([
                    'success' => false,
                    'message' => "Sorry! Can't Delete. This Region is used in Group Management",
                ]);
            }else{
                $region->delete();
                return response()->json([
                    'success' => true,
                    'message' => "Region deleted successfully",
                ]);
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return response()->json([
                'success' => false,
                'message' => $bug,
            ]);
        }
    }

    public function getDistrict($id)
    {
        $districts=DB::table('districts')->where('districts.division_id', $id)->orderBy('name')->get();

        return response()->json($districts);
    }

    public function activeInactive($id)
    {
        try {
            $region = Region::findOrFail($id);

            if($region->status == false) {
                $region->update([
                    'status' => true
                ]);

                return back()->with('success', __('Region active now'));
            }elseif ($region->status == true) {
                $region->update([
                    'status' => false
                ]);

                return back()->with('success', __('Region inactive now'));
            }

            return back()->with('error', __('Action decline'));
        }catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
