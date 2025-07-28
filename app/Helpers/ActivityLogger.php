<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log($action, $details = [])
    {
        $user = auth()->check() ? auth()->user() : null;

        ActivityLog::create([
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'action' => $action,
            'details' => json_encode($details, JSON_UNESCAPED_UNICODE)
        ]);
    }


}
