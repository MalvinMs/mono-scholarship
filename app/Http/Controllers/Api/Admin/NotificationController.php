<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function show(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        return $this->success([
            'notification_channels' => $scholarship->notification_channels,
            'notification_templates' => $scholarship->notification_templates,
        ]);
    }

    public function update(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'notification_channels' => ['sometimes', 'array'],
            'notification_channels.*' => ['string', 'in:whatsapp,email'],
            'notification_templates' => ['sometimes', 'array'],
        ]);

        $scholarship->update($request->only(['notification_channels', 'notification_templates']));

        return $this->success([
            'notification_channels' => $scholarship->notification_channels,
            'notification_templates' => $scholarship->notification_templates,
        ], 'Konfigurasi notifikasi berhasil disimpan.');
    }

    public function test(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'channel' => ['required', 'string', 'in:whatsapp,email'],
            'event_type' => ['required', 'string'],
        ]);

        \App\Jobs\SendNotification::dispatch(
            $request->user(),
            null,
            $scholarship,
            $request->channel,
            $request->event_type,
        );

        return $this->success(null, 'Notifikasi uji coba telah dikirim.');
    }
}
