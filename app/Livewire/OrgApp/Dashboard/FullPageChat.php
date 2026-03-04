<?php

namespace App\Livewire\OrgApp\Dashboard;

use App\Services\AIService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FullPageChat extends Component
{
    public $message = '';
    public $messages = [];
    public $isLoading = false;

    protected $aiService;

    public function boot(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function mount()
    {
        // Initial greeting
        $this->messages[] = [
            'role' => 'assistant',
            'content' => __('أهلاً بك في مساعدك الذكي الشامل. كيف يمكنني مساعدتك في بيانات النظام اليوم؟'),
            'time' => now()->format('H:i'),
        ];
    }

    public function sendMessage()
    {
        if (empty(trim($this->message))) return;

        $userMessage = $this->message;
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'time' => now()->format('H:i'),
        ];

        $this->message = '';
        $this->isLoading = true;
    }

    public function syncData()
    {
        \Illuminate\Support\Facades\Cache::forget('activites-all');
        \Illuminate\Support\Facades\Cache::forget('Employee-all');
        $this->dispatch('notify', ['message' => __('Data synchronized successfully.')]);
    }

    public function getResponse()
    {
        if (!$this->isLoading) return;

        set_time_limit(120);

        try {
            $history = $this->formatHistory();
            $systemData = $this->getSystemDataContext();
            
            $systemPrompt = "أنت مساعد ذكي مدمج في لوحة تحكم (Dashboard) خاصة بمنظمة. 
            يجب أن تقتصر إجابتك **فقط** على البيانات المتاحة في النظام المذكورة أدناه.
            إذا سألك المستخدم عن شيء خارج هذه البيانات، أخبره بلباقة أنك مخصص لمساعدته في بيانات النظام الحالية فقط.
            
            بيانات النظام الحالية:
            {$systemData}
            
            تعليمات هامة:
            1. أجب باللغة التي سألك بها المستخدم (عربي أو إنجليزي).
            2. كن دقيقاً جداً في الأرقام والتواريخ المذكورة في البيانات.
            3. لا تخترع بيانات غير موجودة (No hallucinations).
            4. إذا لم تجد الإجابة في البيانات، قل 'عذراً، لا تتوفر لدي بيانات حول هذا الموضوع حالياً'.";
            
            $prompt = $systemPrompt . "\n\nسجل المحادثة:\n" . $history . "\nالمساعد الذكي:";
            
            $response = $this->aiService->generateContent($prompt);

            if ($response) {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => $response,
                    'time' => now()->format('H:i'),
                ];
            } else {
                $this->messages[] = [
                    'role' => 'assistant',
                    'content' => __('عذراً، حدث خطأ أثناء معالجة طلبك.'),
                    'time' => now()->format('H:i'),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Full page Chatbot error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => __('حدث خطأ ما. يرجى المحاولة مرة أخرى لاحقاً.'),
                'time' => now()->format('H:i'),
            ];
        }

        $this->isLoading = false;
        $this->dispatch('scroll-to-bottom');
    }

    protected function getSystemDataContext()
    {
        // Reusing the same logic from AIChatbot
        $activeActivitiesCount = \App\Reposotries\ActivityRepo::activites()->count();
        $totalBeneficiaries = \App\Reposotries\ActivityBeneficiaryRepo::beneficiaries()->sum('beneficiaries_count');
        $totalBudget = \App\Reposotries\ActivityRepo::activites()->sum('cost');
        $pendingRequests = \App\Reposotries\PurchaseRequisitionRepo::purchases()->count();

        $latestActivities = \App\Reposotries\ActivityRepo::activites()->take(15);
        $activitiesText = $latestActivities->map(fn($a) => "- {$a->name} | {$a->start_date} | {$a->status_info['name']}")->implode("\n");

        $employeesCount = \App\Models\Employee::count();
        $departmentsText = \App\Models\Department::take(10)->pluck('name')->implode(', ');
        $recentEmployees = \App\Models\Employee::latest()->take(10)->pluck('full_name')->implode(', ');

        $upcomingEvents = \App\Models\Event::where('start', '>=', now())->take(10)->get();
        $eventsText = $upcomingEvents->map(fn($e) => "- {$e->title} ({$e->start->format('Y-m-d')})")->implode("\n");

        $latestFeedback = \App\Models\FeedBack::latest()->take(10)->get();
        $feedbackText = $latestFeedback->map(fn($f) => "- {$f->client_name}: {$f->rating} stars - '{$f->comment}'")->implode("\n");

        $usersCount = \App\Models\User::count();

        return "
        إحصائيات النظام الشاملة:
        - الفعاليات: {$activeActivitiesCount}
        - المستفيدين: {$totalBeneficiaries}
        - الميزانية الملتزم بها: {$totalBudget}$
        - طلبات الشراء المعلقة: {$pendingRequests}
        - إجمالي الموظفين: {$employeesCount} (أحدثهم: {$recentEmployees})
        - الأقسام الرئيسية: {$departmentsText}
        - إجمالي المستخدمين: {$usersCount}

        أحدث الفعاليات (Latest Activities):
        {$activitiesText}

        الأحداث والمهام القادمة (Upcoming Events):
        " . ($eventsText ?: "لا توجد أحداث قادمة مسجلة.") . "

        أحدث التقييمات وآراء الطلاب (Latest Feedback):
        " . ($feedbackText ?: "لا توجد تقييمات حديثة.") . "
        ";
    }

    protected function formatHistory()
    {
        $history = "";
        foreach ($this->messages as $msg) {
            $role = $msg['role'] === 'user' ? 'المستخدم' : 'المساعد';
            $history .= "{$role}: {$msg['content']}\n";
        }
        return $history;
    }

    public function render()
    {
        return view('livewire.org-app.dashboard.full-page-chat');
    }
}
