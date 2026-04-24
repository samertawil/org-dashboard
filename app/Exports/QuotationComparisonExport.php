<?php

namespace App\Exports;

use App\Models\PurchaseRequisition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuotationComparisonExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $pr;

    public function __construct(PurchaseRequisition $pr)
    {
        $this->pr = $pr->load(['items.unit', 'quotations.vendor', 'quotations.prices', 'quotations.currency']);
    }

    public function collection()
    {
        $data = collect();
        $quotations = $this->pr->quotations;

        // Row for Winning Vendor Info
        $winner = $quotations->where('status_id', 1)->first();
        if ($winner) {
            $data->push(['الشركة الفائزة بالترسية:', $winner->vendor->name, '', '', '', '']);
            $data->push(['', '', '', '', '', '']); // Empty spacer
        }

        foreach ($this->pr->items as $item) {
            $row = [
                'item_name' => $item->item_name,
                'quantity' => $item->quantity . ' ' . ($item->unit->status_name ?? ''),
            ];

            foreach ($quotations as $quote) {
                $priceRecord = $quote->prices->where('purchase_requisition_item_id', $item->id)->first();
                $price = $priceRecord?->offered_price;
                
                $row['vendor_p_' . $quote->id] = $price ? number_format($price, 2) : '-';
                $row['vendor_c_' . $quote->id] = $quote->currency->status_name ?? '';
                $row['vendor_n_' . $quote->id] = $priceRecord?->vendor_item_notes ?? '-';
            }

            $data->push($row);
        }

        // Add Total Row
        $totalRow = [
            'item_name' => 'المجموع الإجمالي النهائي',
            'quantity' => '',
        ];
        foreach ($quotations as $quote) {
            $totalRow['vendor_p_' . $quote->id] = number_format($quote->total_amount, 2);
            $totalRow['vendor_c_' . $quote->id] = $quote->currency->status_name ?? '';
            $totalRow['vendor_n_' . $quote->id] = ''; 
        }
        $data->push($totalRow);

        return $data;
    }

    public function headings(): array
    {
        $headings = ['اسم الصنف', 'الكمية المطلوبة'];
        foreach ($this->pr->quotations as $quote) {
            $headings[] = 'سعر: ' . $quote->vendor->name;
            $headings[] = 'عملة: ' . $quote->vendor->name;
            $headings[] = 'ملاحظات: ' . $quote->vendor->name;
        }
        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $quotations = $this->pr->quotations;
        $winner = $quotations->where('status_id', 1)->first();
        $headerRow = $winner ? 3 : 1;
        
        $styles = [
            $headerRow => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2E8F0']]],
            'B' . $headerRow => ['fill' => ['fillType' => 'none'], 'font' => ['bold' => true, 'color' => ['rgb' => '000000']]], 
            $sheet->getHighestRow() => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F0F9FF']]],
            'A1' => ['font' => ['bold' => true]], 
        ];

        // تلوين أعمدة الشركة الفائزة باللون الأخضر في الهيدر
        if ($winner) {
            $styles['B1'] = ['font' => ['bold' => true, 'color' => ['rgb' => '16A34A']]]; // تلوين اسم الفائز فقط إذا وجد
            
            $winnerIndex = 0;
            foreach ($quotations as $index => $quote) {
                if ($quote->id === $winner->id) {
                    $winnerIndex = $index;
                    break;
                }
            }

            // حساب أرقام الأعمدة (كل مورد له 3 أعمدة تبدأ من العمود الثالث C)
            $startCol = 3 + ($winnerIndex * 3); // 1=A, 2=B, 3=C...
            
            for ($i = 0; $i < 3; $i++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol + $i);
                $styles[$colLetter . $headerRow] = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '16A34A']]
                ];
            }
        }

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}
