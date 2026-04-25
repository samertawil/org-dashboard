<?php

namespace App\Livewire\OrgApp\PurchaseRequest;

use App\Mail\SendQuotationMail;
use App\Models\PartnerInstitution;
use App\Models\PurchaseQuotationPrice;
use App\Models\PurchaseQuotationResponse;
use App\Models\PurchaseRequisition;
use App\Models\Status;
use App\Reposotries\StatusRepo;
use App\Services\UploadingFilesServices;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
#[Title('تقديم عرض سعر')]
class PublicQuotation extends Component
{
    use WithFileUploads;

    public $token;
    public $vendor_id;
    public $pr;
    public $vendor;

    public $prices = []; // [item_id => price]
    public $item_notes = []; // [item_id => notes]
    public $general_notes;
    public $attachments = [];
    public $currency_id;
    public $input_pin; // الحقل الذي يدخله المستخدم
    
    public $is_submitted = false;
    public $is_expired = false;
    public $show_history = false; 
    public $is_history_verified = false; // هل تم التحقق لرؤية السجل؟
    public $previousOffers = [];
    public $history_pin = ''; // كود الـ PIN المدخل للسجل

    public function mount($token, $vendor_id)
    {
        $this->token = $token;
        $this->pr = PurchaseRequisition::with('items.unit')->where('token', $token)->firstOrFail();
        $this->vendor_id = $vendor_id;
        $this->vendor = PartnerInstitution::findOrFail($vendor_id);

        // التحقق من تاريخ انتهاء الصلاحية
        if ($this->pr->quotation_deadline && now()->gt($this->pr->quotation_deadline)) {
            $this->is_expired = true;
        }

        // جلب العروض السابقة لهذا المورد لهذا الطلب
        $this->previousOffers = \App\Models\PurchaseQuotationResponse::with('currency')
            ->where('purchase_requisition_id', $this->pr->id)
            ->where('vendor_id', $this->vendor_id)
            ->orderBy('submitted_at', 'desc')
            ->get();

        if ($this->previousOffers->count() > 0) {
            $this->show_history = true;
        }

        // Initialize prices
        foreach ($this->pr->items as $item) {
            $this->prices[$item->id] = '';
            $this->item_notes[$item->id] = '';
        }

        // Default currency (example: NIS or USD)
        // You might want to fetch this from statuses
        $this->currency_id = StatusRepo::statuses()->where('p_id_sub', config('appConstant.currencies', 0))->first()?->id;
    }

    public function startNewOffer()
    {
        $this->show_history = false;
    }

    public function verifyHistoryPin()
    {
        $expectedPin = $this->pr->calculateVendorPin($this->vendor_id);

        if ($this->history_pin == $expectedPin) {
            $this->is_history_verified = true;
            $this->pin = $this->history_pin; // تعبئة الـ PIN التلقائي للنموذج أيضاً
        } else {
            $this->addError('history_pin', 'كود التحقق غير صحيح، يرجى التأكد من الكود المرسل إليكم.');
        }
    }

    public function downloadOffer($offerId)
    {
        $offer = \App\Models\PurchaseQuotationResponse::with(['vendor', 'purchaseRequisition', 'prices.requisitionItem', 'currency'])
            ->findOrFail($offerId);

        $pdf = Pdf::loadView('pdf.quotation', ['quotation' => $offer]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'quotation-' . $offer->id . '.pdf');
    }

    public function submit()
    {
        if ($this->is_expired) {
            return;
        }

        $expectedPin = $this->pr->calculateVendorPin($this->vendor_id);
        
        $this->validate([
            'input_pin' => [
                'required',
                function ($attribute, $value, $fail) use ($expectedPin) {
                    if ($value !== $expectedPin) {
                        $fail('كود التحقق غير صحيح. يرجى التأكد من الكود المرسل إليكم عبر الواتساب.');
                    }
                },
            ],
            'prices.*' => 'required|numeric|min:0',
            'attachments.*' => 'nullable|file|max:10240', // 10MB
        ]);

        $uploadedPaths = [];
        if ($this->attachments) {
            foreach ($this->attachments as $file) {
                $path = UploadingFilesServices::uploadAndCompress($file, 'quotations/' . $this->pr->id, 'public',1);
                $uploadedPaths[] = $path;
            }
        }

        $total = 0;
        foreach ($this->prices as $item_id => $price) {
            $item = $this->pr->items->firstWhere('id', $item_id);
            if ($item) {
                $total += $price * $item->quantity;
            }
        }

        $response = PurchaseQuotationResponse::create([
            'purchase_requisition_id' => $this->pr->id,
            'vendor_id' => $this->vendor_id,
            'total_amount' => $total,
            'currency_id' => $this->currency_id,
            'status_id' => null, // Pending
            'notes' => $this->general_notes,
            'attachments' => $uploadedPaths,
            'submitted_at' => now(),
        ]);

        foreach ($this->prices as $item_id => $price) {
            PurchaseQuotationPrice::create([
                'quotation_response_id' => $response->id,
                'purchase_requisition_item_id' => $item_id,
                'offered_price' => $price,
                'vendor_item_notes' => $this->item_notes[$item_id] ?? '',
            ]);
        }
        $data = [
            'name' => $this->vendor->name,
            'subject' =>'تم استلام عرض سعر جديد لطلب الشراء رقم ' . $this->pr->id,
            'notes' => $response->notes,
            'link'=> 'يمكنك الاطلاع على التفاصيل من خلال الرابط أدناه'."https://app.afscgaza.org/dashboard/quotations/$response->id"
       ];
      
        try {
            Mail::to('eng.samertawil@gmail.com')->send(new SendQuotationMail($data));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        $this->is_submitted = true;
        session()->flash('message', 'تم إرسال عرض السعر بنجاح. شكراً لكم!');
    }

    public function render()
    {
        $currencies = StatusRepo::statuses()->where('p_id_sub', config('appConstant.currencies', 0));
        return view('livewire.org-app.purchase-request.public-quotation', [
            'currencies' => $currencies
        ]);
    }
}
