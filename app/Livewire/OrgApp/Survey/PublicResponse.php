<?php

namespace App\Livewire\OrgApp\Survey;

use App\Enums\GlobalSystemConstant;
use App\Models\SurveyAnswer;
use App\Models\SurveyTable;
use App\Services\CivilRegistryApiServices;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

class PublicResponse extends Component
{
    use WithFileUploads;

    public $surveyId;
    public $account_id;
    public $answers = [];
    public $step = 1; // 1: Login, 2: Survey, 0: Closed, 4: Password Gate
    public $enteredPassword = '';
    public $correctPassword = '';
    public $kid_name = '';
    public $birth_date = '';
    public $gender = '';
    public $apiFailed = false;

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

        $apiResponseService = app()->make(CivilRegistryApiServices::class);

        try {
            $account_id_response = $apiResponseService->getData($this->account_id);

            $this->kid_name = $account_id_response['data']['full_name'];
            $this->birth_date = $account_id_response['data']['birth_date'];
            $this->gender = $account_id_response['data']['gender'] == 0 ?
                GlobalSystemConstant::MALE->value : GlobalSystemConstant::FEMALE->value;
            // $this->relations_data = $response['relations_data'] ?? [];
        } catch (\Exception $e) {

            if (str_contains($e->getMessage(), 'Could not resolve host') || str_contains($e->getMessage(), 'cURL error')) {
                $this->apiFailed = true;
            } else {
                $this->addError('account_id', 'الخدمة غير متاحة حاليا يرجى المحاولة لاحقا');
                return;
            }
        }




        $existingAnswers = SurveyAnswer::where('account_id', $this->account_id)
            ->where('survey_table_id', $this->survey()->id)
            ->get()
            ->keyBy('question_id');

        $requiresPassword = false;

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

                if (strtolower(trim($question->question_ar_text)) === 'password' || trim($question->question_ar_text) === 'كلمة المرور') {
                    if (!empty($answer)) {
                        $this->correctPassword = is_string($answer) ? trim($answer) : $answer;
                        if (is_string($this->correctPassword) && str_starts_with($this->correctPassword, '"') && str_ends_with($this->correctPassword, '"')) {
                            $this->correctPassword = json_decode($this->correctPassword);
                        }
                        $requiresPassword = true;
                    }
                }
            }

            // if ($this->surveyId == 3 && !$this->apiFailed) {
            //     if ($question->id == 99 && !empty($this->kid_name)) $answer = $this->kid_name;
            //     if ($question->id == 100 && !empty($this->birth_date)) $answer = $this->birth_date;
            //     if ($question->id == 101 && !empty($this->gender)) $answer = $this->gender;
            // }

            $this->answers[$question->id] = [
                'answer' => $answer,
                'detail' => $detail,
            ];
        }

        if ($requiresPassword) {
            $this->step = 4;
        } else {
            $this->step = 2;
        }
    }

    public function verifyPassword()
    {
        $this->validate([
            'enteredPassword' => 'required'
        ], [
            'enteredPassword.required' => 'كلمة المرور مطلوبة لإكمال التعديل'
        ]);

        if (trim((string)$this->enteredPassword) === trim((string)$this->correctPassword)) {
            $this->step = 2;
        } else {
            $this->addError('enteredPassword', 'كلمة المرور غير صحيحة. الرجاء المحاولة مرة أخرى.');
        }
    }

    public function submit()
    {
        if ($this->surveyId == 3 && !$this->apiFailed) {
            if (!empty($this->kid_name)) $this->answers[99]['answer'] = $this->kid_name;
            if (!empty($this->birth_date)) $this->answers[100]['answer'] = $this->birth_date;
            if (!empty($this->gender)) $this->answers[101]['answer'] = $this->gender;
        }

        $rules = [];
        $messages = [];

        foreach ($this->survey()->questions as $question) {
            $isPasswordQuestion = (strtolower(trim($question->question_ar_text)) === 'password' || trim($question->question_ar_text) === 'كلمة المرور');

            if ($isPasswordQuestion) {
                // Always force 4 digits for password rule 
                $rules['answers.' . $question->id . '.answer'] = 'required|digits:4';
                $messages['answers.' . $question->id . '.answer.digits'] = 'كلمة المرور يجب أن تتكون من 4 أرقام';
                $messages['answers.' . $question->id . '.answer.required'] = 'إجابة السؤال رقم (' . $question->question_order . ') مطلوبة';
            } else {
                // Main answer is conditionally required based on required_answer
                if (!isset($question->required_answer) || $question->required_answer == 1) {
                    $rules['answers.' . $question->id . '.answer'] = 'required';
                    $messages['answers.' . $question->id . '.answer.required'] = 'إجابة السؤال رقم (' . $question->question_order . ') مطلوبة';
                }
            }

            // If question requires detail, add validation for it
            if ($question->require_detail == 1) {
                $rules['answers.' . $question->id . '.detail'] = 'required';
                $messages['answers.' . $question->id . '.detail.required'] = 'التفاصيل للسؤال رقم (' . $question->question_order . ') مطلوبة';
            }
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
