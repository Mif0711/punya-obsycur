<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'finePerDay' => Setting::integer(Setting::KEY_FINE_PER_DAY, 5000),
            'graceDays' => Setting::integer(Setting::KEY_GRACE_DAYS, 2),
            'loanDays' => Setting::integer(Setting::KEY_LOAN_DAYS, 7),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Setting::put(Setting::KEY_FINE_PER_DAY, (string) $data['fine_per_day']);
        Setting::put(Setting::KEY_GRACE_DAYS, (string) $data['grace_days']);
        Setting::put(Setting::KEY_LOAN_DAYS, (string) $data['loan_days']);

        return back()->with('success', 'Pengaturan denda dan durasi peminjaman disimpan.');
    }
}
