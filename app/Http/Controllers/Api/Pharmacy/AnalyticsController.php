<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Api\BaseController;
use App\Models\Invoice;
use App\Models\Stock;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DB;

class AnalyticsController extends BaseController
{
    //
    //Product Performance:
    //Best-selling products: Identifying products with the highest sales based on quantities or revenue.
    //Low-performing products: Identifying products that are not selling well (i.e. below a certain threshold) in the last week period
    public function bestSelling()
    {
        $most_sold_stocks = $this->getBestSelling();

        return $this->sendResponse($most_sold_stocks, 'Most sold stocks retrieved successfully.');
    }

    public function lowPerformingStocks()
    {
        $low_performing_items = $this->getLowPerformingItems();

        return $this->sendResponse($low_performing_items, 'Low performing items retrieved successfully.');
    }


    //Sales Analytics:
    //Total sales revenue: Summing up the 'total_price' field across all invoices.
    //Average transaction value: Calculating the average of 'total_price' across invoices.
    public function averageTransactionValue()
    {
        $average_transaction_value = $this->getAverageTransactionValue();

        return $this->sendResponse($average_transaction_value, 'Average transaction value retrieved successfully.');
    }

    public function totalSalesRevenue()
    {
        $total_sales_revenue = $this->getTotalSalesRevenue();

        return $this->sendResponse($total_sales_revenue, 'Total sales revenue retrieved successfully.');
    }


    //Inventory Analytics:
    //Expired items: Identifying items with 'expiration_date' that have passed or are close to expiration.
    //Restocking analysis: Determining which products are running low based on their quantities.
    public function expiredStocks()
    {
        $expired_items = $this->getExpiredStocks();

        return $this->sendResponse($expired_items, 'Expired items retrieved successfully.');
    }

    public function restockingAnalysis()
    {
        $scarce_stocks = $this->getRestockAnalysis();

        return $this->sendResponse($scarce_stocks, 'Scarce stocks retrieved successfully.');
    }


    //Customer Behavior Analysis:
    //Customer preferences: Analyzing which products customers are buying together ('basket analysis').
    public function customerPreferences()
    {
        $basketAnalysis = $this->getCustomerPreferences();

        return $this->sendResponse($basketAnalysis, 'Customer preferences retrieved successfully.');
    }

    //Operational Efficiency:
    //Time between creation and update: Analyzing the time difference between 'created_at' and 'updated_at' to assess operational efficiency.
    public function operationalEfficiency()
    {
        $highest_stock_time = $this->getOperationalEfficiencyInformation();
        return $this->sendResponse($highest_stock_time, 'Highest stock time retrieved successfully.');
    }


    //Time-based Analytics:
    //Busiest times: Analyzing the distribution of 'invoice_time' to identify peak business hours.
    //Daily/Weekly/Monthly trends: Tracking sales and stock movement patterns over time.
    //busiest days: Analyzing the distribution of 'invoice_date' to identify peak business days.

    public function sellsTotalPricesSummary()
    {
        $sells_summary = $this->totalSummary();
        return $this->sendResponse($sells_summary, 'Sells summary retrieved successfully.');
    }

    public function busiestTimes()
    {
        $busiest_times = $this->getBusiestTimes();
        return $this->sendResponse($busiest_times, 'Busiest times retrieved successfully.');
    }

    public function busiestDays()
    {
        $busiest_days = $this->getBusiestDays();

        return $this->sendResponse($busiest_days, 'Busiest days retrieved successfully.');
    }

    public function allAnalysis()
    {
        $best_selling = $this->getBestSelling();
        $low_performing_items = $this->getLowPerformingItems();
        $average_transaction_value = $this->getAverageTransactionValue();
        $total_sales_revenue = $this->getTotalSalesRevenue();
        $expired_items = $this->getExpiredStocks();
        $scarce_stocks = $this->getRestockAnalysis();
        $basketAnalysis = $this->getCustomerPreferences();
        $highest_stock_time = $this->getOperationalEfficiencyInformation();
        $sells_summary = $this->totalSummary();
        $busiest_times = $this->getBusiestTimes();
        $busiest_days = $this->getBusiestDays();

        $all_analysis = [
            'best_selling' => $best_selling,
            'low_performing_items' => $low_performing_items,
            'average_transaction_value' => $average_transaction_value,
            'total_sales_revenue' => $total_sales_revenue,
            'expired_items' => $expired_items,
            'scarce_stocks' => $scarce_stocks,
            'customer_preferences' => $basketAnalysis,
            'operational_efficiency' => $highest_stock_time,
            'sells_summary' => $sells_summary,
            'busiest_times' => $busiest_times,
            'busiest_days' => $busiest_days,
        ];

        return $this->sendResponse($all_analysis, 'All analysis retrieved successfully.');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getBusiestTimes(): \Illuminate\Support\Collection
    {
        return Invoice::with('stocks')
            ->select(DB::raw('EXTRACT(HOUR FROM created_at) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }


    /**
     * @return array
     */
    private function totalSummary(): array
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

        return [
            'today' => $today_sells,
            'yesterday' => $yesterday_sells,
            'last_week' => $last_week_sells,
            'last_month' => $last_month_sells,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getOperationalEfficiencyInformation(): \Illuminate\Support\Collection
    {
        return DB::table('stocks')
            ->select('id', 'name', DB::raw('EXTRACT(EPOCH FROM (updated_at - created_at)) AS time_difference'))
            ->orderByDesc('time_difference')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->time_difference = CarbonInterval::seconds($item->time_difference)->cascade()->forHumans();
                return $item;
            });
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getCustomerPreferences(): \Illuminate\Support\Collection
    {
        return DB::table('stocks AS s1')
            ->select('s1.name AS product1', 's2.name AS product2', DB::raw('COUNT(*) AS frequency'))
            ->join('stocks AS s2', 's1.id', '<', 's2.id')
            ->join('invoice_stocks AS i1', 's1.id', '=', 'i1.stock_id')
            ->join('invoice_stocks AS i2', 's2.id', '=', 'i2.stock_id')
            ->where('i1.invoice_id', '=', DB::raw('i2.invoice_id'))
            ->where('i1.stock_id', '!=', DB::raw('i2.stock_id'))
            ->groupBy('s1.name', 's2.name')
            ->orderByDesc('frequency')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getRestockAnalysis(): \Illuminate\Support\Collection
    {
        $scarce_stocks =
            \DB::table('stocks')
                ->select('id', 'name', 'quantity_by_units')
                ->where('quantity_by_units', '<', 10)
                ->get();
        return $scarce_stocks;
    }

    /**
     * @return Stock[]|\LaravelIdea\Helper\App\Models\_IH_Stock_C
     */
    public function getExpiredStocks()
    {
        $expired_items = Stock::where('expiration_date', '<=', Carbon::now()->timestamp)
            ->get();
        return $expired_items;
    }

    /**
     * @return int|mixed
     */
    public function getTotalSalesRevenue()
    {
        $total_sales_revenue = Invoice::where('created_at', '<=', Carbon::now()->addWeek())
            ->sum('total_price');
        return $total_sales_revenue;
    }

    /**
     * @return int|mixed
     */
    public function getAverageTransactionValue()
    {
        $average_transaction_value = Invoice::with('stocks')
            ->avg('total_price');

        if (is_null($average_transaction_value)) {
            $average_transaction_value = 0;
        }
        return $average_transaction_value;
    }

    /**
     * @return Invoice[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\LaravelIdea\Helper\App\Models\_IH_Invoice_C|\LaravelIdea\Helper\App\Models\_IH_Invoice_QB[]
     */
    public function getLowPerformingItems()
    {

        $threshold = 20; // Replace with your desired threshold
        $start_date = Carbon::now()->subDays(7);
        $end_date = Carbon::now();

        $low_performing_items =
            \DB::table('invoice_stocks')
                ->join('invoices', 'invoice_stocks.invoice_id', '=', 'invoices.id')
                ->select('stock_id', \DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('stock_id')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->havingRaw('SUM(quantity) < ?', [$threshold])
                ->get();


        return $low_performing_items;
    }

    /**
     * @return mixed
     */
    public function getBestSelling()
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
        return $most_sold_stocks;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getBusiestDays(): \Illuminate\Support\Collection
    {
        //get the busiest days of the week in descending order
        $busiest_days = Invoice::with('stocks')
            ->select(DB::raw('EXTRACT(DOW FROM created_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        //convert the day number to the day name using map function
        $busiest_days = $busiest_days->map(function ($item) {
            $item->day = Carbon::createFromFormat('n', $item->day)->format('l');
            return $item;
        });
        return $busiest_days;
    }
}
