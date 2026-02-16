<?php

namespace App\Http\Controllers;

use App\Models\User;
use Currency;
use DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Earning\Models\EmployeeEarning;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Modules\Product\Models\Order;
use Modules\Product\Models\OrderGroup;
use Modules\Product\Models\OrderItem;
use App\Models\Setting;
use Auth;

class ReportsController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'Reports';

        // module name
        $this->module_name = 'reports';

        // module icon
        $this->module_icon = 'fa-solid fa-chart-line';

        view()->share([
            'module_icon' => $this->module_icon,
        ]);
    }

    public function daily_booking_report(Request $request)
    {
        $module_title = __('report.title_daily_report');

        return view('backend.reports.daily-booking-report', compact('module_title'));
    }

    public function daily_booking_report_index_data(Datatables $datatable, Request $request)
    {
        $data = $request->all();
        // $startDate = Carbon::today();
        // $endDate = Carbon::today();
        //   if (isset($data['filter']['booking_date'])) {

        //       try {

        //           $startDate =  Carbon::createFromFormat('Y-m-d', explode(' to ', $data['filter']['booking_date'])[0])->format('d/m/Y');
        //           $endDate =  Carbon::createFromFormat('Y-m-d', explode(' to ', $data['filter']['booking_date'])[1])->format('d/m/Y');

        //       } catch (\Exception $e) {
        //           \Log::error($e->getMessage());
        //       }
        //   }

        $query = Booking::select(
            DB::raw('DATE(bookings.start_date_time) AS date'),
            DB::raw('COUNT(DISTINCT bookings.id) AS total_booking'),
            DB::raw('COUNT( bookings.system_service_id) AS total_service'),
            DB::raw('COALESCE(SUM( bookings.service_amount), 0) as total_service_amount'),
            DB::raw('COALESCE(SUM( bookings.total_amount), 0) as total_amount'),
            DB::raw('SUM(CASE
            WHEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.type\')) = \'percent\' THEN bookings.service_amount * JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.percent\')) / 100
            WHEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.type\')) = \'fixed\' THEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.tax_amount\'))
            ELSE 0
        END) AS total_tax_amount'),
            // DB::raw('COALESCE(0) +
            // SUM(CASE
            //     WHEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.type\')) = \'percent\' THEN bookings.service_amount * JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.percent\')) / 100
            //     WHEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.type\')) = \'fixed\' THEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, \'$.tax_amount\'))
            //     ELSE 0
            // END) + COALESCE(SUM(DISTINCT bookings.service_amount), 0) AS total_amount')
        )
            ->leftJoin(DB::raw('(SELECT
                booking_id,
                CONCAT(
                    \'{ "type": "\', jt.type, \'", "percent": \', jt.percent, \', "tax_amount": \', jt.tax_amount, \' }\'
                ) AS tax_info
            FROM (
                SELECT
                    booking_id,
                    JSON_UNQUOTE(JSON_EXTRACT(tax_percentage, CONCAT(\'$[\', idx, \'].type\'))) AS type,
                    JSON_UNQUOTE(JSON_EXTRACT(tax_percentage, CONCAT(\'$[\', idx, \'].percent\'))) AS percent,
                    JSON_UNQUOTE(JSON_EXTRACT(tax_percentage, CONCAT(\'$[\', idx, \'].tax_amount\'))) AS tax_amount
                FROM booking_transactions
                CROSS JOIN (
                    SELECT 0 AS idx UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3
                ) AS indices
                WHERE idx < JSON_LENGTH(tax_percentage)
            ) AS jt
            GROUP BY booking_id, jt.type, jt.percent, jt.tax_amount) AS tx'), 'bookings.id', '=', 'tx.booking_id')
            ->where('bookings.status', 'completed')
            // ->whereDate('bookings.start_date_time',">=",$startDate)
            // ->whereDate('bookings.start_date_time',"<=",$endDate)
            ->groupBy('date');

        // dd($query->toSql());

        // $startDate = Carbon::today();
        // $endDate = Carbon::today();

        if (isset($data['filter']['booking_date'])) {
            try {
                $bookingDate = $data['filter']['booking_date'];
                $dates = explode(' to ', $bookingDate);

                if (count($dates) == 2) {
                    $startDate = date('Y-m-d', strtotime($dates[0]));
                    $endDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereDate('bookings.start_date_time', '>=', $startDate)
                        ->whereDate('bookings.start_date_time', '<=', $endDate);
                } else {
                    
                    $date = date('Y-m-d', strtotime($bookingDate));
                    $query->whereDate('bookings.start_date_time', '=', $date);
                }
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        if ($request->has('order') && isset($request->order[0])) {
            $columnIndex = $request->order[0]['column'];
            if ($columnIndex == 0) {
                $query->orderByDesc(DB::raw('MAX(bookings.start_date_time)'));
            }
        }
        return $datatable->eloquent($query)
            ->editColumn('start_date_time', function ($data) {
                return date('d-m-Y', strtotime($data->date));
            })
            ->editColumn('total_booking', function ($data) {
                return $data->total_booking;
            })
            ->editColumn('total_service', function ($data) {
                return $data->total_service ?? 0;
            })
            ->editColumn('total_service_amount', function ($data) {
                return Currency::format($data->total_service_amount ?? 0);
            })
            ->editColumn('total_tax_amount', function ($data) {
                return Currency::format($data->total_amount - $data->total_service_amount ?? 0);
            })

            ->editColumn('total_tip_amount', function ($data) {
                return Currency::format($data->total_tip_amount ?? 0);
            })
            ->editColumn('total_amount', function ($data) {
                return Currency::format($data->total_amount);
            })
            ->orderColumn('total_tax_amount', function ($query, $order) {
                // Using aggregation and recalculating `total_tax_amount` correctly
                $query->selectRaw('
                    DATE(bookings.start_date_time) AS date,
                    COUNT(DISTINCT bookings.id) AS total_booking,
                    COUNT(DISTINCT bookings.system_service_id) AS total_service,
                    COALESCE(SUM(DISTINCT bookings.service_amount), 0) as total_service_amount,
                    COALESCE(SUM(DISTINCT bookings.total_amount), 0) as total_amount,
                    SUM(CASE
                        WHEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, "$.type")) = "percent" THEN bookings.service_amount * JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, "$.percent")) / 100
                        WHEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, "$.type")) = "fixed" THEN JSON_UNQUOTE(JSON_EXTRACT(tx.tax_info, "$.tax_amount"))
                        ELSE 0
                    END) AS total_tax_amount,
                    (SUM(DISTINCT bookings.total_amount) - SUM(DISTINCT bookings.service_amount)) AS total_tax_calculation
                ')
                    ->groupBy('date')
                    ->orderByRaw('(SUM(DISTINCT bookings.total_amount) - SUM(DISTINCT bookings.service_amount)) ' . $order);
            })
            ->addIndexColumn()
            ->rawColumns(['total_tax_amount'])
            ->toJson();
    }

    public function overall_booking_report(Request $request)
    {
        $module_title = __('report.title_overall_report');

        return view('backend.reports.overall-booking-report', compact('module_title'));
    }

    public function overall_booking_report_index_data(Datatables $datatable, Request $request)
    {

        $query = Booking::with('payment')->where('status', 'completed');

        if (isset($request->booing_id)) {
            $query = $query->where('bookings.id', $request->booking_id);
        }

        if (isset($request->date_range)) {
            $dateRange = explode(' to ', $request->date_range);
            $startDate = $dateRange[0] ?? null;
            $endDate = $dateRange[1] ?? $startDate;

            if ($startDate) {
                $query = $query->whereDate('start_date_time', '>=', $startDate)
                    ->whereDate('start_date_time', '<=', $endDate);
            }
        }

        $filter = $request->filter;

        if (isset($filter['booking_date'])) {
            try {
                $bookingDateRange = explode(' to ', $filter['booking_date']);
                $startDate = $bookingDateRange[0] ?? null;
                $endDate = $bookingDateRange[1] ?? $startDate;

                if ($startDate) {
                    $query = $query->whereDate('bookings.start_date_time', '>=', date('Y-m-d', strtotime($startDate)))
                        ->whereDate('bookings.start_date_time', '<=', date('Y-m-d', strtotime($endDate)));
                }
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        if (isset($filter['employee_id'])) {
            $query->where('bookings.employee_id', $filter['employee_id']);
        }
        $query->orderByDesc('bookings.start_date_time'); // Apply the aggregation directly in the ORDER BY clause

        return $datatable->eloquent($query)

            ->editColumn('start_date_time', function ($data) {
                return date('d-m-Y h:i A', strtotime($data->start_date_time));
            })
            ->editColumn('id', function ($data) {
                return setting('booking_invoice_prifix') . $data->id;
            })
            ->editColumn('employee_id', function ($data) {
                return $data->employee->full_name ?? '-';
            })
            ->editColumn('total_service', function ($data) {
                return $data->total_service;
            })
            ->editColumn('total_service_amount', function ($data) {
                return Currency::format($data->service_amount ?? 0);
            })
            ->editColumn('total_tax_amount', function ($data) {
                $totalTaxAmount = 0;
                $taxes = json_decode($data->payment->tax_percentage, true);

                foreach ($taxes as $tax) {
                    if ($tax['type'] === 'percentage') {
                        $percentageAmount = ($tax['value'] / 100) * $data->service_amount;
                        $totalTaxAmount += $percentageAmount;
                    } elseif ($tax['type'] === 'fixed') {
                        $totalTaxAmount += $tax['value'];
                    }
                }

                return Currency::format($totalTaxAmount ?? 0);
            })
            ->editColumn('total_tip_amount', function ($data) {
                return Currency::format($data->total_tip_amount);
            })
            ->editColumn('total_amount', function ($data) {
                return Currency::format($data->total_amount);
            })
            ->orderColumn('employee_id', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = bookings.employee_id LIMIT 1)'), $order);
            }, 1)
            ->addIndexColumn()
            ->rawColumns([])
            ->orderColumns(['id'], '-:column $1')
            ->toJson();
    }

    public function payout_report(Request $request)
    {
        $module_title = __('report.title_staff_report');

        return view('backend.reports.payout-report', compact('module_title'));
    }

    public function payout_report_index_data(Datatables $datatable, Request $request)
    {

        $query = EmployeeEarning::with('employee');

        $filter = $request->filter;

        // if (isset($filter['booking_date'])) {

        //     try {

        //         $startDate = explode(' to ', $filter['booking_date'])[0];
        //         $endDate = explode(' to ', $filter['booking_date'])[1];

        //         $query->whereBetween('payment_date', [$startDate, $endDate]);

        //     } catch (\Exception $e) {
        //         // \Log::error($e->getMessage());
        //     }
        // }

        if (isset($filter['booking_date'])) {
            $bookingDates = explode(' to ', $filter['booking_date']);

            if (count($bookingDates) >= 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($bookingDates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($bookingDates[1]));

                $query->where('payment_date', '>=', $startDate)
                    ->where('payment_date', '<=', $endDate);
            }
        }

        if (isset($filter['employee_id'])) {
            $query->whereHas('employee', function ($q) use ($filter) {
                $q->where('employee_id', $filter['employee_id']);
            });
        }

        return $datatable->eloquent($query)
            ->editColumn('payment_date', function ($data) {
                return customDate($data->payment_date ?? '-');
            })
            ->editColumn('first_name', function ($data) {
                return $data->employee->full_name;
            })
            ->orderColumn('first_name', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT first_name FROM users WHERE id = bookings.employee_id LIMIT 1)'), $order);
            }, 1)
            ->editColumn('commission_amount', function ($data) {
                return Currency::format($data->commission_amount ?? 0);
            })
            ->editColumn('tip_amount', function ($data) {
                return Currency::format($data->tip_amount ?? 0);
            })
            ->editColumn('total_pay', function ($data) {
                return Currency::format($data->total_amount ?? 0);
            })
            ->addIndexColumn()
            ->rawColumns([])
            ->toJson();
    }

    public function staff_report(Request $request)
    {
        $module_title = __('report.title_staff_service_report');

        return view('backend.reports.staff-report', compact('module_title'));
    }

    public function staff_report_index_data(Datatables $datatable, Request $request)
    {
        $query = User::role(['vet', 'groomer', 'walker', 'boarder', 'trainer', 'day_taker'])->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.mobile')
            ->withCount('employeeBooking')
            ->withSum('employeeBooking', 'service_price')
            ->withSum('commission_earning', 'commission_amount')
            ->withSum('tip_earning', 'tip_amount');

        $filter = $request->filter;

        // if(isset($filter['booking_date'])) {

        // try {

        //      $startDate = explode(' to ', $filter['booking_date'])[0];
        //      $endDate = explode(' to ', $filter['booking_date'])[1];

        //       $query->whereBetween('payment_date', [$startDate, $endDate]);

        //       }catch(\Exception $e) {
        //        \Log::error($e->getMessage());
        //       }
        //   }

        if (isset($filter['employee_id'])) {
            $query->where('id', $filter['employee_id']);
        }

        return $datatable->eloquent($query)
            ->editColumn('first_name', function ($data) {
                return $data->full_name;
            })
            ->editColumn('total_services', function ($data) {
                return $data->employee_booking_count ?? 0;
            })
            ->editColumn('total_service_amount', function ($data) {
                return Currency::format($data->employee_booking_sum_service_price ?? 0);
            })
            ->editColumn('total_commission_earn', function ($data) {
                return Currency::format($data->commission_earning_sum_commission_amount ?? 0);
            })
            ->editColumn('total_tip_earn', function ($data) {
                return Currency::format($data->tip_earning_sum_tip_amount ?? 0);
            })
            ->editColumn('total_earning', function ($data) {
                return Currency::format($data->employee_booking_sum_service_price + $data->commission_earning_sum_commission_amount + $data->tip_earning_sum_tip_amount);
            })
            ->addIndexColumn()
            ->rawColumns([])
            ->toJson();
    }

    public function order_report(Request $request)
    {
        $module_title = 'order_report.title';

        $module_name = '.order-report';
        $export_import = true;
        $export_columns = [
            [
                'value' => 'date',
                'text' => 'Date',
            ],
            [
                'value' => 'total_booking',
                'text' => 'No. Booking',
            ],
            [
                'value' => 'total_service',
                'text' => 'No. Services',
            ],
            [
                'value' => 'total_service_amount',
                'text' => 'Service Amount',
            ],
            [
                'value' => 'total_tax_amount',
                'text' => 'Tax Amount',
            ],
            [
                'value' => 'total_tip_amount',
                'text' => 'Tips Amount',
            ],
            [
                'value' => 'total_amount',
                'text' => 'Final Amount',
            ],
        ];
        $export_url = '';

        $totalAdminEarnings = Order::sum('total_admin_earnings');

        return view('backend.reports.order-report', compact('module_title', 'module_name', 'export_import', 'export_columns', 'export_url', 'totalAdminEarnings'));
    }

    public function order_report_index_data(Datatables $datatable, Request $request)
    {

        $orders = OrderItem::with('order');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['code'])) {
                $orders = $orders->whereHas('order', function ($query) use ($filter) {
                    $query->where(function ($q) use ($filter) {
                        $orderGroup = OrderGroup::where('order_code', $filter['code'])->pluck('id');
                        $q->orWhereIn('order_group_id', $orderGroup);
                    });
                });
            }

            if (isset($filter['delivery_status'])) {
                $orders = $orders->where('delivery_status', $filter['delivery_status']);
            }

            if (isset($filter['payment_status'])) {
                $orders = $orders->where('payment_status', $filter['payment_status']);
            }

            if (isset($filter['order_date'][0])) {

                $startDate = $filter['order_date'][0];
                $endDate = $filter['order_date'][1] ?? null;

                if (isset($endDate)) {
                    $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)));
                    $orders->whereDate('created_at', '<=', date('Y-m-d', strtotime($endDate)));
                } else {
                    $orders->whereDate('created_at', date('Y-m-d', strtotime($startDate)));
                }
            }

        }

        $orders = $orders->whereHas('order', function ($query) {
            $query->where(function ($q) {
                $orderGroup = OrderGroup::pluck('id');
                $q->orWhereIn('order_group_id', $orderGroup);
            });
        });

        return $datatable->eloquent($orders)

            ->addIndexColumn()

            ->editColumn('order_code', function ($data) {
                return setting('inv_prefix') . optional(optional($data->order)->orderGroup)->order_code;
            })
            ->editColumn('customer_name', function ($data) {
                $data = $data->order;
                return view('product::backend.order.columns.customer_column', compact('data'));
            })
            ->editColumn('placed_on', function ($data) {
                $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
                return customDate($data->created_at->timezone($timezone));
            })
            //   ->editColumn('items', function ($data) {
            //       return $data->count();
            //   })
            ->editColumn('payment', function ($data) {
                return view('product::backend.order.columns.payment_column', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return view('product::backend.order.columns.status_column', compact('data'));
            })
            ->editColumn('total_admin_earnings', function ($data) {
                // return Currency::format($data->total_admin_earnings);
                $totalprice = $data->total_price;
                $totalTaxAmount = $data->total_tax_amount;
                $totalShippingCost = $data->total_shipping_cost;
                $totalAmount = $totalprice + $totalTaxAmount + $totalShippingCost;

                return \Currency::format($totalAmount);
            })
            ->orderColumn('total_admin_earnings', function ($data, $order) {
                if ($data->count() > 0) {
                    $data->select('order_items.*')->orderBy('total_price', $order);
                }
            }, 1)

            ->filterColumn('customer_name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->editColumn('created_at', function ($data) {
                $diff = Carbon::now()->diffInHours($data->created_at);
                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1')
            ->toJson();


    }
}
