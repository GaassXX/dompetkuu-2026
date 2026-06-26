<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $totalIncome  = Income::where('user_id', $userId)->where('status', 'approved')->sum('amount');
        $totalExpense = Expense::where('user_id', $userId)->where('status', 'approved')->sum('amount');

        $monthIncome  = Income::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $monthExpense = Expense::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $pendingCount = Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_income'      => (float) $totalIncome,
                'total_expense'     => (float) $totalExpense,
                'net_balance'       => (float) ($totalIncome - $totalExpense),
                'month_income'      => (float) $monthIncome,
                'month_expense'     => (float) $monthExpense,
                'month_net'         => (float) ($monthIncome - $monthExpense),
                'pending_transactions' => $pendingCount,
            ],
        ]);
    }

    public function chart(Request $request): JsonResponse
    {
        $userId  = $request->user()->id;
        $months  = min((int) ($request->months ?? 6), 12);
        $data    = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m    = $date->month;
            $y    = $date->year;

            $income  = Income::where('user_id', $userId)
                ->where('status', 'approved')
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->sum('amount');

            $expense = Expense::where('user_id', $userId)
                ->where('status', 'approved')
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->sum('amount');

            $data[] = [
                'month'   => $date->format('M Y'),
                'income'  => (float) $income,
                'expense' => (float) $expense,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
