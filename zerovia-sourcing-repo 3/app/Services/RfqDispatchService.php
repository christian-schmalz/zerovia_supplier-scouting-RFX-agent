<?php

namespace App\Services;

use App\Mail\RfqMail;
use App\Models\RfqDocument;
use App\Models\RfqRecipient;
use Illuminate\Support\Facades\Mail;

class RfqDispatchService
{
    public function dispatch(RfqDocument $doc, string $introText = ''): int
    {
        $sent = 0;

        $doc->recipients()
            ->whereNull('sent_at')
            ->with('supplier')
            ->each(function (RfqRecipient $recipient) use ($doc, $introText, &$sent) {
                Mail::to($recipient->email)
                    ->queue(new RfqMail($doc, $recipient, $introText));

                $recipient->update(['sent_at' => now()]);
                $sent++;
            });

        if ($sent > 0 && $doc->sent_at === null) {
            $doc->update(['sent_at' => now()]);
        }

        return $sent;
    }

    public function markOpened(RfqRecipient $recipient): void
    {
        if ($recipient->opened_at === null) {
            $recipient->update(['opened_at' => now()]);
        }
    }
}
