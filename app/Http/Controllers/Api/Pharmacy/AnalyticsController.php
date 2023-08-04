<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Invoice;
use App\Models\Stock;
use Carbon\Carbon;
use DB;

class AnalyticsController extends BaseController
{
    //
    //Product Performance:
    //Best-selling products: Identifying products with the highest sales based on quantities or revenue.
    //Low-performing products: Identifying products that are not selling well.
    public function bestSelling()
    {
        $most_sold_stocks =
            \DB::table('invoice_stocks')
                ->select('stock_id', \DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('stock_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(10)
                ->get();

        $most_sold_stocks = $most_sold_stocks->map(function ($item) {
            $stock = Stock::find($item->stock_id);
            if ($stock) {
                $item->name = $stock->name;
                $item->quantity = $item->total_quantity;
                unset($item->stock_id);
                unset($item->total_quantity);
            }
            return $item;
        });

        return $this->sendResponse($most_sold_stocks, 'Most sold stocks retrieved successfully.');
    }

    public function lowPerformingStocks()
    {
        $low_performing_items = Invoice::with('stock')
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->get();

        return $this->sendResponse($low_performing_items, 'Low performing items retrieved successfully.');
    }


    //Sales Analytics:
    //Total sales revenue: Summing up the 'total_price' field across all invoices.
    //Average transaction value: Calculating the average of 'total_price' across invoices.
    public function averageTransactionValue()
    {
        $average_transaction_value = Invoice::with('stocks')
            ->avg('total_price');

        if (is_null($average_transaction_value)) {
            $average_transaction_value = 0;
        }

        return $this->sendResponse($average_transaction_value, 'Average transaction value retrieved successfully.');
    }

    public function totalSalesRevenue()
    {
        $total_sales_revenue = Invoice::where('created_at', '<=', Carbon::now()->addWeek())
            ->sum('total_price');

        return $this->sendResponse($total_sales_revenue, 'Total sales revenue retrieved successfully.');
    }


    //Inventory Analytics:
    //Expired items: Identifying items with 'expiration_date' that have passed or are close to expiration.
    //Restocking analysis: Determining which products are running low based on their quantities.
    public function expiredStocks()
    {
        $expired_items = Stock::where('expiration_date', '<=', Carbon::now())
            ->get();

        return $this->sendResponse($expired_items, 'Expired items retrieved successfully.');
    }

    public function restockingAnalysis()
    {
        $scarce_stocks =
            \DB::table('stocks')
                ->select('id', 'name', 'quantity_by_units')
                ->where('quantity_by_units', '<', 10)
                ->get();

        return $this->sendResponse($scarce_stocks, 'Scarce stocks retrieved successfully.');
    }


    //Customer Behavior Analysis:
    //Customer preferences: Analyzing which products customers are buying together ('basket analysis').
    public function customerPreferences()
    {
        $basketAnalysis = DB::table('stocks AS s1')
            ->select('s1.name AS product1', 's2.name AS product2', DB::raw('COUNT(*) AS frequency'))
            ->join('stocks AS s2', 's1.id', '<', 's2.id')
            ->join('invoice_stocks AS i1', 's1.id', '=', 'i1.stock_id')
            ->join('invoice_stocks AS i2', 's2.id', '=', 'i2.stock_id')
            ->where('i1.invoice_id', '=', DB::raw('i2.invoice_id'))
            ->where('i1.stock_id', '!=', DB::raw('i2.stock_id'))
            ->groupBy('s1.name', 's2.name')
            ->orderByDesc('frequency')
            ->get();

        return $this->sendResponse($basketAnalysis, 'Customer preferences retrieved successfully.');
    }

    public function operationalEfficiency()
    {
        $highest_stock_time = DB::table('stocks')
            ->select('id', 'name', DB::raw('TIMESTAMPDIFF(SECOND, created_at, updated_at) AS time_difference'))
            ->orderByDesc('time_difference')
            ->limit(10)
            ->get();

        return $this->sendResponse($highest_stock_time, 'Highest stock time retrieved successfully.');
    }


    //Time-based Analytics:
    //Busiest times: Analyzing the distribution of 'invoice_time' to identify peak business hours.
    //Daily/Weekly/Monthly trends: Tracking sales and stock movement patterns over time.
    public function sellsTotalPricesSummary()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $last_week = Carbon::now()->subDays(7);
        $last_month = Carbon::now()->subDays(30);

        $today_sells = Invoice::with('stocks')
            ->where('created_at', '>=', $today)
            ->sum('total_price');

        $yesterday_sells = Invoice::with('stocks')
            ->whereBetween('created_at', [$yesterday, $today])
            ->sum('total_price');

        $last_week_sells = Invoice::with('stocks')
            ->whereBetween('created_at', [$last_week, $today])
            ->sum('total_price');

        $last_month_sells = Invoice::with('stocks')
            ->whereBetween('created_at', [$last_month, $today])
            ->sum('total_price');

        $sells_summary = [
            'today' => $today_sells,
            'yesterday' => $yesterday_sells,
            'last_week' => $last_week_sells,
            'last_month' => $last_month_sells,
        ];

        return $this->sendResponse($sells_summary, 'Sells summary retrieved successfully.');
    }

    public function busiestTimes()
    {
        $busiest_times = Invoice::with('stocks')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return $this->sendResponse($busiest_times, 'Busiest times retrieved successfully.');
    }
}
