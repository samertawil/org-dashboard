<div class="<?php echo e($isDashboard ? '' : 'container mx-auto'); ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
    
    
    <script src="https://cdn.jsdelivr.net/npm/leaflet-browser-print@1.0.6/dist/leaflet.browser.print.min.js"></script>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-measure@3.1.0/dist/leaflet-measure.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet-measure@3.1.0/dist/leaflet-measure.js"></script>

    <style>
        .leaflet-container { z-index: 5 !important; }
        .sector-icon {
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            color: white; font-size: 14px; font-weight: bold;
        }
        .map-viewport-container {
            height: 450px;
        }
        @media (min-width: 640px) {
            .map-viewport-container {
                height: <?php echo e($isDashboard ? '250px' : '700px'); ?>;
            }
        }
    </style>

    <div class="space-y-4">
        
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <?php if (isset($component)) { $__componentOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7d5f44bf2a2d803ed0b55f72f1f82e2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::icon.index','data' => ['name' => 'map','class' => 'size-6 text-indigo-600 dark:text-indigo-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'map','class' => 'size-6 text-indigo-600 dark:text-indigo-400']); ?>
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
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <?php if (isset($component)) { $__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::heading','data' => ['size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'xl']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e(__('Operations Map Dashboard')); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9)): ?>
<?php $attributes = $__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9; ?>
<?php unset($__attributesOriginale0fd5b6a0986beffac17a0a103dfd7b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9)): ?>
<?php $component = $__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9; ?>
<?php unset($__componentOriginale0fd5b6a0986beffac17a0a103dfd7b9); ?>
<?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isDashboard): ?>
                                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['icon' => 'arrows-pointing-out','variant' => 'subtle','size' => 'sm','href' => ''.e(route('operations.map')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'arrows-pointing-out','variant' => 'subtle','size' => 'sm','href' => ''.e(route('operations.map')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['style' => 'color:red !important;','icon' => 'home','variant' => 'subtle','size' => 'sm','href' => ''.e(route('dashboard')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'color:red !important;','icon' => 'home','variant' => 'subtle','size' => 'sm','href' => ''.e(route('dashboard')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
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
<?php echo e(__('Gaza Strip Operations View')); ?> <?php echo $__env->renderComponent(); ?>
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
                </div>

                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-3 w-full sm:w-auto">
                    <?php $totalCount = $this->allCamps->count() + $this->allActivities->count(); ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isDashboard): ?>
                        <div class="flex flex-wrap items-center justify-center gap-2 sm:mr-4 sm:border-r border-zinc-200 dark:border-zinc-700 sm:pr-4">
                            <?php if (isset($component)) { $__componentOriginalc04b147acd0e65cc1a77f86fb0e81580 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc04b147acd0e65cc1a77f86fb0e81580 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['id' => 'heatmap-toggle','icon' => 'fire','variant' => 'subtle','size' => 'sm','onclick' => 'window.toggleHeatmap()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'heatmap-toggle','icon' => 'fire','variant' => 'subtle','size' => 'sm','onclick' => 'window.toggleHeatmap()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <span class="hidden sm:inline"><?php echo e(__('Heatmap')); ?></span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['icon' => 'variable','variant' => 'subtle','size' => 'sm','onclick' => 'alert(\'قم باستخدام أيقونة المسطرة في أعلى يسار الخريطة للبدء في قياس المسافات والمساحات بدقة.\')']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'variable','variant' => 'subtle','size' => 'sm','onclick' => 'alert(\'قم باستخدام أيقونة المسطرة في أعلى يسار الخريطة للبدء في قياس المسافات والمساحات بدقة.\')']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <span class="hidden sm:inline"><?php echo e(__('Measure')); ?></span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['icon' => 'printer','variant' => 'subtle','size' => 'sm','onclick' => 'window.printMap()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'printer','variant' => 'subtle','size' => 'sm','onclick' => 'window.printMap()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <span class="hidden sm:inline"><?php echo e(__('Print')); ?></span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::button.index','data' => ['icon' => 'document-arrow-down','variant' => 'subtle','size' => 'sm','onclick' => 'window.exportMapData()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'document-arrow-down','variant' => 'subtle','size' => 'sm','onclick' => 'window.exportMapData()']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <span class="hidden sm:inline"><?php echo e(__('Export')); ?></span>
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
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="flex items-center gap-3">
                        <?php if (isset($component)) { $__componentOriginal4cc377eda9b63b796b6668ee7832d023 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4cc377eda9b63b796b6668ee7832d023 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::badge.index','data' => ['color' => 'indigo','class' => 'px-3 py-1 font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'indigo','class' => 'px-3 py-1 font-bold']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e($totalCount); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $attributes = $__attributesOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__attributesOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4cc377eda9b63b796b6668ee7832d023)): ?>
<?php $component = $__componentOriginal4cc377eda9b63b796b6668ee7832d023; ?>
<?php unset($__componentOriginal4cc377eda9b63b796b6668ee7832d023); ?>
<?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginale5140a44d7461450cb1378cd5b47dfc8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale5140a44d7461450cb1378cd5b47dfc8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::radio.group.index','data' => ['wire:model.live' => 'filterType','variant' => 'segmented','size' => 'sm','class' => 'flex-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::radio.group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'filterType','variant' => 'segmented','size' => 'sm','class' => 'flex-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php if (isset($component)) { $__componentOriginal63a6e9bef56b25b50cfa996fe1154357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal63a6e9bef56b25b50cfa996fe1154357 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::radio.index','data' => ['value' => 'all','label' => ''.e(__('All')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'all','label' => ''.e(__('All')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal63a6e9bef56b25b50cfa996fe1154357)): ?>
<?php $attributes = $__attributesOriginal63a6e9bef56b25b50cfa996fe1154357; ?>
<?php unset($__attributesOriginal63a6e9bef56b25b50cfa996fe1154357); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63a6e9bef56b25b50cfa996fe1154357)): ?>
<?php $component = $__componentOriginal63a6e9bef56b25b50cfa996fe1154357; ?>
<?php unset($__componentOriginal63a6e9bef56b25b50cfa996fe1154357); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal63a6e9bef56b25b50cfa996fe1154357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal63a6e9bef56b25b50cfa996fe1154357 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::radio.index','data' => ['value' => 'camps','label' => ''.e(__('Camps')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'camps','label' => ''.e(__('Camps')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal63a6e9bef56b25b50cfa996fe1154357)): ?>
<?php $attributes = $__attributesOriginal63a6e9bef56b25b50cfa996fe1154357; ?>
<?php unset($__attributesOriginal63a6e9bef56b25b50cfa996fe1154357); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63a6e9bef56b25b50cfa996fe1154357)): ?>
<?php $component = $__componentOriginal63a6e9bef56b25b50cfa996fe1154357; ?>
<?php unset($__componentOriginal63a6e9bef56b25b50cfa996fe1154357); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal63a6e9bef56b25b50cfa996fe1154357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal63a6e9bef56b25b50cfa996fe1154357 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::radio.index','data' => ['value' => 'activities','label' => ''.e(__('Activities')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::radio'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'activities','label' => ''.e(__('Activities')).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal63a6e9bef56b25b50cfa996fe1154357)): ?>
<?php $attributes = $__attributesOriginal63a6e9bef56b25b50cfa996fe1154357; ?>
<?php unset($__attributesOriginal63a6e9bef56b25b50cfa996fe1154357); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal63a6e9bef56b25b50cfa996fe1154357)): ?>
<?php $component = $__componentOriginal63a6e9bef56b25b50cfa996fe1154357; ?>
<?php unset($__componentOriginal63a6e9bef56b25b50cfa996fe1154357); ?>
<?php endif; ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale5140a44d7461450cb1378cd5b47dfc8)): ?>
<?php $attributes = $__attributesOriginale5140a44d7461450cb1378cd5b47dfc8; ?>
<?php unset($__attributesOriginale5140a44d7461450cb1378cd5b47dfc8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale5140a44d7461450cb1378cd5b47dfc8)): ?>
<?php $component = $__componentOriginale5140a44d7461450cb1378cd5b47dfc8; ?>
<?php unset($__componentOriginale5140a44d7461450cb1378cd5b47dfc8); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isDashboard): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-zinc-100 dark:border-zinc-700 pt-4">
                    <?php if (isset($component)) { $__componentOriginala467913f9ff34913553be64599ec6e92 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala467913f9ff34913553be64599ec6e92 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::select.index','data' => ['label' => ''.e(__('Go to Camp')).'','searchable' => true,'onchange' => 'window.moveMapToV2(this)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => ''.e(__('Go to Camp')).'','searchable' => true,'onchange' => 'window.moveMapToV2(this)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <option value=""><?php echo e(__('Select a Camp...')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->campsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $camp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($camp->id); ?>" data-lat="<?php echo e($camp->latitude); ?>" data-lng="<?php echo e($camp->longitudes); ?>">
                                <?php echo e($camp->name); ?> <?php echo e(!$camp->latitude ? ' (No GPS)' : ''); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala467913f9ff34913553be64599ec6e92)): ?>
<?php $attributes = $__attributesOriginala467913f9ff34913553be64599ec6e92; ?>
<?php unset($__attributesOriginala467913f9ff34913553be64599ec6e92); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala467913f9ff34913553be64599ec6e92)): ?>
<?php $component = $__componentOriginala467913f9ff34913553be64599ec6e92; ?>
<?php unset($__componentOriginala467913f9ff34913553be64599ec6e92); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginala467913f9ff34913553be64599ec6e92 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala467913f9ff34913553be64599ec6e92 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::select.index','data' => ['label' => ''.e(__('Go to Activity')).'','searchable' => true,'onchange' => 'window.moveMapToV2(this)']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flux::select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => ''.e(__('Go to Activity')).'','searchable' => true,'onchange' => 'window.moveMapToV2(this)']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <option value=""><?php echo e(__('Select an Activity...')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->activitiesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <option value="<?php echo e($activity->id); ?>" data-lat="<?php echo e($activity->latitude); ?>" data-lng="<?php echo e($activity->longitudes); ?>">
                                <?php echo e($activity->display_name); ?> <?php echo e(!$activity->latitude ? ' (No GPS)' : ''); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala467913f9ff34913553be64599ec6e92)): ?>
<?php $attributes = $__attributesOriginala467913f9ff34913553be64599ec6e92; ?>
<?php unset($__attributesOriginala467913f9ff34913553be64599ec6e92); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala467913f9ff34913553be64599ec6e92)): ?>
<?php $component = $__componentOriginala467913f9ff34913553be64599ec6e92; ?>
<?php unset($__componentOriginala467913f9ff34913553be64599ec6e92); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="rounded-2xl border-2 border-zinc-200 dark:border-zinc-700 shadow-lg overflow-hidden relative map-viewport-container" 
             wire:ignore>
            <div id="operations-map-full" style="height: 100%; width: 100%; z-index: 1;"></div>
        </div>

        
        <input type="hidden" id="camps-data" value="<?php echo e(json_encode($this->allCamps)); ?>">
        <input type="hidden" id="activities-data" value="<?php echo e(json_encode($this->allActivities)); ?>">

        <script>
            (function() {
                var map = null;
                var markerLayer = null;
                var heatmapLayer = null;
                var isHeatmapActive = false;
                var isPicking = false;
                var pendingId = null;
                var pendingName = '';
                var isDashboardMode = <?php echo \Illuminate\Support\Js::from($isDashboard)->toHtml() ?>;

                // Simple Classic Pin Shapes (SVG)
                var createPin = function(color) {
                    return L.divIcon({
                        className: '',
                        html: `
                            <div style="position: relative; width: 34px; height: 34px;">
                                <svg viewBox="0 0 384 512" style="width: 100%; height: 100%; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.4));">
                                    <path fill="${color}" d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0z"/>
                                    <circle fill="white" cx="192" cy="192" r="60"/>
                                </svg>
                            </div>`,
                        iconSize: [34, 44], iconAnchor: [17, 44], popupAnchor: [0, -44]
                    });
                };

                var campIcon = createPin('#3b82f6'); // Blue Pin
                var activityDefaultIcon = createPin('#ef4444'); // Red Pin

                function getSectorIcon(sectorId) {
                    var color = '#ef4444';
                    switch(parseInt(sectorId)) {
                        case 1: color = '#ef4444'; break;
                        case 2: color = '#10b981'; break;
                        case 31: color = '#0ea5e9'; break;
                        case 70: color = '#f59e0b'; break;
                        case 67: color = '#8b5cf6'; break;
                        case 55: color = '#ec4899'; break;
                    }
                    return createPin(color);
                }

                window.toggleHeatmap = function() {
                    isHeatmapActive = !isHeatmapActive;
                    var btn = document.getElementById('heatmap-toggle');
                    if (isHeatmapActive) {
                        if (markerLayer) map.removeLayer(markerLayer);
                        drawHeatmap();
                        if(btn) btn.style.backgroundColor = '#fee2e2';
                    } else {
                        if (heatmapLayer) map.removeLayer(heatmapLayer);
                        drawMarkers();
                        if(btn) btn.style.backgroundColor = '';
                    }
                };

                window.printMap = function() {
                    if (map && map.browserPrint) {
                        map.browserPrint.print();
                    } else {
                        window.print();
                    }
                };

                window.exportMapData = function() {
                    try {
                        var camps = JSON.parse(document.getElementById('camps-data').value || '[]');
                        var activities = JSON.parse(document.getElementById('activities-data').value || '[]');
                        var csvRows = [['Type', 'Name', 'Latitude', 'Longitude'].join(',')];
                        camps.forEach(c => { if(c.latitude) csvRows.push(['Camp', '"' + c.name + '"', c.latitude, c.longitudes].join(',')); });
                        activities.forEach(a => { if(a.latitude) csvRows.push(['Activity', '"' + a.name + '"', a.latitude, a.longitudes].join(',')); });
                        var csvContent = "\ufeff" + csvRows.join("\n");
                        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                        var url = URL.createObjectURL(blob);
                        var link = document.createElement("a");
                        link.setAttribute("href", url);
                        link.setAttribute("download", "map_operations_data_" + new Date().toISOString().slice(0,10) + ".csv");
                        link.click();
                    } catch(e) { console.error('Export Error:', e); }
                };

                function drawHeatmap() {
                    if (!map || typeof L.heatLayer === 'undefined') return;
                    if (heatmapLayer) map.removeLayer(heatmapLayer);
                    try {
                        var activities = JSON.parse(document.getElementById('activities-data').value || '[]');
                        var pts = [];
                        activities.forEach(a => { 
                            var lat = parseFloat(a.latitude), lng = parseFloat(a.longitudes);
                            if(!isNaN(lat) && !isNaN(lng)) pts.push([lat, lng, 0.9]); 
                        });
                        heatmapLayer = L.heatLayer(pts, { radius: 35, blur: 20, maxZoom: 10, gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'} }).addTo(map);
                    } catch(e) { console.error('Heatmap Error:', e); }
                }

                window.moveMapToV2 = function(selectEl) {
                    if (!map || !selectEl.value) return;
                    if (isHeatmapActive) window.toggleHeatmap();
                    var opt = selectEl.querySelector('option[value="' + selectEl.value + '"]');
                    var lat = parseFloat(opt.getAttribute('data-lat')), lng = parseFloat(opt.getAttribute('data-lng'));
                    if (!isNaN(lat) && !isNaN(lng)) {
                        map.setView([lat, lng], 18);
                        if (markerLayer && markerLayer.eachLayer) {
                            markerLayer.eachLayer(l => { 
                                if(l.getLatLng && Math.abs(l.getLatLng().lat - lat) < 0.0001) l.openPopup(); 
                            });
                        }
                    } else if (confirm('تحديد الموقع يدوياً؟')) {
                        isPicking = true; pendingId = selectEl.value; pendingName = opt.text;
                        document.getElementById('operations-map-full').style.cursor = 'crosshair';
                    }
                };

                function startMap() {
                    var container = document.getElementById('operations-map-full');
                    if (!container || typeof L === 'undefined') return;
                    if (container._leaflet_id && map) return;
                    if (container._leaflet_id && !map) { container._leaflet_id = null; }

                    try {
                        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                        var sat = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{maxZoom:20, subdomains:['mt0','mt1','mt2','mt3']});
                        map = L.map(container, { layers: [osm] }).setView(isDashboardMode ? [31.4, 34.35] : [31.3547, 34.3088], isDashboardMode ? 10 : 11);
                        L.control.layers({"OSM": osm, "Satellite": sat}).addTo(map);

                        if (!isDashboardMode && typeof L.Control.Measure !== 'undefined') {
                            new L.Control.Measure({ position:'topleft', primaryLengthUnit:'kilometers', secondaryLengthUnit: 'meters', primaryAreaUnit: 'sqmeters', activeColor: '#f43f5e', completedColor: '#10b981', localization: 'ar', captureZIndex: 10000 }).addTo(map);
                        }
                        if (typeof L.browserPrint !== 'undefined') { L.browserPrint({position:'topleft'}).addTo(map); }

                        map.on('click', function(e) {
                            if (isPicking && pendingId && confirm('اعتماد هذا الموقع لـ ' + pendingName + '؟')) {
                                window.Livewire.find('<?php echo e($_instance->getId()); ?>').updateActivityCoordinates(pendingId, e.latlng.lat, e.latlng.lng).then(() => {
                                    alert('تم الحفظ'); isPicking = false; 
                                    document.getElementById('operations-map-full').style.cursor = '';
                                });
                            }
                        });

                        drawMarkers();
                        setTimeout(() => map.invalidateSize(), 500);
                        window.refreshMapMarkers = drawMarkers;
                    } catch (e) { console.error('Map Init Error:', e); }
                }

                function drawMarkers() {
                    if (!map || isHeatmapActive) return;
                    if (markerLayer) map.removeLayer(markerLayer);
                    try {
                        markerLayer = (!isDashboardMode && typeof L.markerClusterGroup !== 'undefined') ? L.markerClusterGroup() : L.featureGroup();
                        
                        var campsInput = document.getElementById('camps-data');
                        var activitiesInput = document.getElementById('activities-data');
                        
                        var camps = JSON.parse(campsInput ? campsInput.value : '[]');
                        var activities = JSON.parse(activitiesInput ? activitiesInput.value : '[]');

                        console.log('Drawing Markers:', { camps: camps.length, activities: activities.length });

                        camps.forEach(c => {
                            try {
                                var lat = parseFloat(c.latitude), lng = parseFloat(c.longitudes);
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    var m = L.marker([lat, lng], {icon:campIcon}).bindPopup('⛺ ' + c.name);
                                    markerLayer.addLayer(m);
                                }
                            } catch(e) { console.warn('Skipping camp marker due to error', e); }
                        });

                        activities.forEach(a => {
                            try {
                                var lat = parseFloat(a.latitude), lng = parseFloat(a.longitudes);
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    var icon = isDashboardMode ? activityDefaultIcon : getSectorIcon(a.sector_id);
                                    var m = L.marker([lat, lng], {icon:icon}).bindPopup('📦 ' + a.name);
                                    markerLayer.addLayer(m);
                                }
                            } catch(e) { console.warn('Skipping activity marker due to error', e); }
                        });
                        map.addLayer(markerLayer);
                    } catch(e) { console.error('Marker Drawing Error:', e); }
                }

                setInterval(startMap, 1000);
                
                const initListener = () => {
                    if (window.Livewire) {
                        Livewire.on('refreshMarkers', () => { if (window.refreshMapMarkers) window.refreshMapMarkers(); });
                    }
                };
                if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initListener); } else { initListener(); }

                document.addEventListener('livewire:navigated', () => { 
                    if (map) { map.remove(); map = null; }
                    startMap(); 
                });
            })();
        </script>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/livewire/org-app/maps/operations-map.blade.php ENDPATH**/ ?>