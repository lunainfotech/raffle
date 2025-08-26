@extends(backpack_view('blank'))

@section('content')

@php
use App\Models\Member;
use Illuminate\Support\Carbon;

$targetTickets = env('TICKET_GOAL', 250);
$deadline = \Carbon\Carbon::parse(env('TICKET_DEADLINE', '2025-10-02'))->format('F j, Y');


// Fetch stats
$totalSales = Member::where('payment_status', 'completed')->count();

$todaySales = Member::whereDate('created_at', today())
->where('payment_status', 'completed')
->count();

$weekSales = Member::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
->where('payment_status', 'completed')
->count();

$monthSales = Member::whereMonth('created_at', now()->month)
->whereYear('created_at', now()->year)
->where('payment_status', 'completed')
->count();
@endphp

{{-- 🔵 FULL-WIDTH INFO BOX --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-warning text-dark shadow p-4">
            <h5 class="font-weight-bold mb-2">
                ⚠️ Important Notice
            </h5>
            <p class="mb-0" style="font-size: 1rem;">
                This event will only happen if <strong>{{ $targetTickets }} tickets</strong> are sold before <strong>{{ $deadline }}</strong>.
                Otherwise, the event will be <strong>automatically cancelled</strong> and <strong>refunds will be issued</strong>.
            </p>
        </div>
    </div>
</div>


{{-- 🔢 4-COLUMN STATS --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-2">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#17a2b8 60%,#138496 100%);">
                        <i class="la la-calendar-day text-white" style="font-size:1.5rem;"></i>
                    </span>
                </div>
                <h6 class="text-muted mb-1">Today</h6>
                <h2 class="font-weight-bold text-info mb-0">{{ $todaySales }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-2">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#ffc107 60%,#ff9800 100%);">
                        <i class="la la-calendar-week text-white" style="font-size:1.5rem;"></i>
                    </span>
                </div>
                <h6 class="text-muted mb-1">This Week</h6>
                <h2 class="font-weight-bold text-warning mb-0">{{ $weekSales }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-2">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#28a745 60%,#218838 100%);">
                        <i class="la la-calendar-alt text-white" style="font-size:1.5rem;"></i>
                    </span>
                </div>
                <h6 class="text-muted mb-1">This Month</h6>
                <h2 class="font-weight-bold text-success mb-0">{{ $monthSales }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mb-2">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg,#343a40 60%,#23272b 100%);">
                        <i class="la la-chart-bar text-white" style="font-size:1.5rem;"></i>
                    </span>
                </div>
                <h6 class="text-muted mb-1">Total</h6>
                <h2 class="font-weight-bold text-dark mb-0">{{ $totalSales }}</h2>
            </div>
        </div>
    </div>
</div>

@endsection