<?php

namespace Modules\Holiday\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Holiday\Models\Holiday;

class HolidaysController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Holidays';

        // module name
        $this->module_name = 'holidays';

        // directory path of the module
        $this->module_path = 'holiday::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {

        $branch_id = $request->branch_id;

        $data = Holiday::where('branch_id', $branch_id)->get();

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
{
    $holidays = collect($request->holidays);
    $branch_id = $request->branch_id;

    // Add or update holidays from the submitted list
    foreach ($holidays as $holiday) {
        // If the holiday already exists, update it, otherwise create a new one
        Holiday::updateOrCreate(
            [
                'branch_id' => $branch_id,
                'date' => $holiday['date'],
            ],
            [
                'title' => $holiday['title'],
                'branch_id' => $branch_id,
                'date' => $holiday['date'],
            ]
        );
    }

    $message = __('messages.holiday_update');
    return response()->json(['message' => $message, 'status' => true], 200);
}

}
