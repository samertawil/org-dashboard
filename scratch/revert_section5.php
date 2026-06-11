<?php
$filePath = __DIR__ . '/../resources/views/livewire/org-app/reports/education-director-dashboard.blade.php';
$content = file_get_contents($filePath);

// Find the start of Section 5 (the comment block) 
$section5Start = strpos($content, "\n    <!-- ============================================================ -->\n    <!-- Section 5: Supervisor Reports");
if ($section5Start === false) {
    echo "ERROR: Section 5 start marker not found\n";
    exit(1);
}

// Find @assets (last occurrence, which comes after Section 5)
$assetsPos = strrpos($content, '    @assets');
if ($assetsPos === false) {
    echo "ERROR: @assets not found\n";
    exit(1);
}

$beforeSection5 = substr($content, 0, $section5Start);
$afterSection5  = "\n    @assets\n        <script src=\"https://cdn.jsdelivr.net/npm/apexcharts\"></script>\n    @endassets\n</div>\n";

$section5 = '
    <!-- ============================================================ -->
    <!-- Section 5: Supervisor Reports (التقارير المقدمة من المشرف) -->
    <!-- ============================================================ -->
    <div x-data="{
            modalOpen: false,
            modalReport: null,
            openModal(report) {
                this.modalReport = report;
                this.modalOpen = true;
            },
            closeModal() {
                this.modalOpen = false;
                this.modalReport = null;
            },
            get allAttachments() {
                if (!this.modalReport || !this.modalReport.bodies) return [];
                let atts = [];
                this.modalReport.bodies.forEach(b => {
                    if (b.parsed_attachments && b.parsed_attachments.length) {
                        atts = atts.concat(b.parsed_attachments);
                    }
                });
                return atts;
            }
        }"
        class="grid grid-cols-1 gap-6 relative">

        <flux:card class="p-6 overflow-hidden">
            <!-- Section Header + Filters -->
            <div class="flex flex-col gap-4 border-b pb-5 mb-6 dark:border-zinc-700">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400 shrink-0">
                        <flux:icon name="document-text" class="size-5" />
                    </div>
                    <div>
                        <flux:heading size="lg" class="font-bold">القسم الخامس: تقارير المشرفين</flux:heading>
                        <flux:subheading>التقارير المقدمة إلى الإدارة من المشرفين الميدانيين</flux:subheading>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="flex flex-wrap items-end gap-3 print:hidden">
                    <div class="flex flex-col gap-1 min-w-48">
                        <flux:label class="text-right text-xs">المركز التعليمي</flux:label>
                        <flux:select wire:model.live="reportSearchGroup" class="w-full text-right text-sm">
                            <option value="">-- كل المراكز --</option>
                            @foreach ($groups as $grp)
                                <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    <div class="flex flex-col gap-1 min-w-36">
                        <flux:label class="text-right text-xs">الدفعة</flux:label>
                        <flux:select wire:model.live="reportSearchBatch" class="w-full text-right text-sm">
                            <option value="">-- كل الدفعات --</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch }}">{{ $batch }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    @if ($reportSearchGroup !== \'\' || $reportSearchBatch !== \'\')
                        <flux:button wire:click="$set(\'reportSearchGroup\', \'\'); $set(\'reportSearchBatch\', \'\')"
                            variant="ghost" size="sm" icon="x-mark"
                            class="text-rose-600 hover:text-rose-700 dark:text-rose-400 self-end">
                            مسح
                        </flux:button>
                    @endif

                    <div class="flex items-end gap-1 mr-auto">
                        <span class="inline-flex items-center gap-1 text-xs bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2.5 py-1 rounded-full font-medium">
                            <flux:icon name="document-text" class="size-3.5" />
                            {{ $supervisorReports->count() }} تقرير
                        </span>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay for Section 5 -->
            <div wire:loading.delay wire:target="reportSearchGroup,reportSearchBatch"
                class="absolute inset-0 z-10 bg-white/60 dark:bg-zinc-800/60 backdrop-blur-sm flex items-center justify-center rounded-xl">
                <flux:icon name="arrow-path" class="size-8 animate-spin text-orange-500 dark:text-orange-400" />
            </div>

            @if ($supervisorReports->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="size-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                        <flux:icon name="document-text" class="size-8 text-zinc-400" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">لا توجد تقارير مطابقة للفلترة الحالية</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">جرّب تغيير فلتر المركز أو الدفعة</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse text-sm">
                        <thead>
                            <tr class="border-b-2 border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">#</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">اسم النشاط</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">المجال</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">المركز التعليمي</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300">المحتوى</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300">الملاحظة</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">تاريخ التقرير</th>
                                <th class="pb-3 pt-1 px-3 font-bold text-zinc-700 dark:text-zinc-300 text-center whitespace-nowrap">المرفقات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($supervisorReports as $report)
                                @php
                                    $firstBody = $report->bodies->first();
                                    $totalAtts = $report->bodies->sum(fn($b) => count($b->parsed_attachments));
                                    $domainColors = [
                                        \'التعليم\'            => \'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300\',
                                        \'الدعم النفسي\'       => \'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300\',
                                        \'مهارات وقيم تربوية\' => \'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300\',
                                    ];
                                    $domainClass = $domainColors[$report->domain_name] ?? \'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300\';
                                    $modalData = json_encode([
                                        \'id\'     => $report->id,
                                        \'name\'   => $report->report_name,
                                        \'date\'   => $report->report_date,
                                        \'bodies\' => $report->bodies->map(fn($b) => [
                                            \'content\'            => $b->content,
                                            \'observation\'        => $b->observation,
                                            \'parsed_attachments\' => $b->parsed_attachments,
                                        ])->values()->toArray(),
                                    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
                                @endphp
                                <tr class="hover:bg-orange-50/30 dark:hover:bg-orange-950/10 transition-colors">
                                    <td class="py-3 px-3 text-zinc-400 font-mono text-xs">{{ $report->id }}</td>

                                    <td class="py-3 px-3 font-medium text-zinc-900 dark:text-zinc-100 max-w-[200px]">
                                        <span class="line-clamp-2 leading-snug">{{ $report->activity_name ?? \'—\' }}</span>
                                    </td>

                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($report->domain_name)
                                            <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $domainClass }}">
                                                {{ $report->domain_name }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 max-w-[200px]">
                                        @forelse ($report->group_names as $gname)
                                            <span class="block text-xs text-zinc-700 dark:text-zinc-300 leading-snug line-clamp-2">{{ $gname }}</span>
                                        @empty
                                            <span class="text-zinc-400">—</span>
                                        @endforelse
                                    </td>

                                    <td class="py-3 px-3 max-w-[280px]">
                                        @if($firstBody && $firstBody->content)
                                            <p class="text-zinc-700 dark:text-zinc-300 text-xs leading-relaxed line-clamp-3">{{ $firstBody->content }}</p>
                                        @else
                                            <span class="text-zinc-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 max-w-[200px]">
                                        @if($firstBody && $firstBody->observation)
                                            <p class="text-zinc-600 dark:text-zinc-400 text-xs leading-relaxed line-clamp-2 italic">{{ $firstBody->observation }}</p>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">لا يوجد</span>
                                        @endif
                                    </td>

                                    <td class="py-3 px-3 whitespace-nowrap text-zinc-500 dark:text-zinc-400 text-xs">
                                        {{ \Carbon\Carbon::parse($report->report_date)->format(\'Y-m-d\') }}
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        @if($totalAtts > 0)
                                            <button @click="openModal({{ $modalData }})"
                                                class="inline-flex items-center gap-1 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 text-xs font-medium px-2.5 py-1 rounded-lg transition-colors cursor-pointer">
                                                <flux:icon name="photo" class="size-3.5" />
                                                {{ $totalAtts }}
                                            </button>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </flux:card>

        <!-- ===== Attachments Modal ===== -->
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeModal()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            style="display:none"
        >
            <div
                x-show="modalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col overflow-hidden"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="size-9 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                            <flux:icon name="photo" class="size-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 dark:text-zinc-100 text-base" x-text="modalReport?.name ?? \'\'"></h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="\'تاريخ التقرير: \' + (modalReport?.date ?? \'\')"></p>
                        </div>
                    </div>
                    <button @click="closeModal()"
                        class="size-8 rounded-lg flex items-center justify-center text-zinc-500 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto flex-1 p-6">
                    <template x-if="allAttachments.length === 0">
                        <div class="flex flex-col items-center justify-center py-12 text-zinc-400">
                            <flux:icon name="photo" class="size-12 mb-3 opacity-30" />
                            <p class="text-sm">لا توجد مرفقات</p>
                        </div>
                    </template>

                    <template x-if="allAttachments.length > 0">
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4" x-text="allAttachments.length + \' مرفق\'"></p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="(att, idx) in allAttachments" :key="idx">
                                    <div class="group relative rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 aspect-square">
                                        <template x-if="[\'jpg\',\'jpeg\',\'png\',\'gif\',\'webp\',\'svg\'].includes((att.extension ?? \'\').toLowerCase())">
                                            <a :href="\'/storage/\' + att.path" target="_blank" rel="noopener" class="block w-full h-full">
                                                <img :src="\'/storage/\' + att.path" :alt="att.name"
                                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <div class="bg-white/90 rounded-full p-2">
                                                        <svg class="size-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </a>
                                        </template>
                                        <template x-if="![\'jpg\',\'jpeg\',\'png\',\'gif\',\'webp\',\'svg\'].includes((att.extension ?? \'\').toLowerCase())">
                                            <a :href="\'/storage/\' + att.path" target="_blank" rel="noopener"
                                                class="flex flex-col items-center justify-center h-full gap-2 text-zinc-500 hover:text-indigo-600 transition-colors p-3">
                                                <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-xs text-center break-all line-clamp-2" x-text="att.name"></span>
                                                <span class="text-xs font-mono uppercase bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.5 rounded" x-text="att.extension"></span>
                                            </a>
                                        </template>
                                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs px-2 py-1 translate-y-full group-hover:translate-y-0 transition-transform duration-200 truncate"
                                            x-text="att.name"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
';

$newContent = $beforeSection5 . $section5 . $afterSection5;
file_put_contents($filePath, $newContent);
echo "SUCCESS: Section 5 reverted to original.\n";
echo "New file size: " . strlen($newContent) . " bytes\n";
echo "New line count: " . substr_count($newContent, "\n") . "\n";
