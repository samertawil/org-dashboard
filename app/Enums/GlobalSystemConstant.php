<?php

namespace App\Enums;

enum GlobalSystemConstant: int
{ 
    case INACTIVE = 0;
    case ACTIVE = 1;
   
    case MALE = 2;
    case FEMALE = 3;
   

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::MALE => 'Male',
            self::FEMALE => 'Female',
           
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'check-circle',
            self::INACTIVE => 'times-circle',
            self::MALE => '👨',
            self::FEMALE => '👩',
           
        };
    }
    public static function options(): \Illuminate\Support\Collection 
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'icon' => $case->icon(),
            'type' => $case->getType(), // إضافة نوع الثابت
        ]);
    }
    public function getType()
    {

        return match ($this) {
            self::ACTIVE, self::INACTIVE  =>  'status',
            self::MALE, self::FEMALE  =>  'gender',
           
        };
    }
}
