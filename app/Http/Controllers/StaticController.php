<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Membership\Member;
use App\Models\User;
use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class StaticController extends Controller
{
    public function privacy(): \Illuminate\View\View
    {
        return view('privacy',['locale' => app()->getLocale() ?? 'de']);
    }

    public function imprint(): \Illuminate\View\View
    {
        return view('impressum');
    }

    public function aboutUs(): \Illuminate\View\View
    {

        $mdService = new MarkdownService;

        $html = $mdService->getMarkdownAsHtml('statute_2014');

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

        return view('about-us', ['team' => $team], ['html' => $html]);
    }

    public function rollbackMail(Request $request): \Illuminate\Http\RedirectResponse
    {
        $decrypted = decrypt($request->query('token'));
        [$userId, $oldEmail] = explode('|', $decrypted);

        $user = User::findOrFail($userId);
        $user->update(['email' => $oldEmail]);
        Auth::guard('web')->login($user);

        return redirect('/dashboard')->with('status', 'Email zurückgesetzt.');
    }
}
