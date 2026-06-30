<?php

namespace App\Http\Controllers\Api\Treasurer;

use App\Http\Controllers\Api\BaseController;
use App\Models\Disbursement;
use Illuminate\Http\Request;

class DisbursementController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Disbursement::with([
            'application:id,registration_number',
            'application.user:id,name',
            'scholarship:id,name',
        ]);

        if ($request->filled('filter.scholarship_id')) {
            $query->where('scholarship_id', $request->input('filter.scholarship_id'));
        }

        if ($request->filled('filter.status')) {
            $query->where('status', $request->input('filter.status'));
        }

        return $this->success(
            $query->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15))
                ->through(fn ($d) => [
                    'id' => $d->id,
                    'scholarship' => $d->scholarship?->name,
                    'applicant' => $d->application?->user?->name,
                    'registration_number' => $d->application?->registration_number,
                    'bank_name' => $d->bank_name,
                    'account_holder_name' => $d->account_holder_name,
                    'amount' => $d->amount,
                    'status' => $d->status,
                    'disbursed_at' => $d->disbursed_at?->format('Y-m-d H:i:s'),
                ])
        );
    }

    public function show(Disbursement $disbursement): \Illuminate\Http\JsonResponse
    {
        $disbursement->load([
            'application:id,registration_number',
            'application.user:id,name',
            'scholarship:id,name',
        ]);

        return $this->success($disbursement->toArray());
    }

    public function update(Disbursement $disbursement, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:processing,disbursed,failed'],
            'notes' => ['nullable', 'string'],
        ]);

        $data = ['status' => $request->status, 'notes' => $request->notes];

        if ($request->status === 'disbursed') {
            $data['disbursed_at'] = now();
            $data['processed_by'] = $request->user()->id;
        }

        $disbursement->update($data);

        return $this->success($disbursement, 'Status pencairan berhasil diperbarui.');
    }
}
