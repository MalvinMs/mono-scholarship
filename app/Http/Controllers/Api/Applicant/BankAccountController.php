<?php

namespace App\Http\Controllers\Api\Applicant;

use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\Disbursement;
use Illuminate\Http\Request;

class BankAccountController extends BaseController
{
    public function update(Application $application, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return $this->error('Tidak memiliki akses.', 403);
        }

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder_name' => ['required', 'string', 'max:255'],
        ]);

        $disbursement = Disbursement::updateOrCreate(
            ['application_id' => $application->id],
            [
                'scholarship_id' => $application->scholarship_id,
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_holder_name' => $validated['account_holder_name'],
                'amount' => $application->scholarship?->fund_amount,
            ]
        );

        return $this->success([
            'id' => $disbursement->id,
            'bank_name' => $disbursement->bank_name,
            'account_holder_name' => $disbursement->account_holder_name,
            'status' => $disbursement->status,
        ], 'Data rekening berhasil disimpan.');
    }
}
