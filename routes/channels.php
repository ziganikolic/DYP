<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('tournament.{code}', function ($user, $code) {
    return true; // Public tournament channel
});
