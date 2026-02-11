<?php

namespace App\Livewire\OrgApp\Reports;

use Livewire\Component;
use App\Models\Activity;
use App\Models\FeedBack;
use Illuminate\Support\Facades\DB;

class FeedbackAnalysis extends Component
{
    public $dateFrom='2023-10-30';
    public $dateTo;

    public function mount()
    {
        // $this->dateFrom = now()->startOfYear()->format('Y-m-d');
        $this->dateTo = now()->endOfYear()->format('Y-m-d');
    }

    public function render()
    {
        // 1. Base Query for Feedback
        $feedbackQuery = FeedBack::query()
             ->whereBetween('created_at', [$this->dateFrom, $this->dateTo]);
             
        $feedbacks = $feedbackQuery->get();

        // 2. KPIs
        $totalFeedback = $feedbacks->count();
        $avgRating = $feedbacks->avg('rating') ?? 0;
        
        // Count positive (4-5), neutral (3), negative (1-2)
        $positiveCount = $feedbacks->where('rating', '>=', 4)->count();
        $neutralCount = $feedbacks->where('rating', 3)->count();
        $negativeCount = $feedbacks->where('rating', '<', 3)->count();
        
        $sentimentScore = $totalFeedback > 0 ? ($positiveCount / $totalFeedback) * 100 : 0;

        // 3. Chart Data
        
        // Chart 1: Rating Distribution (1 to 5 stars)
        $ratingDistribution = $feedbacks->groupBy('rating')
            ->map->count();
            
        // Ensure all ratings 1-5 exist
        $filledRatings = [];
        for ($i=1; $i<=5; $i++) {
            $filledRatings[$i] = $ratingDistribution[$i] ?? 0;
        }

        $ratingChartData = [
             'labels' => array_keys($filledRatings),
             'series' => array_values($filledRatings)
        ];
        
        // Chart 2: Feedback Trend (Monthly Average Rating)
        $trendData = $feedbacks->groupBy(fn($f) => $f->created_at->format('M Y'))
            ->map->avg('rating');
            
        // Sort by date (simple approach: created_at logic, but for array keys M Y, simpler to trust default sort or enhance if needed. 
        // Feedbacks usually come ordered by ID/created_at if generic query, but groupBy might shuffle. 
        // Let's assume OK for now or sort keys.)
        
        $trendChartData = [
             'labels' => $trendData->keys()->toArray(),
             'series' => $trendData->values()->map(fn($v) => round($v, 1))->toArray()
        ];
        
        // Chart 3: Top Activities by Rating (Top 5)
        // Group by activity name (via relation)
        // Need to load Activity relation or if FeedBack has activity_id
        $topActivities = $feedbacks->load('activity')->groupBy(fn($f) => $f->activity->name ?? 'Unknown')
             ->map->avg('rating')
             ->sortDesc()
             ->take(5);

        $activityChartData = [
             'labels' => $topActivities->keys()->toArray(),
             'series' => $topActivities->values()->map(fn($v) => round($v, 1))->toArray()
        ];


        return view('livewire.org-app.reports.feedback-analysis', [
             'kpis' => compact('totalFeedback', 'avgRating', 'sentimentScore'),
             'ratingChartData' => $ratingChartData,
             'trendChartData' => $trendChartData,
             'activityChartData' => $activityChartData
        ]);
    }
}
