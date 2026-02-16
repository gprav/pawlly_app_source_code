<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Product\Models\Review;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->module_title = 'Vendors Management';
        $this->module_name = 'vendors';
        $this->module_path = 'backend.vendors';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'icon-Product',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new User());
        $customefield = CustomField::exportCustomFields(new User());

        $filter = [
            'status' => $request->status,
        ];

        return view('backend.vendors.index', compact('module_action', 'columns', 'customefield', 'filter'));
    }

    /**
     * Get vendors data for DataTables.
     */
    public function index_data(Request $request)
    {
        $query = User::role('pet_store')
            ->with('media')
            ->select('users.*');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['status'])) {
                $query->where('status', $filter['status']);
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status', function ($data) {
                return view('backend.vendors.datatable.status', compact('data'));
            })
            ->editColumn('profile_image', function ($data) {
                return view('backend.vendors.datatable.profile_image', compact('data'));
            })
            ->editColumn('created_at', function ($data) {
                return date('Y-m-d', strtotime($data->created_at));
            })
            ->addColumn('action', function ($data) {
                return view('backend.vendors.datatable.action', compact('data'));
            })
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row-checked-values" id="datatable-row-'.$row->id.'" name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.',this)">';
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$keyword%"]);
            })
            ->rawColumns(['action', 'status', 'check'])
            ->make(true);
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $module_action = 'Create';

        return view('backend.vendors.create', compact('module_action'));
    }

    /**
     * Show vendor details.
     */
    public function show($id)
    {
        $module_action = 'Show';
        $vendor = User::role('pet_store')->findOrFail($id);

        // Get vendor statistics
        $stats = [
            'total_products' => $vendor->products()->count(),
            'total_orders' => $vendor->vendorOrders()->count(),
            'total_revenue' => $vendor->vendorOrders()
                ->whereHas('orders', function($q) {
                    $q->where('delivery_status', 'delivered');
                })
                ->sum('total_amount'),
            'average_rating' => Review::where('user_id', $id)->avg('rating') ?? 0,
            'total_reviews' => Review::where('user_id', $id)->count(),
        ];

        return view('backend.vendors.show', compact('module_action', 'vendor', 'stats'));
    }

    /**
     * Show the form for editing a vendor.
     */
    public function edit($id)
    {
        $module_action = 'Edit';
        $vendor = User::role('pet_store')->findOrFail($id);

        return view('backend.vendors.edit', compact('module_action', 'vendor'));
    }

    /**
     * Update vendor status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $vendor = User::role('pet_store')->findOrFail($id);
        $vendor->status = $request->status;
        $vendor->save();

        return response()->json([
            'status' => true,
            'message' => 'Vendor status updated successfully',
        ]);
    }

    /**
     * Update vendor store status.
     */
    public function updateStoreStatus(Request $request, $id)
    {
        $request->validate([
            'enable_store' => 'required|in:0,1',
        ]);

        $vendor = User::role('pet_store')->findOrFail($id);
        $vendor->enable_store = $request->enable_store;
        $vendor->save();

        return response()->json([
            'status' => true,
            'message' => 'Vendor store status updated successfully',
        ]);
    }

    /**
     * Delete a vendor.
     */
    public function destroy($id)
    {
        $vendor = User::role('pet_store')->findOrFail($id);
        $vendor->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vendor deleted successfully',
        ]);
    }

    /**
     * Bulk delete vendors.
     */
    public function bulk_destroy(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No vendors selected',
            ]);
        }

        User::role('pet_store')->whereIn('id', $ids)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vendors deleted successfully',
        ]);
    }
}
