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

    protected static function booted(): void
    {
        // Clean up the raw .eml snapshot and any stored attachments
        // whenever a record is deleted (single or bulk via the model).
        static::deleting(function (EmailMessage $email) {
            $email->deleteStoredFiles();
        });
    }

    /**
     * Remove the raw .eml file and attachment files associated with this email.
     */
    public function deleteStoredFiles(): void
    {
        $storagePath = config('email-sandbox.storage_path');

        $files = [$storagePath.'/'.$this->id.'.eml'];

        foreach ((array) $this->attachments as $attachment) {
            $files[] = $storagePath.'/'.$attachment;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
