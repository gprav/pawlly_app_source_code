<?php

namespace Modules\Service\Http\Controllers\Backend\API;

use Illuminate\Contracts\Support\Renderable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Service\Models\ServiceFacility;
use Modules\Service\Transformers\ServiceFacilityResource;

class ServiceFacilityController extends Controller
{
    public function facilityList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $servicefacility =  ServiceFacility::with('user');
        $employee_id = $request->input('employee_id'); 
        $added_by_admin = $request->input('added_by_admin');  
        $is_booking = $request->input('is_booking'); 
        $admin_ids = User::role(['admin', 'demo_admin'])->pluck('id')->toArray();
        $search = $request->input('search');
        
         $servicefacility = $servicefacility->where(function ($query) use ($employee_id, $added_by_admin, $admin_ids) {
            
            if ($employee_id) {
                $query->where(function ($subQuery) use ($employee_id) {
                    $subQuery->where('created_by', $employee_id);
                });
            }
            if(($added_by_admin == 1)){
                $query->where(function ($subQuery) use ($employee_id, $admin_ids) {
                    $subQuery->where('created_by', $employee_id)
                             ->orWhereIn('created_by', $admin_ids);
                });
            }
            
        });

        
        if($is_booking == 1){
            $servicefacility = ServiceFacility::with('user')
            ->join('users', 'service_facility.created_by', '=', 'users.id')
            ->whereIn('service_facility.created_by', array_merge([$employee_id], $admin_ids))
            ->where('service_facility.status', 1)
            ->orderByRaw("FIELD(users.user_type, 'boarder', 'admin')")
            ->select('service_facility.*');
        }
        if ($search) {

            $servicefacility = ServiceFacility::where(function($query) use ($employee_id, $admin_ids, $search) {
                $query->where('created_by', $employee_id)
                      ->orWhereIn('created_by', $admin_ids);
            })->where('name', 'like', "%{$request->search}%");

        }
        $servicefacility = $servicefacility->paginate($perPage);
        $items = ServiceFacilityResource::collection($servicefacility);
      

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.facility_list'),
        ], 200);
    }
}
