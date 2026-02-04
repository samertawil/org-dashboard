<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'identity_number',
        'full_name',
        'birth_date',
        'student_groups_id',
        'gender',
        'activation',
        'status_id',
        'parent_phone',
        'living_parent_id',
        'notes',
        'added_type',
        'enrollment_type',
        'created_by',
        'updated_by',
    ];

    public static function maxBirthDate()
    {
        $from = new DateTime();
        $from->modify(config('malRules.allowDate1.fromDate'));
        $from =  $from->format('Y-m-d');  // birthdate not grater than $from date  "6 years old"

        return $from;
    }

    public static function minBirthDate()
    {
        $to = new DateTime();
        $x =  $to->modify(config('malRules.allowDate1.toDate'));
        //    $to=$x->modify(config('malRules.allowDate1.plus_date'));
        $to =  $to->format('Y-m-d');   // birthdate not less than $from date  "6 years old"
        return $to;
    }




    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function livingParent()
    {
        return $this->belongsTo(Status::class, 'living_parent_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(FeedBack::class);
    }
}
