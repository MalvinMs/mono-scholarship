<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __invoke(Request $request)
    {
        $programs = Scholarship::query()
            ->whereIn('status', ['open', 'closed', 'announced'])
            ->orderByDesc('date_start')
            ->withCount('applications')
            ->get([
                'id', 'name', 'slug', 'description', 'academic_year',
                'fund_amount', 'quota_primary', 'quota_reserve',
                'date_start', 'date_end', 'status',
            ]);

        return view('public.program-list', [
            'programs' => $programs,
        ]);
    }
}
