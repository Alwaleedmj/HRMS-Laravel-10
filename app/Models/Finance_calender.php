<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_calender extends Model
{
    use HasFactory;

    protected $table = "finance_calender";
    protected $fillable = ['FINANCE_YR', 'FINANCE_YR_DESC', 'start_date', 'end_date', 'is_open', 'company_code', 'added_by', 'created_at', 'updated_at'] ;

    public function added(){
        return $this->belongsTo('\App\Models\Admin','added_by','id');
    }
    public function updated_by(){
        return $this->belongsTo('\App\Models\Admin','updated_by','id');
    }
}
