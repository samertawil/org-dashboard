<?php
    use App\Helpers\Arabic;
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo e(Arabic::reshape('عرض سعر مالي')); ?></title>
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
            font-family: 'DejaVu Sans', 'Cairo', 'Amiri', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            direction: rtl;
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
            margin: 0;
            color: #111;
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
            padding: 8px;
            text-align: right; /* تم ضبطها لليمين */
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        .label {
            font-weight: bold;
            color: #555;
            font-size: 11px;
        }
        .value {
            font-size: 12px;
            color: #000;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
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
                <h1><?php echo e(Arabic::reshape('عرض سعر مالي')); ?></h1>
                <div style="font-size: 10px; color: #666; margin-top: 5px;">
                    <?php echo e(Arabic::reshape('تاريخ الاستخراج:')); ?> <?php echo e(date('Y-m-d H:i')); ?>

                </div>
            </div>
            <div class="header-right">
                <strong><?php echo e(Arabic::reshape(config('app.name'))); ?></strong><br>
                <?php echo e(Arabic::reshape('نظام إدارة المشتريات')); ?>

            </div>
        </div>
    </div>

    <!-- معلومات الطلب والمورد -->
    <div class="section">
        <div class="section-title"><?php echo e(Arabic::reshape('معلومات العرض')); ?></div>
        <table>
            <tr>
                <td style="width: 50%; border: none;">
                    <span class="label"><?php echo e(Arabic::reshape('اسم المورد:')); ?></span>
                    <span class="value"><?php echo e(Arabic::reshape($quotation->vendor->name)); ?></span>
                </td>
                <td style="width: 50%; border: none;">
                    <span class="label"><?php echo e(Arabic::reshape('رقم طلب الشراء:')); ?></span>
                    <span class="value">#<?php echo e($quotation->purchaseRequisition->request_number); ?></span>
                </td>
            </tr>
            <tr>
                <td style="border: none;">
                    <span class="label"><?php echo e(Arabic::reshape('تاريخ التقديم:')); ?></span>
                    <span class="value"><?php echo e($quotation->submitted_at->format('Y-m-d H:i')); ?></span>
                </td>
                <td style="border: none;">
                    <span class="label"><?php echo e(Arabic::reshape('العملة:')); ?></span>
                    <span class="value"><?php echo e(Arabic::reshape($quotation->currency->status_name ?? '-')); ?></span>
                </td>
            </tr>
        </table>
    </div>

    <!-- جدول الأصناف -->
    <div class="section">
        <div class="section-title"><?php echo e(Arabic::reshape('تفاصيل الأسعار المقدمة')); ?></div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;"><?php echo e(Arabic::reshape('الصنف')); ?></th>
                    <th><?php echo e(Arabic::reshape('الكمية')); ?></th>
                    <th><?php echo e(Arabic::reshape('سعر الوحدة')); ?></th>
                    <th><?php echo e(Arabic::reshape('الإجمالي')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $quotation->prices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <tr>
                    <td>
                        <?php echo e(Arabic::reshape($price->requisitionItem->item_name)); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($price->vendor_item_notes): ?>
                            <br><small style="color: #666;"><?php echo e(Arabic::reshape('ملاحظة: ' . $price->vendor_item_notes)); ?></small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td style="text-align: center;"><?php echo e($price->requisitionItem->quantity); ?></td>
                    <td style="text-align: center;"><?php echo e(number_format($price->offered_price, 2)); ?></td>
                    <td style="text-align: center;"><?php echo e(number_format($price->offered_price * $price->requisitionItem->quantity, 2)); ?></td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="3" style="text-align: left; padding-left: 20px;"><?php echo e(Arabic::reshape('المجموع الإجمالي للعرض:')); ?></td>
                    <td style="text-align: center; color: #d9534f;"><?php echo e(number_format($quotation->total_amount, 2)); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quotation->notes): ?>
        <div class="section">
            <div class="section-title"><?php echo e(Arabic::reshape('ملاحظات المورد العامة')); ?></div>
            <div style="padding: 10px; background: #f9f9f9; border: 1px solid #eee;">
                <?php echo e(Arabic::reshape($quotation->notes)); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="footer">
        <?php echo e(Arabic::reshape(config('app.name'))); ?> - <?php echo e(Arabic::reshape('تقرير عرض سعر آلي')); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/pdf/quotation.blade.php ENDPATH**/ ?>