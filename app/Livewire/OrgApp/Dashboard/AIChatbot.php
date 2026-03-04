<?php

namespace App\Livewire\OrgApp\Dashboard;

use App\Services\AIService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class AIChatbot extends Component
{
    public $isOpen = false;
    public $message = '';
    public $messages = [];
    public $isLoading = false;

    protected $aiService;

    public function boot(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        
        if ($this->isOpen && empty($this->messages)) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => __('Hello! I am your AI assistant. How can I help you today?'),
                'time' => now()->format('H:i'),
            ];
        }
    }

    public function sendMessage()
    {
        if (trim($this->message) === '') {
            return;
        }

        $userMessage = $this->message;
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'time' => now()->format('H:i'),
        ];

        $this->message = '';
        $this->isLoading = true;

        // Use a placeholder or just wait for the AI response
        $this->dispatch('scroll-to-bottom');
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
                    'content' => __('Sorry, I encountered an error processing your request.'),
                    'time' => now()->format('H:i'),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->messages[] = [
                'role' => 'assistant',
                'content' => __('An error occurred. Please try again later.'),
                'time' => now()->format('H:i'),
            ];
        }

        $this->isLoading = false;
        $this->dispatch('scroll-to-bottom');
    }

    protected function getSystemDataContext()
    {
        // 1. Dashboard KPIs
        $activeActivitiesCount = \App\Reposotries\ActivityRepo::activites()->count();
        $totalBeneficiaries = \App\Reposotries\ActivityBeneficiaryRepo::beneficiaries()->sum('beneficiaries_count');
        $totalBudget = \App\Reposotries\ActivityRepo::activites()->sum('cost');
        $pendingRequests = \App\Reposotries\PurchaseRequisitionRepo::purchases()->count();

        // 2. Latest Activities
        $latestActivities = \App\Reposotries\ActivityRepo::activites()->take(10);
        $activitiesText = $latestActivities->map(fn($a) => "- {$a->name} | {$a->start_date} | {$a->status_info['name']}")->implode("\n");

        // 3. Employees & Departments
        $employeesCount = \App\Models\Employee::count();
        $departmentsText = \App\Models\Department::take(5)->pluck('name')->implode(', ');
        $recentEmployees = \App\Models\Employee::latest()->take(5)->pluck('full_name')->implode(', ');

        // 4. Events & Tasks
        $upcomingEvents = \App\Models\Event::where('start', '>=', now())->take(5)->get();
        $eventsText = $upcomingEvents->map(fn($e) => "- {$e->title} ({$e->start->format('Y-m-d')})")->implode("\n");

        // 5. Recent Feedback
        $latestFeedback = \App\Models\FeedBack::latest()->take(5)->get();
        $feedbackText = $latestFeedback->map(fn($f) => "- {$f->client_name}: {$f->rating} stars - '{$f->comment}'")->implode("\n");

        // 6. Users & Roles
        $usersCount = \App\Models\User::count();
        $recentUsers = \App\Models\User::latest()->take(5)->pluck('name')->implode(', ');

        return "
        إحصائيات النظام (Current Dashboard Stats):
        - الفعاليات: {$activeActivitiesCount}
        - المستفيدين: {$totalBeneficiaries}
        - الميزانية الملتزم بها: {$totalBudget}$
        - طلبات الشراء المعلقة: {$pendingRequests}
        - إجمالي الموظفين: {$employeesCount} (أحدثهم: {$recentEmployees})
        - الأقسام الرئيسية: {$departmentsText}
        - إجمالي المستخدمين: {$usersCount} (أحدثهم: {$recentUsers})

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
            $role = $msg['role'] === 'user' ? 'User' : 'Assistant';
            $history .= "{$role}: {$msg['content']}\n";
        }
        return $history;
    }

    public function render()
    {
    
        return view('livewire.org-app.dashboard.a-i-chatbot');
    }
}
