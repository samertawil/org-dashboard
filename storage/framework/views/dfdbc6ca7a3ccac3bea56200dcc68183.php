<?php
    use App\Helpers\Arabic;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Activity Report - <?php echo e(Arabic::reshape($activity->name)); ?></title>
    <style>
        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: normal;
            src: url("<?php echo e(public_path('fonts/Amiri-Regular.ttf')); ?>") format('truetype');
        }
        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: bold;
            src: url("<?php echo e(public_path('fonts/Amiri-Bold.ttf')); ?>") format('truetype');
        }

        @font-face {
            font-family: 'Cairo';
            font-style: normal;
            font-weight: normal;
            src: url("<?php echo e(public_path('fonts/cairo_normal.ttf')); ?>") format('truetype');
        }

        @font-face {
            font-family: 'Cairo';
            font-style: normal;
            font-weight: bold;
            src: url("<?php echo e(public_path('fonts/cairo_bold.ttf')); ?>") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans','Cairo', 'Amiri', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: bottom;
        }
        .header-right {
            text-align: right;
        }
        h1 {
            font-size: 18px;
            text-transform: uppercase;
            margin: 0;
            color: #111;
        }
        .meta {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .label {
            font-weight: bold;
            color: #555;
            font-size: 10px;
            display: block;
        }
        .value {
            font-size: 12px;
            color: #000;
            display: block;
            margin-bottom: 10px;
        }
        .grid-2 {
            width: 100%;
            display: table;
        }
        .col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            background: #eee;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <h1>Activity Report</h1>
                <div class="meta">Generated: <?php echo e(date('Y-m-d H:i')); ?></div>
            </div>
            <div class="header-right">
                <strong><?php echo e(config('app.name')); ?></strong><br>
                Organization Dashboard
            </div>
        </div>
    </div>

    <!-- Overview -->
    <div class="section">
        <div class="section-title">Activity Overview</div>
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;">
                    <span class="label">Activity Name</span>
                    <span class="value"><?php echo e(Arabic::reshape($activity->name)); ?></span>
                </td>
                <td style="border: none; width: 50%;">
                     <span class="label">Status</span>
                     <span class="badge"><?php echo e(Arabic::reshape($activity->status_info['name'])); ?></span>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">
                    <span class="label">Duration</span>
                    <span class="value"><?php echo e($activity->start_date); ?> &rarr; <?php echo e($activity->end_date ?? 'Ongoing'); ?></span>
                </td>
                <td style="border: none;">
                    <span class="label">Total Cost</span>
                    <span class="value">$<?php echo e(number_format($activity->cost, 2)); ?> USD (<?php echo e(number_format($activity->cost_nis, 2)); ?> NIS)</span>
                </td>
            </tr>
             <tr style="border: none;">
                <td style="border: none;">
                    <span class="label">Rating</span>
                    <span class="value"><?php echo e($activity->rating_info['rating']); ?> / 5</span>
                </td>
                <td style="border: none;"></td>
            </tr>
        </table>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->description): ?>
            <div style="margin-top: 10px;">
                <span class="label">Description</span>
                <div style="font-size: 11px; color: #444; margin-top: 4px;"><?php echo e(Arabic::reshape($activity->description)); ?></div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Location -->
    <div class="section">
        <div class="section-title">Location Details</div>
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;">
                    <span class="label">Region</span>
                    <span class="value"><?php echo e(Arabic::reshape($activity->regions->region_name ?? '-')); ?></span>
                </td>
                <td style="border: none; width: 50%;">
                    <span class="label">City</span>
                    <span class="value"><?php echo e(Arabic::reshape($activity->cities->city_name ?? '-')); ?></span>
                </td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">
                    <span class="label">Neighborhood</span>
                    <span class="value"><?php echo e(Arabic::reshape($activity->activityNeighbourhood->neighbourhood_name ?? '-')); ?></span>
                </td>
                <td style="border: none;">
                    <span class="label">Specific Location</span>
                    <span class="value"><?php echo e(Arabic::reshape($activity->activityLocation->location_name ?? '-')); ?></span>
                </td>
            </tr>
        </table>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->address_details): ?>
            <div style="margin-top: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #eee;">
                <span class="label">Detailed Address:</span>
                <span style="font-size: 11px;"><?php echo e(Arabic::reshape($activity->address_details)); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Financials - Parcels -->
    <div class="section">
        <div class="section-title">Parcels Distribution</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->parcels->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Parcel Type</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activity->parcels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parcel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td>
                            <?php echo e(Arabic::reshape($parcel->parcelType->status_name ?? '-')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($parcel->notes): ?><br><small style="color: #666;"><?php echo e(Arabic::reshape($parcel->notes)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><?php echo e($parcel->distributed_parcels_count); ?></td>
                        <td>$<?php echo e(number_format($parcel->cost_for_each_parcel, 2)); ?></td>
                        <td>$<?php echo e(number_format($parcel->distributed_parcels_count * $parcel->cost_for_each_parcel, 2)); ?></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="font-style: italic; color: #777;">No parcels data.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Financials - Beneficiaries -->
    <div class="section">
        <div class="section-title">Beneficiaries Impact</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->beneficiaries->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Count</th>
                        <th>Cost/Unit</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activity->beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td>
                            <?php echo e(Arabic::reshape($beneficiary->beneficiaryType->status_name ?? '-')); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($beneficiary->notes): ?><br><small style="color: #666;"><?php echo e(Arabic::reshape($beneficiary->notes)); ?></small><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><?php echo e($beneficiary->beneficiaries_count); ?></td>
                        <td>$<?php echo e(number_format($beneficiary->cost_for_each_beneficiary, 2)); ?></td>
                        <td>$<?php echo e(number_format($beneficiary->beneficiaries_count * $beneficiary->cost_for_each_beneficiary, 2)); ?></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="font-style: italic; color: #777;">No beneficiaries data.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Teams -->
    <div class="section">
        <div class="section-title">Assigned Work Teams</div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->workTeams->count() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activity->workTeams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e(Arabic::reshape($team->employeeRel->full_name ?? '-')); ?></td>
                        <td><?php echo e(Arabic::reshape($team->missionTitle->status_name ?? 'Member')); ?></td>
                        <td><?php echo e(Arabic::reshape($team->notes ?? '-')); ?></td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="font-style: italic; color: #777;">No teams assigned.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <?php echo e(config('app.name')); ?> - Automated Activity Report - Page <span class="page-number"></span>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/livewire/org-app/activity/pdf.blade.php ENDPATH**/ ?>