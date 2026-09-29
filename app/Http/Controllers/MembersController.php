<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\MemberFeeType;
use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use App\Services\PdfGeneratorService;
use Illuminate\Http\Response;

final class MembersController extends Controller
{
    /**
     * Mitgliedsantrag als PDF abrufen — Token-gesichert, ohne Authentifizierung.
     *
     * Der Token ist der 64-stellige, zufällige Wert aus der member_applications-Tabelle,
     * der bereits im Verifizierungs-Flow per E-Mail verschickt wird. Dadurch ist der
     * Antrag nur für den Besitzer des Links abrufbar, nicht per ID-Enumeration.
     */
    public function printApplication(string $token): Response
    {
        $application = MemberApplication::query()
            ->where('token', $token)
            ->firstOrFail();

        $member = new Member;
        $member->id = $application->id;
        $member->name = $application->name;
        $member->first_name = $application->first_name;
        $member->email = $application->email;
        $member->phone = $application->phone;
        $member->mobile = $application->mobile;
        $member->address = $application->address;
        $member->zip = $application->zip;
        $member->city = $application->city;
        $member->country = $application->country;
        $member->birth_date = $application->birth_date;
        $member->gender = $application->gender ?? Gender::ma;
        $member->fee_type = MemberFeeType::FULL;

        $pdfContent = PdfGeneratorService::generatePdf('member-application', $member, null, false);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"mitgliedsantrag-{$application->id}-".now()->format('Ymd').'.pdf"');
    }
}
