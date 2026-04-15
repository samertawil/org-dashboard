<?php

namespace App\Livewire\OrgApp\Survey;

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use App\Models\SurveyTable;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PublicResponse extends Component
{
    use WithFileUploads;

    public $surveyId;
    public $account_id;
    public $answers = [];
    public $step = 1; // 1: Login, 2: Survey, 0: Closed

    protected $rules = [
        'account_id' => 'required|numeric|min_digits:9|max_digits:9|unique:students,identity_number',
        // 'account_id' => 'required|numeric|min_digits:9|max_digits:9|unique:survey_answers,account_id,NULL,id,survey_table_id,' . 'surveyId',
    ];

    protected $messages = [
        'account_id.required' => 'رقم الهوية مطلوب',
        'account_id.numeric' => 'رقم الهوية يجب أن يكون رقمًا',
        'account_id.min_digits' => 'رقم الهوية يجب أن يكون 9 أرقام',
        'account_id.max_digits' => 'رقم الهوية يجب أن يكون 9 أرقام',
        'account_id.unique' => 'رقم الهوية استفاد مسبقا',
    ];

    public function mount($id)
    {
        $this->surveyId = $id;
        
        if (!$this->survey()->is_active) {
            $this->step = 0; // Closed state
        }
    }

    #[Computed]
    public function survey()
    {
        // Cache the entire survey including its questions for 24 hours (86400 seconds)
        return Cache::remember('survey_data_' . $this->surveyId, 86400, function () {
            return SurveyTable::with('questions')->findOrFail($this->surveyId);
        });
    }

    public function startSurvey()
    {
        $this->validate();
        
        $existingAnswers = SurveyAnswer::where('account_id', $this->account_id)
            ->where('survey_table_id', $this->survey()->id)
            ->get()
            ->keyBy('question_id');

        // Initialize answers array
        foreach ($this->survey()->questions as $question) {
            $existing = $existingAnswers->get($question->id);
            
            $answer = '';
            $detail = '';

            if ($existing) {
                $savedText = $existing->answer_ar_text;
                if ($savedText && str_contains($savedText, ' - التوضيح: ')) {
                    $parts = explode(' - التوضيح: ', $savedText, 2);
                    $answer = $parts[0];
                    $detail = $parts[1] ?? '';
                } else {
                    $answer = $savedText;
                }
                
                // Decode JSON arrays for multiple choice answers if needed
                if (is_string($answer) && str_starts_with($answer, '[') && str_ends_with($answer, ']')) {
                    $decoded = json_decode($answer, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $answer = $decoded;
                    }
                }
            }

            $this->answers[$question->id] = [
                'answer' => $answer,
                'detail' => $detail,
            ];
        }

        $this->step = 2;
    }

    public function submit()
    {
        $rules = [];
        $messages = [];

        foreach ($this->survey()->questions as $question) {
            // Main answer is required by default for all questions
            $rules['answers.' . $question->id . '.answer'] = 'required';
            $messages['answers.' . $question->id . '.answer.required'] = 'إجابة السؤال رقم (' . $question->question_order . ') مطلوبة';

            // If question requires detail, add validation for it
            
            // if ($question->require_detail == 1) {
            //     $rules['answers.' . $question->id . '.detail'] = 'required';
            //     $messages['answers.' . $question->id . '.detail.required'] = 'التفاصيل للسؤال رقم (' . $question->question_order . ') مطلوبة';
            // }
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        foreach ($this->survey()->questions as $question) {
            $answerData = $this->answers[$question->id];
            
            // Handle file upload processing
            if ($question->answer_input_type == 6 && isset($answerData['answer']) && $answerData['answer'] instanceof \Illuminate\Http\UploadedFile) {
                $uploadedFile = $answerData['answer'];
                $mimeType = $uploadedFile->getMimeType();

                // If it's an image, compress and resize it using Intervention Image
                if (str_starts_with($mimeType, 'image/')) {
                    $manager = new ImageManager(new Driver());
                    // Read the uploaded image
                    $image = $manager->read($uploadedFile->getRealPath());
                    
                    // Scale down the image proportionally if it exceeds 1000px in width
                    $image->scaleDown(width: 1000);
                    
                    // Encode as JPEG with 80% quality to heavily reduce file size (usually < 1MB)
                    $encodedImage = $image->toJpeg(quality: 80);
                    
                    // Generate a unique path and save it to the public disk
                    $filename = $uploadedFile->hashName();
                    // change the extension to jpg since we converted it to Jpeg
                    $filename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                    $path = 'surveys/' . $this->survey()->id . '/' . $filename;
                    
                    Storage::disk('public')->put($path, $encodedImage->toString());
                    $answerData['answer'] = $path;
                } else {
                    // For non-images (PDF, word docs), just store them natively
                    $path = $uploadedFile->store('surveys/' . $this->survey()->id, 'public');
                    $answerData['answer'] = $path;
                }
            }

            // Format answer text, optionally including detail if provided
            $mainAnswer = is_array($answerData['answer']) ? json_encode($answerData['answer'], JSON_UNESCAPED_UNICODE) : $answerData['answer'];
            $fullAnswerText = !empty($answerData['detail']) ? $mainAnswer . ' - التوضيح: ' . $answerData['detail'] : $mainAnswer;

            SurveyAnswer::updateOrCreate(
                [
                    'survey_table_id' => $this->survey()->id,
                    'account_id' => $this->account_id,
                    'question_id' => $question->id,
                ],
                [
                    'answer_ar_text' => $fullAnswerText,
                    'survey_no' => $this->survey()->survey_for_section, // Using section as survey_no for legacy compatibility
                ]
            );
        }

        session()->flash('message', 'شكراً لك! تم تسجيل ردك بنجاح.');
        $this->step = 3; // Thank you step
    }
    #[Title('استبيان')]
    public function render()
    {
        return view('livewire.org-app.survey.public-response')
            ->layout('layouts.guest'); // Use guest layout for public access
    }
}
