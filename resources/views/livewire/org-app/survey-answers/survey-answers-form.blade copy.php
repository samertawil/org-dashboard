<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <flux:field>
        <flux:label>{{ __('Survey No') }} <span class="text-red-500">*</span></flux:label>
        <flux:input wire:model="survey_no" type="number" />
        <flux:error name="survey_no" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Account ID') }}</flux:label>
        <flux:input wire:model="account_id" type="number" />
        <flux:error name="account_id" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Question') }}</flux:label>
        <flux:select wire:model="question_id" :placeholder="__('Select Question')">
            @foreach($this->questions as $question)
                <option value="{{ $question->id }}">{{ $question->question_ar_text ?? $question->question_en_text }}</option>
            @endforeach
        </flux:select>
        <flux:error name="question_id" />
    </flux:field>

    <flux:field>
        <flux:label>{{ __('Created By') }}</flux:label>
        <flux:select wire:model="created_by" :placeholder="__('Select Employee')">
            @foreach($this->employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
            @endforeach
        </flux:select>
        <flux:error name="created_by" />
    </flux:field>

    <flux:field class="col-span-1 md:col-span-2">
        <flux:label>{{ __('Answer (AR)') }}</flux:label>
        <flux:textarea wire:model="answer_ar_text" rows="3" />
        <flux:error name="answer_ar_text" />
    </flux:field>

    <flux:field class="col-span-1 md:col-span-2">
        <flux:label>{{ __('Answer (EN)') }}</flux:label>
        <flux:textarea wire:model="answer_en_text" rows="3" />
        <flux:error name="answer_en_text" />
    </flux:field>
</div>
