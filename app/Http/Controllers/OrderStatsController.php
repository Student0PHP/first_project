<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderStatsController extends Controller
{
    public function statsForLast7Days()
    {
        $providerId = Auth::id();
        $from = Carbon::now()->subDays(6)->startOfDay();

        $orders = Order::where('provider_id', $providerId)
            ->where('status', 'confirmed')
            ->where('created_at', '>=', $from)
            ->get()
            ->groupBy(function ($order) {
                return Carbon::parse($order->created_at)->format('Y-m-d');
            });
        $stats = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');
            $stats[$date] = [
                'earnings' => 0,
                'total_time' => 0,
            ];
        }
        foreach ($orders as $date => $dailyOrders) {
            $earnings = $dailyOrders->sum(function ($order) {
                return floatval($order->earnings);
            });
            $totalTime = $dailyOrders->sum(function ($order) {
                return floatval($order->total_time);
            });
            $stats[$date] = [
                'earnings' => $earnings,
                'total_time' => $totalTime,
            ];
        }

        $outputLines = [];
        foreach ($stats as $date => $data) {
            $line = "Дата: {$date} | Earnings: {$data['earnings']} | Total Time: {$data['total_time']}";
            $outputLines[] = $line;
        }
        $outputText = implode("\n", $outputLines);

        return response($outputText, 200)
            ->header('Content-Type', 'text/plain');
    }
}
