<?php

namespace App\Exports;

 
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseRequestExport implements FromCollection, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return [
            __('Request Number'),
            __('Request Date'),
            __('Description'),
            __('Justification'),
            __('Suggested Vendors'),
            __('Quotation Deadline'),
            __('Budget Details'),
            __('Total Dollar'),
            __('Total NIS'),
            __('Order Count'),
            __('Status'),
            __('Created By'),
        ];
    }

    public function map($pr): array
    {
        $vendors = $pr->suggested_vendors->pluck('name')->implode(', ');
        
        return [
            $pr->request_number,
            $pr->request_date ? $pr->request_date->format('Y-m-d') : '',
            $pr->description,
            $pr->justification,
            $vendors,
            $pr->quotation_deadline ? $pr->quotation_deadline->format('Y-m-d') : '',
            $pr->budget_details,
            $pr->estimated_total_dollar,
            $pr->estimated_total_nis,
            $pr->order_count,
            $pr->status->status_name ?? '',
            $pr->creator->name ?? '',
        ];
    }
}
