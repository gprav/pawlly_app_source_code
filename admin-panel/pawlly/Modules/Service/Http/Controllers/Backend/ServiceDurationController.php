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
use Modules\Service\Models\ServiceDuration;
use Yajra\DataTables\DataTables;
use Modules\Service\Http\Requests\ServiceDurationRequest;
use Auth;

class ServiceDurationController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'service.duration';
        // module name
        $this->module_name = 'service-duration';

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

        $type = request()->type;
        session(['type' => $type]);
        return view('service::backend.serviceduration.index_datatable', compact('module_action'));
    }

    public function duration_type($type)
    {

        $module_action = 'List';
        session(['type' => $type]);
        return view('service::backend.serviceduration.index_datatable', compact('module_action'));


    }



    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
        $data = ServiceDuration::where('type', $request->type);
        if ($request->employee_id !== null) {
            $employee_id = $request->employee_id;

            $data = $data->where(function ($query) use ($employee_id, $user_ids) {
                $query->where('created_by', $employee_id)
                    ->orWhereIn('created_by', $user_ids);
            })
                ->where('status', 1)
                ->get(); // Get the collection

            $filteredCollection = $data->filter(function ($item) use ($employee_id) {
                if ($item->created_by != $employee_id && $this->isAdminDurationSameAsEmployee($item, $employee_id)) {
                    return false; // Exclude admin duration where same as employee
                }
                return true;
            });

            if ($filteredCollection->isEmpty()) {
                return [];
            }

            $data = [];
            foreach ($filteredCollection as $item) {
                $data[] = $item;
            }
        } else {
            $data = $data->whereIn('created_by', $user_ids)->where('status', 1)
                ->get();
        }

        return response()->json($data);
    }

    private function isAdminDurationSameAsEmployee($item, $employee_id)
    {
        // Logic to determine if admin duration is same as employee duration
        // Example: Comparing duration fields
        return $item->duration == ServiceDuration::where('created_by', $employee_id)
            ->where('duration', $item->duration)
            ->exists();
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = ServiceDuration::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_serviceduration_update');
                break;

            case 'delete':
                ServiceDuration::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_serviceduration_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function index_data(Request $request)
    {
        $user = Auth::user();
        $usertype = $user->user_type;
        $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
        $type = session('type');
        $query = ServiceDuration::query()->orderBy('updated_at', 'desc');
    
        if ($type == 'training') {
            $edit_permission = 'edit_traning_duration';
            $delete_permission = 'delete_traning_duration';
        } else {
            $edit_permission = 'edit_walking_duration';
            $delete_permission = 'delete_walking_duration';
        }
    
        // Filter the data based on the selected 'type'
        if ($type == 'walking' && $usertype === 'walker') {
            // When the type is 'walking' and user is a 'walker', show only the walker’s data
            $query->where('created_by', $user->id);
        } else {
            // If it's 'training' or for other users (admin, demo_admin), do not filter by 'walker'
            $query->where('type', $type);
        }
    
        $filter = $request->filter;
    
        // Apply the filter if set
        if (isset($filter)) {
            if (isset($filter['duration_rolewise'])) {
                if ($filter['duration_rolewise'] === "my_duration" && $usertype === 'walker') {
                    $query->where('created_by', $user->id); // Show only the walker's duration
                } elseif ($filter['duration_rolewise'] === "added_by_employee") {
                    $query->whereNotIn('created_by', $user_ids); // Show durations added by non-admins
                } elseif ($filter['duration_rolewise'] === "all") {
                    // Show all durations (default)
                } else {
                    $query->whereIn('created_by', $user_ids); // Show durations created by admins
                }
            }
        }
    
        return Datatables::of($query)
            ->addColumn('check', function ($row) use ($user) {
                if ($row->created_by === $user->id || $user->hasRole(['admin', 'demo_admin'])) {
                    return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
                }
            })
            ->addColumn('action', function ($data) use ($user, $edit_permission, $delete_permission) {
                return view('service::backend.serviceduration.action_column', compact('data', 'user', 'edit_permission', 'delete_permission'));
            })
            ->editColumn('status', function ($row) use ($user) {
                if ($row->created_by === $user->id || $user->hasRole(['admin', 'demo_admin'])) {
                    $checked = $row->status ? 'checked="checked"' : '';
                    return '
                            <div class="form-check form-switch ">
                                <input type="checkbox" data-url="' . route('backend.service-duration.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input" id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" ' . $checked . '>
                            </div>
                           ';
                } else {
                    return '-';
                }
            })
            ->editColumn('duration', function ($data) {
                return formatTime($data->duration);
            })
            ->editColumn('price', function ($data) {
                return \Currency::format($data->price);
            })
            ->addColumn('employee', function ($data) {
                return view('service::backend.user_id', compact('data'));
            })
            ->rawColumns(['action', 'status', 'check'])
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
    public function store(ServiceDurationRequest $request)
    {
        $hours = $request->hours;
        $minutes = $request->minutes;
        $duration = $hours . ':' . $minutes;
        $type = session('type');
        if ($request->is('api/*')) {
            $user = auth()->user();
            $usertype = $user->user_type;
            if ($usertype === "walker") {
                $type = "walking";
            }

        }

        $data = ServiceDuration::create(array_merge($request->all(), ['duration' => $duration, 'type' => $type]));

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
        $data = ServiceDuration::findOrFail($id);
        list($hours, $minutes) = explode(':', $data->duration);
        $data['hours'] = $hours;
        $data['minutes'] = $minutes;
        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ServiceDurationRequest $request, $id)
    {
        $hours = $request->hours;
        $minutes = $request->minutes;
        $duration = $hours . ':' . $minutes;
        $data = ServiceDuration::findOrFail($id);

        $data->update(array_merge($request->all(), ['duration' => $duration]));

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
        $data = ServiceDuration::findOrFail($id);

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

        $data = ServiceDuration::with('user')->onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('service::backend.serviceduration.trash', compact('data', 'module_name_singular', 'module_action'));
    }
    public function restore($id)
    {
        $data = ServiceDuration::withTrashed()->find($id);
        $data->restore();

        $message = Str::singular($this->module_title) . ' Data Restoreded Successfully';

        return redirect('app/serviceduration');
    }
    public function update_status(Request $request, Serviceduration $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function duration_price(Request $request)
    {


        $data = ServiceDuration::where('id', $request->duration_id)->first();

        return response()->json(['data' => $data, 'status' => true]);

    }

}
