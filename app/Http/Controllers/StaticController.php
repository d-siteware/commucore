<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Membership\Member;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class StaticController extends Controller
{
    public function privacy(): View
    {
        return view('privacy', ['locale' => app()->getLocale()]);
    }

    public function imprint(): View
    {
        return view('impressum');
    }

    public function aboutUs(): View
    {

        $aboutContent = app(SettingsService::class)->get('organization.about_us');
        if (is_array($aboutContent)) {
            $aboutContent = $aboutContent[app()->getLocale()]
                ?? $aboutContent[config('app.fallback_locale')]
                ?? '';
        }

        $statuteContent = app(SettingsService::class)->get('organization.statute');
        if (is_array($statuteContent)) {
            $statuteContent = $statuteContent[app()->getLocale()]
                ?? $statuteContent[config('app.fallback_locale')]
                ?? '';
        }

        $lang = app()->getLocale();

        $team = Member::with(['activeRoles' => function ($query): void {
            $query->wherePivot('resigned_at', null);
        }])
            ->join('member_role', 'members.id', '=', 'member_role.member_id')
            ->join('roles', 'member_role.role_id', '=', 'roles.id')
            ->where('member_role.resigned_at', null)
            ->orderBy('roles.sort', 'asc')
            ->select('members.*') // Ensure only Member columns are selected
            ->distinct() // Avoid duplicate members if they have multiple roles
            ->get();

        return view('about-us', ['team' => $team, 'aboutContent' => $aboutContent, 'statuteContent' => $statuteContent, 'locale' => $lang]);
    }

    public function rollbackMail(Request $request): RedirectResponse
    {
        $decrypted = decrypt($request->query('token'));
        [$userId, $oldEmail] = explode('|', $decrypted);

        $user = User::findOrFail($userId);
        $user->update(['email' => $oldEmail]);
        Auth::guard('web')->login($user);

        return redirect('/dashboard')->with('status', 'Email zurückgesetzt.');
    }
}
