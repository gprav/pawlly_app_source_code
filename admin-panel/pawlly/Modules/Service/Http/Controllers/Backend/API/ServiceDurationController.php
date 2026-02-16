<?php

namespace Modules\Service\Http\Controllers\Backend\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Service\Models\ServiceDuration;
use Modules\Service\Transformers\ServiceDurationResource;
use App\Models\User;

class ServiceDurationController extends Controller
{
    public function durationList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $serviceduration =  ServiceDuration::with('user');
        $employee_id = $request->input('employee_id'); 
        $added_by_admin = $request->input('added_by_admin');  
        $admin_ids = User::role(['admin', 'demo_admin'])->pluck('id')->toArray();
        $is_booking = $request->input('is_booking'); 
        if ($request->has('type') && $request->type != '') {
            $serviceduration = $serviceduration->Where('type', $request->type);
        }
        if($added_by_admin == 1){
            $serviceduration = $serviceduration->whereHas('user', function ($q) {
                $q->whereIn('user_type', ['admin', 'demo_admin']);
            })
            ->orWhereNull('created_by');
        }
       
        if ($request->has('employee_id')) {
            $serviceduration = $serviceduration->where('created_by', $employee_id);
        }
        
        if($is_booking == 1){
                // $serviceduration = ServiceDuration::where('created_by', $employee_id)->where('status',1);
                
                // if ($serviceduration->count() == 0) {
                //     $serviceduration = ServiceDuration::where('type', $request->type)
                //         ->whereIn('created_by', $admin_ids) 
                //         ->where('status',1);
                // }
                $serviceduration = ServiceDuration::where('type',  $request->type)
                ->where(function($query) use ($employee_id, $admin_ids) {
                    $query->where('created_by', $employee_id)
                          ->orWhereIn('created_by', $admin_ids);
                })
                ->where('status', 1)
                ->orderBy('created_at', 'desc') // Example ordering by created_at in descending order
                ->paginate($perPage);
                $filteredCollection = $serviceduration->getCollection()->filter(function ($item) use ($employee_id) {
                    if ($item->created_by != $employee_id && $this->isAdminDurationSameAsEmployee($item, $employee_id)) {
                        return false; // Exclude admin duration where same as employee
                    }
                    return true;
                });
                
                if ($filteredCollection->isEmpty()) {
                    return response()->json([
                        'status' => true,
                        'data' => [],
                        'message' => __('service.duration_list'),
                    ], 200);
                }

                $serviceduration = [];
                foreach ($filteredCollection as $item) {
                    $serviceduration[] = $item;
                }
                return response()->json([
                    'status' => true,
                    'data' => $serviceduration,
                    'message' => __('service.duration_list'),
                ], 200);
                
        }
        $serviceduration = $serviceduration->paginate($perPage);
        $items = ServiceDurationResource::collection($serviceduration);
      

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.duration_list'),
        ], 200);
    }
    private function isAdminDurationSameAsEmployee($item, $employee_id)
    {
        // Logic to determine if admin duration is same as employee duration
        // Example: Comparing duration fields
        return $item->duration == ServiceDuration::where('created_by', $employee_id)
                            ->where('duration', $item->duration)
                            ->exists();
    }
}
