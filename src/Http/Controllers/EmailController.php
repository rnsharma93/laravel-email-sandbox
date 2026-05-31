<?php
namespace Ram\EmailSandbox\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Ram\EmailSandbox\Models\EmailMessage;

class EmailController extends Controller
{
    public function index(Request $request)
    {
        $emails = EmailMessage::query()
            ->when($request->search, function($q, $search) {
                $q->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('text_body', 'like', "%{$search}%")
                      ->orWhere('html_body', 'like', "%{$search}%");
                });
            })
            ->when($request->from, fn($q, $from) => $q->where('from', 'like', "%{$from}%"))
            ->when($request->to, fn($q, $to) => $q->where('to', 'like', "%{$to}%"))
            ->when($request->date, fn($q, $date) => $q->whereDate('created_at', $date))
            ->when($request->date_from, fn($q, $dateFrom) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($request->date_to, fn($q, $dateTo) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('email-sandbox::emails.index', compact('emails'));
    }

    public function show($id)
    {
        $email = EmailMessage::findOrFail($id);
        return view('email-sandbox::emails.show', compact('email'));
    }

    public function download($id, $file)
    {
        $email = EmailMessage::findOrFail($id);
        if (!in_array($file, $email->attachments ?? [])) {
            abort(404);
        }

        $path = config('email-sandbox.storage_path').'/'.$file;
        abort_unless(is_file($path), 404);

        // Strip the random prefix added at capture time for a clean download name.
        $original = preg_replace('/^[a-zA-Z0-9]+_/', '', $file);

        return response()->download($path, $original);
    }

    public function destroy($id)
    {
        EmailMessage::findOrFail($id)->delete();
        return redirect()->route('email-sandbox.index');
    }

    public function destroyAll()
    {
        // Chunk-delete so the model's "deleting" event fires for each record
        // and the associated .eml / attachment files are cleaned up.
        EmailMessage::query()->chunkById(200, function ($emails) {
            foreach ($emails as $email) {
                $email->delete();
            }
        });

        return redirect()->route('email-sandbox.index');
    }
}
