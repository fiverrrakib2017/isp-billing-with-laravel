<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    public function department(){
        return $this->belongsTo(Department::class,'department_id','id');
    }
    public function designation(){
        return $this->belongsTo(Designation::class,'designation_id','id');
    }
}
