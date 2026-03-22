<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminFinancialsController extends Controller
{
    public function index()
    {
        $feePercent = Setting::getVal('ticket_fee_percent', 10);
        $organizerFee = (int) Setting::getVal('organizer_fee', 500);
        $eventFee = (int) Setting::getVal('event_fee', 100);

        $proSubscribersCount = \App\Models\User::where('has_unlimited_events', true)->count();
        $proRevenue = $proSubscribersCount * $organizerFee;

        $paidEventsCount = Event::where('payment_status', 'paid')
                                            ->whereNotNull('cashfree_order_id')
                                            ->where('cashfree_order_id', 'like', 'EVENT_%')
                                            ->count();
        $eventListingRevenue = $paidEventsCount * $eventFee;

        $events = Event::with(['user', 'bookings' => function($query) {
            $query->where('payment_status', 'paid');
        }])
        ->whereHas('bookings', function($query) {
            $query->where('payment_status', 'paid');
        })
        ->orderBy('date', 'desc')
        ->paginate(20);

        $globalSales = 0;
        $globalFees = 0;
        $settledPayouts = 0;
        $outstandingBalance = 0;

        // Calculate global sums efficiently across all events that have generated revenue
        $allRevenueEvents = Event::with(['bookings' => function($q) {
            $q->where('payment_status', 'paid');
        }])->whereHas('bookings', function($q) {
            $q->where('payment_status', 'paid');
        })->get();

        foreach ($allRevenueEvents as $e) {
            $sales = $e->bookings->sum('amount_paid');
            $pCut = round($sales * ($feePercent / 100), 2);
            $vAmount = round($sales - $pCut, 2);

            $globalSales += $sales;
            
            if ($e->payout_status === 'completed') {
                $settledPayouts += $e->payout_amount;
                $globalFees += ($sales - $e->payout_amount);
            } else {
                $outstandingBalance += $vAmount;
                $globalFees += $pCut;
            }
        }

        // Attach calculated variables directly for view table rendering of the paginated subset
        foreach ($events as $event) {
            $eventSales = $event->bookings->sum('amount_paid');
            $eventPlatformCut = round($eventSales * ($feePercent / 100), 2);
            $eventVendorAmount = round($eventSales - $eventPlatformCut, 2);

            $event->calculated_sales = $eventSales;
            $event->calculated_platform_cut = $eventPlatformCut;
            $event->calculated_vendor_amount = $event->payout_status === 'completed' && $event->payout_amount > 0 ? $event->payout_amount : $eventVendorAmount;
        }

        return view('admin.financials.index', compact(
            'events', 
            'globalSales', 
            'globalFees', 
            'settledPayouts', 
            'outstandingBalance',
            'feePercent',
            'proRevenue',
            'proSubscribersCount',
            'eventListingRevenue',
            'paidEventsCount'
        ));
    }
}
