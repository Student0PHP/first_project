<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderStatsController extends Controller
{
    protected function secondsToHMS(int $totalSeconds): string
    {
        if ($totalSeconds < 0) {
            $totalSeconds = 0;
        }
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private function calculateStats(): array
    {
        $providerId = Auth::id();
        $from = Carbon::now()->subDays(6)->startOfDay();

        $statsRaw = Order::selectRaw(
            'DATE(created_at) as order_date, ' .
            'SUM(earnings) as total_earnings, ' .
            'SUM(TIME_TO_SEC(total_time)) as total_time_seconds'
        )
            ->where('provider_id', $providerId)
            ->where('status', 'confirmed')
            ->where('created_at', '>=', $from)
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        $stats = [];


        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');

            if ($statsRaw->has($date)) {
                $stats[$date] = [
                    'earnings' => (float)$statsRaw[$date]->total_earnings,
                    'total_time_seconds' => (int)$statsRaw[$date]->total_time_seconds,
                ];
            } else {
                $stats[$date] = [
                    'earnings' => 0,
                    'total_time_seconds' => 0,
                ];
            }
        }

        return $stats;
    }

    public function statsForLast7Days()
    {
        $stats = $this->calculateStats();

        $outputLines = [];
        foreach ($stats as $date => $data) {
            $formattedTotalTime = $this->secondsToHMS($data['total_time_seconds']);
            $line = "Дата: {$date} | Earnings: {$data['earnings']} | Total Time: {$formattedTotalTime}";
            $outputLines[] = $line;
        }
        $outputText = implode("\n", $outputLines);

        return response($outputText, 200)
            ->header('Content-Type', 'text/plain');
    }

    public function showStatsPage()
    {
        $stats = $this->calculateStats();

        foreach ($stats as $date => &$data) {
            $data['formatted_total_time'] = $this->secondsToHMS($data['total_time_seconds']);
            $data['formatted_earnings'] = number_format($data['earnings'], 2);
        }
        unset($data);

        return view('order.stats', [
            'stats' => $stats
        ]);
    }
}
