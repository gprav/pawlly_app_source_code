<?php

namespace Modules\Service\Http\Controllers\Backend\API;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Service\Models\ServiceTraining;
use Modules\Service\Transformers\ServiceTrainingResource;
use App\Models\User;
use Modules\Service\Transformers\ServiceTrainingDurationResource;
use Modules\Service\Models\ServiceTrainingDurationMapping;

class ServiceTrainingController extends Controller
{
    public function trainingList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $servicetraining = ServiceTraining::with('trainingDuration', 'user');

        $employee_id = $request->input('employee_id');
        $added_by_admin = $request->input('added_by_admin');
        $is_booking = $request->input('is_booking');
        $admin_ids = User::role(['admin', 'demo_admin'])->pluck('id')->toArray();
     
        $search = $request->input('search');

        // Apply the filters and search term
        $servicetraining = $servicetraining->where(function ($query) use ($employee_id, $added_by_admin, $admin_ids) {
            
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
            $servicetraining = 
            ServiceTraining::with('user')
            ->join('users', 'service_training.created_by', '=', 'users.id')
            ->whereIn('service_training.created_by', array_merge([$employee_id], $admin_ids))
            ->where('service_training.status', 1)
            ->orderByRaw("FIELD(users.user_type, 'boarder', 'admin')")
            ->select('service_training.*');
        }

        if ($search) {

            $servicetraining = ServiceTraining::where(function($query) use ($employee_id, $admin_ids, $search) {
                $query->where('created_by', $employee_id)
                      ->orWhereIn('created_by', $admin_ids);
            })->where('name', 'like', "%{$request->search}%");

        }
        
        
        $servicetraining = $servicetraining->paginate($perPage);
        $items = ServiceTrainingResource::collection($servicetraining);
      

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.training_list'),
        ], 200);
    }


    public function store(Request $request)
    {
        $data = ServiceTraining::create($request->all());

        $message = __('messages.create_form', ['form' => __('service.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function trainingDurationList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $durationlist =  ServiceTrainingDurationMapping::with('servicetraining');
        if($request->has('training_type_id')){
            $durationlist = $durationlist->where('type_id',$request->training_type_id);
        }

        $durationlist = $durationlist->paginate($perPage);
        $items = ServiceTrainingDurationResource::collection($durationlist);
      

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('service.facility_list'),
        ], 200);
    }

}
