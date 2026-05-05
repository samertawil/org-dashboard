<div class="flex flex-col gap-6">
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
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
<?php echo e(__('Student Details')); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::subheading','data' => ['class' => 'hidden sm:block']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::subheading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'hidden sm:block']); ?>
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
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <span title="<?php echo e(__('Go back to student list')); ?>">
                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('student.index')).'','wire:navigate' => true,'variant' => 'ghost','icon' => 'arrow-left','class' => 'flex-1 sm:flex-none print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('student.index')).'','wire:navigate' => true,'variant' => 'ghost','icon' => 'arrow-left','class' => 'flex-1 sm:flex-none print:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('Back')); ?>

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
            </span>
            <span title="<?php echo e(__('View grading scale results')); ?>">
                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => '$set(\'showGradingScale\', true)','variant' => 'ghost','icon' => 'chart-bar','class' => 'flex-1 sm:flex-none print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'showGradingScale\', true)','variant' => 'ghost','icon' => 'chart-bar','class' => 'flex-1 sm:flex-none print:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php echo e(__('Scale')); ?>

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
            </span>
            <span title="<?php echo e(__('Print student report')); ?>">
                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['onclick' => 'printWithDynamicName()','variant' => 'ghost','icon' => 'printer','class' => 'flex-1 sm:flex-none print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['onclick' => 'printWithDynamicName()','variant' => 'ghost','icon' => 'printer','class' => 'flex-1 sm:flex-none print:hidden']); ?>
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
            </span>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('student.create')): ?>
                <span title="<?php echo e(__('Edit student details')); ?>">
                    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['href' => ''.e(route('student.edit', $student)).'','wire:navigate' => true,'variant' => 'primary','icon' => 'pencil','class' => 'flex-1 sm:flex-none print:hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('student.edit', $student)).'','wire:navigate' => true,'variant' => 'primary','icon' => 'pencil','class' => 'flex-1 sm:flex-none print:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php echo e(__('Edit')); ?>

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
                </span>
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
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-6 print:mb-2 text-center sm:text-start">
                    <div
                        class="h-16 w-16 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl font-bold shrink-0 print:hidden">
                        <?php echo e(strtoupper(substr($student->full_name, 0, 1))); ?>

                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white print:text-lg">
                            <?php echo e($student->full_name); ?></h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400"><?php echo e($student->identity_number); ?></p>
                    </div>
                </div>

                <div class="space-y-3 print:space-y-1">
                    <div
                        class="space-y-3 grid grid-cols-1 gap-y-4 gap-x-2 text-sm print:grid-cols-3 print:gap-y-1 print:gap-x-4">


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

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lateSurveyStudentData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        
                        <div class="info-pair flex">
                            <span class="text-zinc-500 min-w-fit"><?php echo e(__('Late Survey')); ?>:</span>
                            <div
                                class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($data->section_name ?? $data->survey_for_section); ?></div>
                        </div>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                </div>
            </div>
        </div>

        
        <div class="md:col-span-2">
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 print:px-2 print:py-1">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white print:text-base">
                        <?php echo e(__('اجابات النماذج والتقيمات')); ?></h3>
                </div>

                <div class="p-6 space-y-8">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($comparisonResults)): ?>
                    <div class="space-y-4">
                        <?php if (isset($component)) { $__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::heading','data' => ['level' => '3','class' => 'flex items-center gap-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => '3','class' => 'flex items-center gap-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'chart-bar','class' => 'size-5 text-indigo-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chart-bar','class' => 'size-5 text-indigo-500']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                            <?php echo e(__('نتائج قياس الأثر والتقدم (Pre vs Post)')); ?>

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

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $comparisonResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pair): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                                    <tr>
                                        <th colspan="5" class="px-4 py-2 text-center text-xs font-bold text-zinc-600 bg-indigo-50/50 dark:bg-indigo-900/20">
                                            <?php echo e($pair->pre_name); ?> vs <?php echo e($pair->post_name); ?>

                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-semibold text-zinc-500 uppercase"><?php echo e(__('Domain')); ?></th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase"><?php echo e(__('Pre')); ?></th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase"><?php echo e(__('Post')); ?></th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase"><?php echo e(__('Progress')); ?></th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase"><?php echo e(__('Status')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pair->domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50">
                                        <td class="px-3 py-2 text-xs font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($domain['name']); ?></td>
                                        <td class="px-3 py-2 text-xs text-center text-zinc-600 tracking-tighter"><?php echo e($domain['pre']); ?></td>
                                        <td class="px-3 py-2 text-xs text-center text-zinc-600 tracking-tighter"><?php echo e($domain['post']); ?></td>
                                        <td class="px-3 py-2 text-xs text-center font-bold">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($domain['diff'] !== null): ?>
                                            <span class="<?php echo e($domain['diff'] >= 0 ? 'text-emerald-600' : 'text-red-600'); ?>">
                                                <?php echo e($domain['diff'] > 0 ? '+' : ''); ?><?php echo e($domain['diff']); ?>%
                                            </span>
                                            <?php else: ?>
                                            <span class="text-zinc-400">---</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border" style="background-color: <?php echo e($domain['color']); ?>20; color: <?php echo e($domain['color']); ?>; border-color: <?php echo e($domain['color']); ?>40">
                                                <?php echo e($domain['evaluation']); ?>

                                            </span>
                                        </td>
                                    </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pair->total): ?>
                                <tfoot class="bg-zinc-50/50 dark:bg-zinc-900/30">
                                    <tr class="font-bold">
                                        <td class="px-3 py-2 text-xs text-indigo-700 dark:text-indigo-400"><?php echo e(__('Total Progress')); ?></td>
                                        <td class="px-3 py-2 text-xs text-center"><?php echo e($pair->total['pre']); ?></td>
                                        <td class="px-3 py-2 text-xs text-center"><?php echo e($pair->total['post']); ?></td>
                                        <td class="px-3 py-2 text-xs text-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pair->total['diff'] !== null): ?>
                                            <span class="<?php echo e($pair->total['diff'] >= 0 ? 'text-emerald-700' : 'text-red-700'); ?>">
                                                <?php echo e($pair->total['diff'] > 0 ? '+' : ''); ?><?php echo e($pair->total['diff']); ?>%
                                            </span>
                                            <?php else: ?>
                                            <span class="text-zinc-400">---</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" style="color: <?php echo e($pair->total['color']); ?>">
                                                <?php echo e($pair->total['evaluation']); ?>

                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </table>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                    <?php if (isset($component)) { $__componentOriginalc481942d30cc0ab06077963cf20a45e8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc481942d30cc0ab06077963cf20a45e8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::separator','data' => ['variant' => 'subtle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'subtle']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $attributes = $__attributesOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__attributesOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc481942d30cc0ab06077963cf20a45e8)): ?>
<?php $component = $__componentOriginalc481942d30cc0ab06077963cf20a45e8; ?>
<?php unset($__componentOriginalc481942d30cc0ab06077963cf20a45e8); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->surveyStudentanswers->isEmpty()): ?>
                        <div class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                            <?php echo e(__('No survey answers recorded for this student.')); ?>

                        </div>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $student->surveyStudentanswers->unique('survey_no')->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            
                            <div class="md:hidden space-y-4 mb-8 print:hidden">
                                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                    <h4 class="text-base font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'clipboard-document-list','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clipboard-document-list','size' => 'sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                                        <?php echo e($survey->surveyfor->status_name); ?>

                                    </h4>
                                </div>

                                <div class="space-y-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->sortBy('question.question_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <?php
                                            $displayAnswerAr = $answer->answer_ar_text;
                                            if (!empty($answer->answer_ar_text) && !empty($answer->question?->answer_options)) {
                                                $decodedAr = json_decode($answer->answer_ar_text, true);
                                                $valuesAr = json_last_error() === JSON_ERROR_NONE && is_array($decodedAr) ? $decodedAr : [$answer->answer_ar_text];
                                                $labelsAr = [];
                                                foreach ($valuesAr as $val) {
                                                    $found = $val;
                                                    $options = is_string($answer->question->answer_options) ? json_decode($answer->question->answer_options, true) : $answer->question->answer_options;
                                                    if (is_array($options)) {
                                                        foreach ($options as $option) {
                                                            if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                                                                if ((string) $option['value'] === (string) $val) { $found = $option['label']; break; }
                                                            } elseif (is_string($option)) {
                                                                if ((string) $option === (string) $val) { $found = $option; break; }
                                                            }
                                                        }
                                                    }
                                                    $labelsAr[] = $found;
                                                }
                                                $displayAnswerAr = implode('، ', $labelsAr);
                                            } else {
                                                $decodedAr = json_decode($answer->answer_ar_text, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAr)) {
                                                    $displayAnswerAr = implode('، ', $decodedAr);
                                                }
                                            }
                                        ?>

                                        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-3">
                                            <div class="flex items-start gap-3">
                                                <span class="shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700 text-xs font-bold text-zinc-500">
                                                    <?php echo e($answer->question?->question_order); ?>

                                                </span>
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white leading-relaxed">
                                                    <?php echo e($answer->question?->question_ar_text); ?>

                                                </div>
                                            </div>
                                            
                                            <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-3 rounded-lg border border-emerald-100/50 dark:border-emerald-800/30">
                                                <div class="text-[10px] uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold mb-1"><?php echo e(__('الإجابة')); ?></div>
                                                <div class="text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed">
                                                    <?php echo e($displayAnswerAr ?? '-'); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>

                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700 text-[10px] text-zinc-500 space-y-1">
                                    <?php
                                        $creators = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->creator?->full_name ?? $ans->created_by)->filter()->unique()->implode('، ');
                                        $created = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->created_at ? \Carbon\Carbon::parse($ans->created_at)->format('Y-m-d') : null)->filter()->unique()->implode('، ');
                                    ?>
                                    <div class="flex items-center gap-2">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'user','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user','size' => 'xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                                        <span><?php echo e(__('بواسطة')); ?>: <?php echo e($creators ?: '-'); ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calendar','size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','size' => 'xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                                        <span dir="ltr"><?php echo e($created); ?></span>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="hidden md:block overflow-x-auto mb-8 print:block">
                                <table class="w-full survey-table border-collapse">
                                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 print:bg-white">
                                        <tr>
                                            <th class="px-4 py-2 border text-sm font-semibold text-center">
                                             # </th>
                                            <th class="px-4 py-2 border text-sm font-semibold text-center">
                                                <?php echo e(__('أسئلة نموذج')); ?> - <?php echo e($survey->surveyfor->status_name); ?> </th>
                                            <th class="px-4 py-2 border text-sm font-semibold text-center">
                                                <?php echo e(__('الإجابة')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->sortBy('question.question_order'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                                                <td class="px-4 py-2 border text-sm">
                                                    <div class="font-medium text-zinc-900 dark:text-zinc-100"
                                                        style="width: 10px;">
                                                        <?php echo e($answer->question?->question_order ?? __('Unknown Question')); ?>

                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 border text-sm">
                                                    <div class="font-medium text-zinc-900 dark:text-zinc-100"
                                                        style="width: 350px;">
                                                        <?php echo e($answer->question?->question_ar_text ?? __('Unknown Question')); ?>

                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($answer->question?->question_en_text): ?>
                                                        <div class="text-xs text-zinc-500 print:hidden">
                                                            <?php echo e($answer->question->question_en_text); ?>

                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td class="px-4 py-2 border text-sm text-zinc-700 dark:text-zinc-300"
                                                    style="width: 200px;">
                                                    <?php
                                                        $displayAnswerAr = $answer->answer_ar_text;
                                                        if (!empty($answer->answer_ar_text) && !empty($answer->question?->answer_options)) {
                                                            $decodedAr = json_decode($answer->answer_ar_text, true);
                                                            $valuesAr = json_last_error() === JSON_ERROR_NONE && is_array($decodedAr) ? $decodedAr : [$answer->answer_ar_text];
                                                            $labelsAr = [];
                                                            foreach ($valuesAr as $val) {
                                                                $found = $val;
                                                                $options = is_string($answer->question->answer_options) ? json_decode($answer->question->answer_options, true) : $answer->question->answer_options;
                                                                if (is_array($options)) {
                                                                    foreach ($options as $option) {
                                                                        if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                                                                            if ((string) $option['value'] === (string) $val) { $found = $option['label']; break; }
                                                                        } elseif (is_string($option)) {
                                                                            if ((string) $option === (string) $val) { $found = $option; break; }
                                                                        }
                                                                    }
                                                                }
                                                                $labelsAr[] = $found;
                                                            }
                                                            $displayAnswerAr = implode('، ', $labelsAr);
                                                        } else {
                                                            $decodedAr = json_decode($answer->answer_ar_text, true);
                                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAr)) {
                                                                $displayAnswerAr = implode('، ', $decodedAr);
                                                            }
                                                        }
                                                    ?>
                                                    <?php echo e($displayAnswerAr ?? '-'); ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($answer->answer_en_text): ?>
                                                        <div class="text-xs text-zinc-500 mt-1 border-t border-zinc-100 pt-1 print:hidden">
                                                            <?php echo e($answer->answer_en_text); ?>

                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </tbody>
                                    <tfoot class="bg-zinc-50 dark:bg-zinc-900/50 print:bg-white">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 border text-sm text-zinc-500 dark:text-zinc-400 text-center font-medium">
                                                <?php
                                                    $creators = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->creator?->full_name ?? $ans->created_by)->filter()->unique()->implode('، ');
                                                    $created = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->created_at ? \Carbon\Carbon::parse($ans->created_at)->format('Y-m-d') : null)->filter()->unique()->implode('، ');
                                                ?>
                                                <div class="flex items-center justify-center gap-6">
                                                    <div class="flex items-center gap-1">
                                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'user','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user','class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                                                        <span><?php echo e(__('بواسطة')); ?>: <?php echo e($creators ?: '-'); ?></span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'calendar','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'calendar','class' => 'size-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $attributes = $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2)): ?>
<?php $component = $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2; ?>
<?php unset($__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2); ?>
<?php endif; ?>
                                                        <span dir="ltr"><?php echo e($created); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="text-center text-zinc-500 py-4"><?php echo e(__('No surveys found')); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>
            </div>
        </div>
        <?php if (isset($component)) { $__componentOriginal8cc9d3143946b992b324617832699c5f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cc9d3143946b992b324617832699c5f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::modal.index','data' => ['wire:model' => 'showGradingScale','class' => 'md:w-[800px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'showGradingScale','class' => 'md:w-[800px]']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showGradingScale): ?>
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <?php if (isset($component)) { $__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::heading','data' => ['level' => '2','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['level' => '2','size' => 'lg']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php echo e(__('Grading Scale Results')); ?>

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
                </div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->studentGradingScale->unique('survey_no')->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
<div>
    <p class="text-center" style="font-weight: 500;"><?php echo $survey->status_name ?? '-'; ?></p>
</div>
<div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
    <table class="w-full table-auto divide-y divide-zinc-200 dark:divide-zinc-700">
        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
            <tr>
                <th class="px-4 py-3 text-start text-xs font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('Domain')); ?></th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('Total Marks')); ?></th>
               
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('Grade %')); ?></th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('Evaluation')); ?></th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('Description')); ?></th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $this->studentGradingScale->where('survey_no', $survey->survey_no); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                    <?php
                        $domain = \App\Models\Status::find($grade->domain_id);
                    ?>
                    <td class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-3 text-sm font-medium',
                     
                        'text-red-600 dark:text-red-400' => !$domain,
                    ]); ?>">
                        <p><?php echo e($domain?->status_name ?? __('التقييم الكلي للطفل')); ?></p>
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-zinc-600 dark:text-zinc-300">
                        <?php echo e(intval($grade->total_marks)); ?>/<?php echo e($grade->max_total_score); ?>

                    </td>
                    
                    <td class="px-4 py-3 text-sm text-center font-bold text-indigo-600 dark:text-indigo-400">
                        <?php echo e($grade->grade); ?>%
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <?php echo e($grade->evaluation); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5  text-xs font-medium ">
                            <?php echo e($grade->description); ?>

                        </span>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                        <?php echo e(__('No grading scale data available for this student.')); ?>

                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
               

                <div class="flex justify-end">
                    <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['wire:click' => '$set(\'showGradingScale\', false)','variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'showGradingScale\', false)','variant' => 'ghost']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <?php echo e(__('Close')); ?>

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
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cc9d3143946b992b324617832699c5f)): ?>
<?php $attributes = $__attributesOriginal8cc9d3143946b992b324617832699c5f; ?>
<?php unset($__attributesOriginal8cc9d3143946b992b324617832699c5f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cc9d3143946b992b324617832699c5f)): ?>
<?php $component = $__componentOriginal8cc9d3143946b992b324617832699c5f; ?>
<?php unset($__componentOriginal8cc9d3143946b992b324617832699c5f); ?>
<?php endif; ?>
</div>
</div>

</div>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/livewire/org-app/student/show.blade.php ENDPATH**/ ?>