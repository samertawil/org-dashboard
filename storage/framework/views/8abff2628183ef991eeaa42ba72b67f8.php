<div class="flex flex-col gap-6">
    
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <?php if (isset($component)) { $__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::heading','data' => ['level' => '1','size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => '1','size' => 'xl']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('Student Details')); ?>: <?php echo e($student->full_name); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9)): ?>
<?php $attributes = $__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9; ?>
<?php unset($__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9)): ?>
<?php $component = $__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9; ?>
<?php unset($__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal43e8c568bbb8b06b9124aad3ccf4ec97 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal43e8c568bbb8b06b9124aad3ccf4ec97 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::subheading','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::subheading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('View comprehensive information and survey answers for this student.')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal43e8c568bbb8b06b9124aad3ccf4ec97)): ?>
<?php $attributes = $__attributesOriginal43e8c568bbb8b06b9124aad3ccf4ec97; ?>
<?php unset($__attributesOriginal43e8c568bbb8b06b9124aad3ccf4ec97); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal43e8c568bbb8b06b9124aad3ccf4ec97)): ?>
<?php $component = $__componentOriginal43e8c568bbb8b06b9124aad3ccf4ec97; ?>
<?php unset($__componentOriginal43e8c568bbb8b06b9124aad3ccf4ec97); ?>
<?php endif; ?>
        </div>
        <div class="flex gap-2">
            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('student.index')).'','wire:navigate' => true,'variant' => 'ghost','icon' => 'arrow-left','class' => 'print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('student.index')).'','wire:navigate' => true,'variant' => 'ghost','icon' => 'arrow-left','class' => 'print:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('Back to List')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['onclick' => 'printWithDynamicName()','variant' => 'ghost','icon' => 'printer','class' => 'print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['onclick' => 'printWithDynamicName()','variant' => 'ghost','icon' => 'printer','class' => 'print:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(__('Print')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('student.create')): ?>
                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('student.edit', $student)).'','wire:navigate' => true,'variant' => 'primary','icon' => 'pencil','class' => 'print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('student.edit', $student)).'','wire:navigate' => true,'variant' => 'primary','icon' => 'pencil','class' => 'print:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('Edit Student')); ?>

                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $attributes = $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580)): ?>
<?php $component = $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580; ?>
<?php unset($__componentOriginalc04b147acd0e65cc1a77f86fb0e81580); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function printWithDynamicName() {
            const originalTitle = document.title;
            document.title = "<?php echo e($student->full_name); ?>";
            window.print();
            setTimeout(() => {
                document.title = originalTitle;
            }, 100);
        }
    </script>

    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }

            .print\:hidden {
                display: none !important;
            }

            .bg-zinc-50,
            .bg-zinc-100,
            .bg-white {
                background-color: transparent !important;
            }

            .dark\:bg-zinc-800,
            .dark\:bg-zinc-900 {
                background-color: transparent !important;
            }

            .border,
            .border-zinc-200,
            .border-zinc-700 {
                border-color: #e5e7eb !important;
            }

            .shadow-sm {
                shadow: none !important;
            }

            .grid {
                display: block !important;
            }

            .md\:grid-cols-3 {
                display: flex !important;
                flex-direction: column !important;
            }

            .md\:col-span-1,
            .md\:col-span-2 {
                width: 100% !important;
                margin-bottom: 2rem !important;
            }

            .p-6 {
                padding: 1.5rem !important;
            }

            h1,
            h2,
            h3,
            h4 {
                color: black !important;
            }

            .text-zinc-500,
            .text-zinc-400 {
                color: #6b7280 !important;
            }

            @page {
                margin: 2cm;
            }
        }
    </style>

    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
                font-size: 0.85rem !important;
            }

            .print\:hidden {
                display: none !important;
            }

            .bg-zinc-50,
            .bg-zinc-100,
            .bg-white,
            .dark\:bg-zinc-800,
            .dark\:bg-zinc-900 {
                background-color: transparent !important;
            }

            .border,
            .border-zinc-200,
            .border-zinc-700 {
                border-color: #d1d5db !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }

            .grid {
                display: grid !important;
                /* Keep grid for layout control */
            }

            .md\:grid-cols-3 {
                display: block !important;
            }

            .md\:col-span-1,
            .md\:col-span-2 {
                width: 100% !important;
                margin-bottom: 0.75rem !important;
            }

            .p-6,
            .p-4 {
                padding: 0.4rem !important;
            }

            .m-6,
            .mb-6 {
                margin-bottom: 0.25rem !important;
            }

            h1 {
                font-size: 1.1rem !important;
            }

            h2 {
                font-size: 1rem !important;
            }

            h3 {
                font-size: 0.95rem !important;
            }

            .h-16.w-16 {
                display: none !important;
            }

            @page {
                margin: 0.5cm;
            }

            .survey-table th,
            .survey-table td {
                padding: 2px 6px !important;
                border: 1px solid #d1d5db !important;
            }

            .survey-table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            /* Ensure info pairs are on the same line if possible */
            .info-pair {
                display: flex !important;
                gap: 0.5rem !important;
                align-items: baseline !important;
            }
        }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-1 flex flex-col gap-6">
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden p-6 print:p-2">
                <div class="flex items-center gap-4 mb-6 print:mb-2">
                    <div
                        class="h-16 w-16 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center text-zinc-500 dark:text-zinc-400 text-2xl font-bold print:hidden">
                        <?php echo e(strtoupper(substr($student->full_name, 0, 1))); ?>

                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white print:text-lg">
                            <?php echo e($student->full_name); ?></h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400"><?php echo e($student->identity_number); ?></p>
                    </div>
                </div>

                <div class="space-y-3 print:space-y-1">
                    <div class="space-y-3 grid grid-cols-1 gap-y-4 gap-x-2 text-sm print:grid-cols-3 print:gap-y-1 print:gap-x-4">

                        
                    <div class="info-pair">
                        <span class="text-zinc-500 min-w-fit"><?php echo e(__('Group')); ?>:</span>
                        <span
                            class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($student->studentGroup?->name ?? '-'); ?></span>
                    </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Birth Date')); ?>:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($student->birth_date ?? '-'); ?></span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Age When Join')); ?>:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($this->studentData->student_age_when_join ?? '-'); ?>

                             
                                y</span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Gender')); ?>:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                <?php
                                    $genderEnum = \App\Enums\GlobalSystemConstant::tryFrom($student->gender);
                                ?>
                                <?php echo e($genderEnum ? $genderEnum->label() : '-'); ?>

                            </span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Status')); ?>:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                <?php
                                    $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($student->activation);
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusEnum): ?>
                                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                            $student->activation == 1,
                                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                            $student->activation != 1,
                                    ]); ?>">
                                        <?php echo e($statusEnum->label()); ?>

                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Group DB')); ?>:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($student->group->status_name ?? '-'); ?></span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Enrollment')); ?>:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->enrollment_type === 'full_week'): ?>
                                    <?php echo e(__('Full Week')); ?>

                                <?php elseif($student->enrollment_type === 'sat_mon_wed'): ?>
                                    <?php echo e(__('Sat/Mon/Wed')); ?>

                                <?php elseif($student->enrollment_type === 'sun_tue_thu'): ?>
                                    <?php echo e(__('Sun/Mon/Thu')); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>

                        
                    </div>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->notes): ?>
                        <div class="pt-4 mt-4 border-t border-zinc-200 dark:border-zinc-700 print:pt-1 print:mt-1">
                            <div class="text-sm text-zinc-500 mb-1 print:text-xs font-semibold"><?php echo e(__('Notes')); ?>:
                            </div>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 print:text-xs"><?php echo e($student->notes); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="md:col-span-2">
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 print:px-2 print:py-1">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white print:text-base">
                        <?php echo e(__('Survey Answers')); ?></h3>
                </div>

                <div class="p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->surveyStudentanswers->isEmpty()): ?>
                        <div class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                            <?php echo e(__('No survey answers recorded for this student.')); ?>

                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full survey-table border-collapse">
                                <thead class="bg-zinc-50 dark:bg-zinc-900/50 print:bg-white">
                                    <tr>
                                        <th class="px-4 py-2 border text-sm font-semibold text-right">
                                            <?php echo e(__('Question')); ?></th>
                                        <th class="px-4 py-2 border text-sm font-semibold text-right">
                                            <?php echo e(__('Answer')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $student->surveyStudentanswers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                                            <td class="px-4 py-2 border text-sm">
                                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                                    <?php echo e($answer->question?->question_ar_text ?? __('Unknown Question')); ?>

                                                </div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($answer->question?->question_en_text): ?>
                                                    <div class="text-xs text-zinc-500 print:hidden">
                                                        <?php echo e($answer->question->question_en_text); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                            <td class="px-4 py-2 border text-sm text-zinc-700 dark:text-zinc-300">
                                                <?php echo e($answer->answer_ar_text ?? '-'); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($answer->answer_en_text): ?>
                                                    <div
                                                        class="text-xs text-zinc-500 mt-1 border-t border-zinc-100 pt-1 print:hidden">
                                                        <?php echo e($answer->answer_en_text); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/livewire/org-app/student/show.blade.php ENDPATH**/ ?>