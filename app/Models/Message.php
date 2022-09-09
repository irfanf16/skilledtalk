<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'connection_id',
        'send_from',
        'send_to',
        'type',
        'text',
        'meeting_id'
    ];

    public function sender(){
        return $this->belongsTo(User::class, 'send_from', 'id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'send_to', 'id');
    }

    public function consultation(){
        return $this->hasOne(Consultation::class, 'meeting_id', 'meeting_id');
    }

}
