<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Models\Membership\Member;

final class MemberApplicationPdf extends BasePdfTemplate
{
    public function __construct(public Member $member, public string $filename, $locale)
    {
        app()->setLocale($locale);

        parent::__construct($locale, __('report.event.title'));
    }

    public function generateContent(): string
    {
        $html = view('pdf.membership-application', ['member' => $this->member, 'locale' => $this->member->localeModel()])->render();
        //        $filename = __('members.apply.print.filename', ['tm' => date('YmdHis'), 'id' => $this->member->id]);

        $this->SetTitle(__('members.apply.print.title'));
        $this->SetSubject(__('members.apply.print.title'));
        $this->setMargins(24, 10, 10);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->AddPage();
        $this->writeHTML($html, true, false, true, false, '');

        return $this->Output($this->filename);
    }
}
