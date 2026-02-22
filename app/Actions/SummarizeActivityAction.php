<?php

namespace App\Actions;

use App\Models\Activity;
use App\Models\ActivitySummary;
use App\Services\AIService;

class SummarizeActivityAction
{
    public function __construct(protected AIService $aiService)
    {
    }

    public function execute(Activity $activity): ?ActivitySummary
    {
        // 1. Collect data for the prompt
        $activityData = $this->collectActivityData($activity);

        // 2. Build the prompt in Arabic
        $prompt = $this->buildPrompt($activityData);

        // 3. Call AI Service
        $summaryText = $this->aiService->generateContent($prompt);

        if (!$summaryText) {
            return null;
        }

        // 4. Save or update the summary
        return ActivitySummary::updateOrCreate(
            ['activity_id' => $activity->id],
            [
                'summary_text' => $summaryText,
                'model_used' => 'gemini-flash-latest',
            ]
        );
    }

    protected function collectActivityData(Activity $activity): array
    {
        $activity->load(['beneficiaries', 'parcels', 'feedbacks', 'workTeams']);

        return [
            'name' => $activity->name,
            'description' => $activity->description,
            'cost' => $activity->cost,
            'beneficiaries_count' => $activity->beneficiaries->count(),
            'parcels_count' => $activity->parcels->count(),
            'feedbacks' => $activity->feedbacks->map(fn($f) => $f->feedback_text)->filter()->toArray(),
            'average_rating' => $activity->average_rating,
        ];
    }

    protected function buildPrompt(array $data): string
    {
        $feedbacks = implode("\n- ", array_slice($data['feedbacks'], 0, 5));
        
        return "أنت مساعد ذكي متخصص في تحليل مشاريع المنظمات. قم بكتابة ملخص تنفيذي احترافي باللغة العربية للنشاط التالي:
        
        اسم النشاط: {$data['name']}
        الوصف: {$data['description']}
        التكلفة: {$data['cost']}
        عدد المستفيدين: {$data['beneficiaries_count']}
        عدد الطرود/الخدمات الموزعة: {$data['parcels_count']}
        متوسط التقييم: {$data['average_rating']}
        
        عينة من آرء المشاركين:
        - {$feedbacks}
        
        يرجى أن يكون الملخص مركزاً على الإنجازات والأرقام المذكورة، وأن يكون بأسلوب تقرير رسمي موجه للإدارة. لا تذكر أسماء أشخاص، ركز على النتائج.";
    }
}
