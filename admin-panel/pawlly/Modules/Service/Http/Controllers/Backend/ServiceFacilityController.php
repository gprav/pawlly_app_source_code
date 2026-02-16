<?php

namespace Modules\Service\Http\Controllers\Backend;

use App\Authorizable;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Service\Http\Requests\ServiceFacilityRequest;
use Modules\Service\Models\ServiceFacility;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Validation\Rule;

class ServiceFacilityController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'service.service_facility';
        // module name
        $this->module_name = 'service-facility';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
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

        return view('service::backend.servicefacility.index_datatable', compact('module_action'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $employee_id = $request->employee_id;
        $category_id = $request->category_id;
        $branch_id = $request->branch_id;
        $admin_ids = User::role(['admin', 'demo_admin'])->pluck('id')->toArray();
        $data = ServiceFacility::with('user');

        if (isset($branch_id)) {
            $data = $data->whereHas('branches', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            })->where('status',1);
        }
        if ($request->has('employee_id') && $request->employee_id) {
            $data = $data->join('users', 'service_facility.created_by', '=', 'users.id')
            ->whereIn('service_facility.created_by', array_merge([$employee_id], $admin_ids))
            ->where('service_facility.status', 1)
            ->orderByRaw("FIELD(users.user_type, 'boarder', 'admin')")
            ->select('service_facility.*');
        } else {
            $data = $data->whereIn('created_by', $admin_ids)->where('status',1);
        }
        $data = $data->get();

        return response()->json($data);
    }

 

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $services = ServiceFacility::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_service_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                ServiceFacility::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_service_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function update_status(Request $request, ServiceFacility $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(Request $request)
    {
        $user=Auth::User();
        $usertype = $user->user_type;
        $user_ids = User::role(['admin', 'demo_admin'])->pluck('id');
        $query = ServiceFacility::query()->orderBy('updated_at', 'desc');
        $filter = $request->filter;

        if (isset($filter)) {
          
          
            if (isset($filter['facility_rolewise'])) {
                if($filter['facility_rolewise'] === "my_facility" && $usertype == 'boarder'){
                    $query->where('created_by', $user->id);
                }elseif($filter['facility_rolewise'] === "added_by_employee"){
                    $query->whereNotIn('created_by', $user_ids);
                }elseif($filter['facility_rolewise'] === "all"){
                    $query;
                }else{
                    $query->whereIn('created_by',$user_ids);
                }
                
            }
        }

        return Datatables::of($query)
                        ->addColumn('check', function ($row) use ($user){
                            if($row->created_by === $user->id || $user->hasRole(['admin', 'demo_admin'])){
                            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
                            }
                        })
                        ->addColumn('action', function ($data) use ($user){
                            return view('service::backend.servicefacility.action_column', compact('data','user'));
                        })
                        ->editColumn('status', function ($row) use ($user){
                            if($row->created_by === $user->id || $user->hasRole(['admin', 'demo_admin'])){
                            $checked = '';
                            if ($row->status) {
                                $checked = 'checked="checked"';
                            }
            
                            return '
                            <div class="form-check form-switch ">
                                <input type="checkbox" data-url="'.route('backend.service-facility.update_status', $row->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$row->id.'"  name="status" value="'.$row->id.'" '.$checked.'>
                            </div>
                           ';}else{
                            return '-';
                           }
                        })
                        ->addColumn('employee', function ($data) {
                            return view('service::backend.user_id', compact('data'));
                            // return optional($data->user)->first_name.' '.optional($data->user)->last_name;
                        })
                        ->rawColumns(['action', 'status', 'check'])
                        ->orderColumns(['id'], '-:column $1')
                        ->make(true);
    }

    public function index_list_data(Request $request)
    {

        $term = trim($request->q);

        $query_data = User::role('employee')->where(function ($q) {
            if (! empty($term)) {
                $q->orWhere('name', 'LIKE', "%$term%");
            }
        })->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->first_name.$row->last_name,
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

        return view('service::backend.servicefacility.create', compact('module_action'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ServiceFacilityRequest $request)
    {
        $data = ServiceFacility::create($request->all());

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

        $data = ServiceFacility::findOrFail($id);

        return view('service::backend.servicefacility.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = ServiceFacility::findOrFail($id);

        if (! is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->toArray();
        }

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_facility', 'name')->ignore($id),
            ],
        ]);
        $data = ServiceFacility::findOrFail($id);

        $request_data = $request->except('feature_image');

        $data->update($request_data);

        if ($request->custom_fields_data) {

            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        storeMediaFile($data, $request->file('feature_image'), 'feature_image');

        $message = __('messages.update_form', ['form' =>__($this->module_title)]);

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
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = ServiceFacility::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('service.singular_title')]);

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

        $data = ServiceFacility::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('service::backend.servicefacility.trash', compact("$data", 'module_name_singular', 'module_action'));
    }

    /**
     * Restore a soft deleted entry.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function restore($id)
    {
        $data = ServiceFacility::withTrashed()->find($id);
        $data->restore();

        $message = __('messages.service_data');

        return response()->json(['message' => $message, 'status' => true]);
    }

}
