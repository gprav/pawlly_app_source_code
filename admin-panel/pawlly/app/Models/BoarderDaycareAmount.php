<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class BoarderDaycareAmount extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'boarder_daycare_perdayamount';
    protected $fillable = ['user_id', 'amount', 'type'];
    protected $casts = [
        'user_id' => 'integer',
        'amount' => 'double',
      ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
