<?php

namespace App\Services\EducationalActivityDetail\Validation\Strategies;

use App\Services\EducationalActivityDetail\Validation\EducationalActivityDetailValidationStrategy;

class ShortActivityValidationStrategy implements EducationalActivityDetailValidationStrategy
{
    /**
     * Get the validation rules.
     *
     * @param int|null $maxConsistent
     * @param mixed $statusId
     * @return array
     */
    public function getRules(?int $maxConsistent, $statusId): array
    {
        $consistentRule = 'nullable|integer|min:1';
        if ($maxConsistent !== null) {
            $consistentRule .= '|max:' . $maxConsistent;
        }

        return [
            'educational_activity_id' => 'required|exists:educational_activity_schedules,id',
            'consistent'              => $consistentRule,
            'what_learned'            => 'required|string',
            'teacher_report_detail'   => 'required|string',
            'status_id'               => 'required|exists:statuses,id',
            'replaced_activity'       => $statusId != 193 ? 'required|string' : 'nullable|string',
            'replaced_reason'         => $statusId != 193 ? 'required|string' : 'nullable|string',
            'existingAttachments'     => 'required|array|size:1',
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function getMessages(): array
    {
        return [
            'consistent.required'         => 'مطلوب ادخال عدد المنسجمين',
            'what_learned.required'       => 'مطلوب ادخال حقل الاستفادة ',
            'teacher_report_detail.required' => 'مطلوب ادخال حقل التقرير ',
            'status_id.required'          => 'مطلوب ادخال حقل الحالة ',
            'consistent.integer'          => 'مطلوب ادخال عدد صحيح',
            'consistent.max'              => __('لا يمكن أن تتجاوز قيمة المنسجمين بالنشاط عدد الطلاب الحاضرين للنشاط وهو :max طالب.'),
            'consistent.min'              => __('لا يمكن أن تكون قيمة المنسجمين بالنشاط أقل من صفر.'),
            'existingAttachments.required'=> __('يجب إرفاق مرفق واحد بالتقرير.'),
            'existingAttachments.array'   => __('صيغة المرفقات غير صحيحة.'),
            'existingAttachments.size'    => __('يجب أن يكون عدد المرفقات 1 بالضبط، لا أقل ولا أكثر.'),
        ];
    }
}
