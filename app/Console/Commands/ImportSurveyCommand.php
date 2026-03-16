<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ImportSurveyCommand extends Command
{
    // الأمر الذي ستكتبه في Terminal
    protected $signature = 'survey:import {file}'; 
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
            $header = array_shift($rows); // السطر الأول هو العناوين (الأسئلة)
            
            if (empty($header)) {
                $this->error("لا توجد عناوين في الملف!");
                return;
            }

            $questionIds = [];

            DB::beginTransaction();
            
            $this->info("بدء معالجة الأسئلة...");
            
            foreach ($header as $index => $questionText) {
                if ($index == 0 || empty($questionText)) continue; // تخطي عمود "اسم الطالب"

                $questionId = DB::table('survey_questions')->insertGetId([
                    'survey_for_section' => 119,
                    'question_order' => $index,
                    'question_ar_text' => $questionText,
                    'answer_input_type' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $questionIds[$index] = $questionId;
            }

            // 2. معالجة البيانات (الإجابات)
            $this->info("بدء معالجة إجابات الطلاب...");
            foreach ($rows as $row) {
                $accountId = $row[0]; // نفترض أن العمود الأول فيه ID الطالب
                
                if (empty($accountId)) continue;

                foreach ($row as $index => $answerText) {
                    if ($index == 0 || empty($answerText) || !isset($questionIds[$index])) continue;

                    DB::table('survey_answers')->insert([
                        'account_id' => $accountId,
                        'survey_no' => 119,
                        'question_id' => $questionIds[$index],
                        'answer_ar_text' => $answerText,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
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