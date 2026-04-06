<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudentsResultSurveyCommand extends Command
{
    // الأمر الذي ستكتبه في Terminal
    protected $signature = 'survey:import2 {file}'; 
    protected $description = 'Import student survey from Excel or CSV file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("الملف غير موجود!");
            return;
        }

        try {
            $this->info("بدء قراءة الملف...");
            
            // قراءة الملف باستخدام Laravel Excel
            $data = Excel::toArray([], $filePath);
            
            if (empty($data) || empty($data[0])) {
                $this->error("الملف فارغ!");
                return;
            }

            $rows = $data[0];
          
            $header = array_shift($rows);
  
          
            
            if (empty($header)) {
                $this->error("لا توجد عناوين في الملف!");
                return;
            }

            $questionIds = [];

            DB::beginTransaction();
            
            $this->info("بدء معالجة الأسئلة...");
            
            foreach ($header as $index => $questionText) {
                if ($index == 0 ||$index == 1 ||$index == 2 || empty($questionText)) continue; // تخطي عمود "اسم الطالب"

                $questionId = DB::table('survey_questions')->insertGetId([
                    'survey_for_section' => 142,
                    'question_order' => $index-2,
                    'question_ar_text' => $questionText,
                    'answer_input_type' => 1,
                    'batch_no' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $questionIds[$index] = $questionId;
            }

            // 2. معالجة البيانات (الإجابات)
            $this->info("بدء معالجة إجابات الطلاب...");
            foreach ($rows as $row) {
                // التصحيح: العمود الأول يحتوي على الاسم، والعمود الثالث يحتوي على رقم الهوية (account_id)
                $studentName = $row[0]; // اسم الطالب
                $createdby = $row[1] ;
                $createdat = $row[2]; // تاريخ الانشاء من ملف الاكسل
                
                if (empty($studentName)) continue;

                // تحويل تاريخ الإكسل الرقمي إلى تاريخ صحيح، أو استخراجه من نص عادي
                try {
                    $createdAtObj = is_numeric($createdat) 
                        ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($createdat) 
                        : \Carbon\Carbon::parse($createdat);
                    
                    $formattedCreatedAt = $createdAtObj->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $formattedCreatedAt = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
                }

                foreach ($row as $index => $answerText) {
                    if ($index == 0 || $index == 1 || $index == 2 || empty($answerText) || !isset($questionIds[$index])) continue;

                    DB::table('survey_answers_temp')->insert([
                        'account_id' => $studentName,
                        'created_by' => $createdby,
                        'survey_no' => 142,
                        'question_id' => $questionIds[$index],
                        'answer_ar_text' => $answerText,
                        'created_at' => $formattedCreatedAt,  
                        'updated_at' => \Carbon\Carbon::now(),
                    ]);
                }
            }

            DB::commit();
            $this->info("تم الاستيراد بنجاح (done)");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("فشل الاستيراد : " . $e->getMessage());
        }
    }
}

//command to run the import (make sure to adjust the file path as needed)

//php artisan survey:import "E:\file2.xlsx"