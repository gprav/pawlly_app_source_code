<?php

namespace Modules\Service\Http\Controllers\Backend;

use App\Authorizable;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Service\Http\Requests\ServiceRequest;
use Modules\Service\Models\Service;
use Modules\Service\Models\ServiceBranches;
use Modules\Service\Models\ServiceEmployee;
use Modules\Service\Models\ServiceGallery;
use Modules\Service\Models\ServiceTraining;
use Yajra\DataTables\DataTables;
use Modules\Service\Models\ServiceTrainingDurationMapping;
use Auth;
use Illuminate\Validation\Rule;
use Modules\Service\Models\ServiceDuration;
use Modules\Service\Http\Requests\ServiceTrainingRequest;

class ServiceTrainingController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'service.training_service';
        // module name
        $this->module_name = 'service-training';

        // module icon
        // $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            // 'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        return view('service::backend.servicetraining.index_datatable', compact('module_action'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $employee_id = $request->employee_id;
        $admin_ids = User::role(['admin', 'demo_admin'])->pluck('id')->toArray();
        $data = ServiceTraining::with('user');
        if ($request->has('employee_id') && $request->employee_id) {
            $data =
                $data->join('users', 'service_training.created_by', '=', 'users.id')
                    ->whereIn('service_training.created_by', array_merge([$employee_id], $admin_ids))
                    ->where('service_training.status', 1)
                    ->orderByRaw("FIELD(users.user_type, 'boarder', 'admin')")
                    ->select('service_training.*');
        } else {
            $data = $data->whereIn('created_by', $admin_ids)->where('status', 1);
        }
        $data = $data->get();
        return response()->json($data);
    }

    public function duration_list(Request $request)
    {
        $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');

        // Prepare the initial query
        $query_data = ServiceTraining::with('trainingDuration');

        if ($request->type_id !== null) {
            $typeId = $request->type_id;
            $query_data = $query_data->whereHas('trainingDuration', function ($qry) use ($typeId) {
                $qry->where('type_id', $typeId)->where('status', 1);
            });
        } else {
            $query_data = $query_data->whereIn('created_by', $user_ids)->where('status', 1);
        }

        $query_data = $query_data->get();

        $data = [];

        // Process ServiceTraining results
        foreach ($query_data as $item) {
            if ($item instanceof ServiceTraining) {
                foreach ($item->trainingDuration as $trainingDuration) {
                    $data[] = [
                        'id' => $trainingDuration->id,
                        'duration' => $trainingDuration->duration,
                        'price' => $trainingDuration->amount,
                    ];
                }
            }
        }

        return response()->json($data);
    }




    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $services = ServiceTraining::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_servicetraining_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                ServiceTraining::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_servicetraining_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function update_status(Request $request, ServiceTraining $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(Request $request)
    {
        $user = Auth::User();
        $usertype = $user->user_type;
        $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
        $query = ServiceTraining::query()->orderBy('updated_at', 'desc');
        $filter = $request->filter;

        if (isset($filter)) {


            if (isset($filter['training_type_rolewise'])) {
                if ($filter['training_type_rolewise'] === "my_type" && $usertype === 'trainer') {
                    $query->where('created_by', $user->id);
                } elseif ($filter['training_type_rolewise'] === "added_by_employee") {

                    $query->whereNotIn('created_by', $user_ids);
                } elseif ($filter['training_type_rolewise'] === "all") {
                    $query;
                } else {
                    $query->whereIn('created_by', $user_ids);
                }

            }
        }
        return Datatables::of($query)
            ->addColumn('check', function ($row) use ($user) {
                if ($row->created_by === $user->id || $user->hasRole(['admin', 'demo_admin'])) {
                    return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
                }
            })
            ->addColumn('action', function ($data) use ($user) {
                return view('service::backend.servicetraining.action_column', compact('data', 'user'));
            })
            ->editColumn('status', function ($row) use ($user) {
                if ($row->created_by === $user->id || $user->hasRole(['admin', 'demo_admin'])) {
                    $checked = '';
                    if ($row->status) {
                        $checked = 'checked="checked"';
                    }

                    return '
                            <div class="form-check form-switch ">
                                <input type="checkbox" data-url="' . route('backend.service-training.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                            </div>
                           ';
                } else {
                    return '-';
                }
            })
            ->addColumn('employee', function ($data) {
                return view('service::backend.user_id', compact('data'));
                // return optional($data->user)->first_name.' '.optional($data->user)->last_name;
            })
            ->orderColumn('status', function ($query, $order) {
                $query->orderBy('status', $order);
            })
            ->editColumn('duration_list', function ($data) {

                $viewButton = "<b><button type='button' data-assign-module='{$data->id}' data-assign-target='#duration-list' data-assign-event='duration_list' class='btn btn-secondary btn-sm rounded text-nowrap px-1' data-bs-toggle='tooltip' title='View Duration'> <i class='fas fa-eye'></i></button></b>";

                return $viewButton;
            })
            ->rawColumns(['action', 'status', 'check', 'duration_list'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    public function index_list_data(Request $request)
    {

        $term = trim($request->q);

        $query_data = User::role('employee')->where(function ($q) {
            if (!empty($term)) {
                $q->orWhere('name', 'LIKE', "%$term%");
            }
        })->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->first_name . $row->last_name,
                'avatar' => $row->profile_image,
            ];
        }

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = 'Create';

        return view('service::backend.services.create', compact('module_action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ServiceTrainingRequest $request)
    {
        $slug = Str::slug($request->input('name', 'default-name'));

        $data = $request->except('durations');
        $data['slug'] = $slug;
        $serviceTraining = ServiceTraining::create($data);

        if ($request->has('durations') && $request->durations !== '[{"hours":"","minutes":"","amount":""}]') {
            $durations = json_decode($request->durations, true); // Decode as an associative array

            foreach ($durations as $duration) {
                $duration_data = [
                    'type_id' => $serviceTraining->id,
                    // 'duration' => sprintf('%02d:%02d', $duration['hours'], $duration['minutes']),
                    'duration' => $duration['duration'],
                    'amount' => $duration['amount'],
                    'status' => $duration['status'],
                ];
                ServiceTrainingDurationMapping::create($duration_data);
            }
        }
        $message = __('messages.create_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = Service::findOrFail($id);

        return view('service::backend.services.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = ServiceTraining::with('trainingDuration')->findOrFail($id);
        // $data['trainingDuration'] = optional($data->trainingDuration)->map(function ($duration) {
        //     // Split the duration into hours and minutes
        //     $time = explode(':', $duration->duration);
        //     $hours = isset($time[0]) ? $time[0] : '00';
        //     $minutes = isset($time[1]) ? $time[1] : '00';

        //     return [
        //         'hours' => $hours,
        //         'minutes' => $minutes,
        //         'amount' => $duration->amount,
        //         'status' => $duration->status,
        //     ];
        // })->toArray();

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ServiceTrainingRequest $request, $id)
    {

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_training', 'name')->ignore($id),
            ],
        ]);

        $data = ServiceTraining::findOrFail($id);

        $data->update($request->all());

        ServiceTrainingDurationMapping::where('type_id', $id)->forceDelete();
        if ($request->has('durations') && $request->durations !== '[{"hours":"","minutes":"","amount":""}]') {
            $durations = json_decode($request->durations, true); // Decode as an associative array

            foreach ($durations as $duration) {
                $duration_data = [
                    'type_id' => $id,
                    // 'duration' => sprintf('%02d:%02d', $duration['hours'], $duration['minutes']),
                    'duration' => $duration['duration'],
                    'amount' => $duration['amount'],
                    'status' => $duration['status'],
                ];
                ServiceTrainingDurationMapping::create($duration_data);
            }
        }
        $message = __('messages.update_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = ServiceTraining::findOrFail($id);
        ServiceTrainingDurationMapping::where('type_id', $id)->delete();
        $data->delete();

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = ServiceTraining::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('service::backend.servicetraining.trash', compact('data', 'module_name_singular', 'module_action'));
    }
    public function restore($id)
    {
        $data = ServiceTraining::withTrashed()->find($id);
        $data->restore();

        $message = Str::singular($this->module_title) . ' Data Restoreded Successfully';

        return redirect('app/servicetraining');
    }
    public function type_duration_list($id)
    {

        $trainingDuration = ServiceTrainingDurationMapping::where('type_id', $id)->with('servicetraining')->get();
        $serviceTraining = ServiceTraining::where('id', $id)->first();

        $data = [
            'duration' => $trainingDuration,
            'type' => $serviceTraining->name,
        ];

        return response()->json(['data' => $data, 'status' => true,]);

    }

}
