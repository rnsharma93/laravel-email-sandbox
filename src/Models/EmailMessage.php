<?php
namespace Ram\EmailSandbox\Models;

use Illuminate\Database\Eloquent\Model;

class EmailMessage extends Model
{
    protected $guarded = [];
    protected $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'headers' => 'array',
        'attachments' => 'array',
    ];
}
