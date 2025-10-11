<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoanCaseKivNotes extends Model
{
    use HasFactory;

    protected $table = 'loan_case_kiv_notes';
    protected $primaryKey = 'id';

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            // ... code here
        });

        self::created(function($model){
            static::getLatestNote($model->case_id);
        });

        self::updating(function($model){
        });

        self::updated(function($model){
            static::getLatestNote($model->case_id);
        });

        self::deleting(function($model){
            // ... code here
        });

        self::deleted(function($model){
            static::getLatestNote($model->case_id);
            
        });
    }

    public static function getLatestNote($case_id)
    {
        $latest_note = LoanCaseKivNotes::where('case_id', $case_id)->where('status',1)->orderBy('created_at', 'desc')->take(1)->first();

        if($latest_note)
        {
            LoanCase::where('id', $case_id)->update(['latest_notes' => $latest_note->notes]);
        }
    }
}
