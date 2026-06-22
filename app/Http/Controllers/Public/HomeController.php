<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $programs = Scholarship::query()
            ->whereIn('status', ['open', 'closed', 'announced'])
            ->orderByDesc('date_start')
            ->limit(3)
            ->withCount('applications')
            ->get([
                'id', 'name', 'slug', 'description', 'academic_year',
                'fund_amount', 'quota_primary',
                'date_start', 'date_end', 'status',
            ]);

        return view('welcome', [
            'programs' => $programs,
        ]);
    }
}
