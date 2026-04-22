<?php

declare(strict_types=1);

namespace App\Pdfs;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;
use Illuminate\Support\Str;

final class AccountReportPdf extends BasePdfTemplate
{
    protected \App\Models\Accounting\AccountReport $report;

    protected $filename;

    public function __construct(AccountReport $accountReport, $locale, $filename)
    {
        parent::__construct($locale, __('reports.account.title')); // Pass locale & title
        $this->report = $accountReport;
        $this->filename = $filename;

        // Set document metadata
        $this->SetTitle(__('reports.account.title'));
        $this->SetSubject(__('reports.account.title'));
    }

    public function generateContent(): string
    {
        $hH1 = 12;
        $h = 9;
        $sm = 7;
        $wHeading = 40;
        $width_Datum = 15;
        $width_Buchung = 48;
        $width_Referenz = 37;
        $width_Einnahme = 16;
        $width_Ausgabe = 16;
        $width_Typ = 20;
        $width_Stand = 0;

        $created_by = $this->report->user->member->fullName();

        $this->AddPage();
        $this->SetFont('helvetica', 'B', $hH1);
        $this->Cell(0, 6, 'Übersicht', 0, 1, 'L');

        $this->SetFont('helvetica', 'B', $sm);
        $this->Cell($wHeading, 3, 'Finanzkonto', 0, 0, 'L');
        $this->Cell($wHeading, 3, 'Nummer:', 0, 0, 'L');
        $this->Cell($wHeading, 3, 'Institut:', 0, 0, 'L');
        $this->Cell($wHeading, 3, 'Typ:', 0, 0, 'L');
        $this->Cell(0, 3, 'Startguthaben:', 0, 1, 'R');
        $this->SetFont('helvetica', '', $h);

        $account = Account::find($this->report->account_id);
        $this->Cell($wHeading, 4, $account->name, 0, 0, 'L');
        $this->Cell($wHeading, 4, $account->number, 0, 0, 'L');
        if ($account->institute) {
            $this->Cell($wHeading, 4, $account->institute.' / '.$account->iban, 0, 1, 'L');
        } else {
            $this->Cell($wHeading, 4, '-', 0, 0, 'L');
        }
        $this->Cell($wHeading, 3, $account->type->value, 0, 0, 'L');
        $this->Cell(0, 4, $this->nf($account->starting_amount), 0, 1, 'R');

        $this->ln(5);

        $this->SetFont('helvetica', 'B', $sm);
        $this->Cell($wHeading, 3, 'Erstellt am:', 0, 0, 'L');
        $this->Cell($wHeading, 3, 'Erstellt von:', 0, 0, 'L');
        $this->Cell($wHeading, 3, 'Begin der Erfassung:', 0, 0, 'L');
        $this->Cell(0, 3, 'Ende der Erfassung:', 0, 1, 'L');

        $this->SetFont('helvetica', '', $h);
        $this->Cell($wHeading, 5, $this->report->created_at->isoFormat('LLL'), 0, 0, 'L');
        $this->Cell($wHeading, 5, $created_by, 0, 0, 'L');
        $this->Cell($wHeading, 5, $this->report->period_start->locale($this->locale)
            ->isoFormat('LLL'), 0, 0, 'L');
        $this->Cell(0, 5, $this->report->period_end->locale($this->locale)
            ->isoFormat('LLL'), 0, 1, 'L');

        $this->ln(5);

        $this->SetFont('helvetica', 'B', $hH1);
        $this->Cell(0, 6, 'Buchungsliste', 0, 1, 'L');

        $html = '<table cellpadding="3" cellspacing="0" style="font-size: 10pt; font-weight: normal;" ><thead><tr>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" width="40">Datum</th>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" width="120">Buchung</th>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" width="100">Referenz</th>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" width="52" align="right">Einnahme</th>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" width="52"  align="right">Ausgabe</th>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" width="53" >Typ</th>
        <th style="border-bottom: 1px solid grey; font-size: 8pt; font-weight: bold;" align="right">Stand EUR</th>
        </tr></thead><tbody>
        <tr>
        <td width="40"></td>
        <td width="384" colspan="5">Übernahme aus Vormonat</td>
        <td align="right">'.$this->nf($this->report->starting_amount).'</td>
        </tr>';

        /* $this->SetFont('helvetica', 'B', $sm);
         $this->cell($width_Datum,4,'Datum',0,0,'L');
         $this->cell($width_Buchung,4,'Buchung',0,0,'L');
         $this->cell($width_Referenz,4,'Referenz',0,0,'L');
         $this->cell($width_Einnahme,4,'Einnahme',0,0,'R');
         $this->cell($width_Ausgabe,4,'Ausgabe',0,0,'R');
         $this->cell($width_Typ,4,'Typ',0,0,'L');
         $this->cell($width_Stand,4,'Stand EUR',0,1,'R');


         $this->SetFont('helvetica', '', $h);
         $this->cell($width_Datum,4,$this->report->period_start->locale($this->locale)->isoFormat('Do MMMM'),1,0,'L');
         $this->cell($width_Buchung,4,'Übernahme aus Vormonat',1,0,'L');
         $this->cell($width_Referenz,4,'',1,0,'L');
         $this->cell($width_Einnahme,4,'',1,0,'R');
         $this->cell($width_Ausgabe,4,'',1,0,'R');
         $this->cell($width_Typ,4,'',1,0,'L');
         $this->cell($width_Stand,4,$a,1,1,'R');*/

        $transactions = Transaction::where('account_id', '=', $this->report->account_id)
            ->where('status', TransactionStatus::booked->value)
            ->whereBetween('date', [$this->report->period_start, $this->report->period_end])
            ->orderBy('date')
            ->get();

        $sub = $this->report->starting_amount;
        $total_in = 0;
        $total_out = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->type === TransactionType::Deposit) {
                $in = $transaction->amount_gross * $transaction->type->multiplier();
                $out = 0;
                $sub += $in;
                $total_in += $in;
            } else {
                $out = $transaction->amount_gross * $transaction->type->multiplier();
                $in = 0;
                $sub += $out;
                $total_out += $out;
            }

            /*            $this->cell($width_Datum,5,$transaction->date->locale($this->locale)->isoFormat('Do MMMM'),1,0,'L');
                        $this->cell($width_Buchung,5,Str::limit($transaction->label,25),1,0,'L');
                        $this->cell($width_Referenz,5,Str::limit($transaction->reference,20),1,0,'L');
                        $this->cell($width_Einnahme,5,$this->nf($in),1,0,'R');
                        $this->cell($width_Ausgabe,5,$this->nf($out),1,0,'R');
                        $this->cell($width_Typ,5,TransactionType::from($transaction->type)->value,1,0,'L');
                        $this->cell($width_Stand,5,$this->nf($sub),1,1,'R');*/
            $html .= '
<tr>
    <td style="border-bottom: solid 0.2rem #999999;" width="40">'.$transaction->date->locale($this->locale)
                ->isoFormat('Do MMM').' </td>
    <td style="border-bottom: solid 0.2rem #999999;" width="120">'.$transaction->label.'</td>
    <td style="border-bottom: solid 0.2rem #999999;" width="100">'.$transaction->reference.'</td>
    <td style="border-bottom: solid 0.2rem #999999;" width="52" align="right">'.$this->nf($in).'</td>
    <td style="border-bottom: solid 0.2rem #999999;" width="52" align="right">'.$this->nf($out).'</td>
    <td style="border-bottom: solid 0.2rem #999999;" width="60">'.$transaction->type->label().'</td>
    <td style="border-bottom: solid 0.2rem #999999;" align="right">'.$this->nf($sub).'</td>
</tr>
';
        }
        $html .= '</tbody></table>';
        $this->writeHTML($html, true, false, true, false, '');
        $this->AddPage();
        $this->ln(10);
        $this->SetFont('helvetica', 'B', $hH1);
        $this->Cell(0, 6, 'Zusammenfassung', 0, 1, 'L');

        $this->SetFont('helvetica', '', $h);
        $this->Cell($width_Referenz, 6, 'Stand bei Übernahme', 0, 0, 'L');
        $this->Cell($width_Typ, 6, $this->nf($this->report->starting_amount).' EUR', 0, 1, 'R');

        $this->Cell($width_Referenz, 6, 'Summe Einnahmen', 0, 0, 'L');
        $this->Cell($width_Typ, 6, $this->nf($total_in).' EUR', 0, 1, 'R');

        $this->Cell($width_Referenz, 6, 'Summe Ausgaben', 0, 0, 'L');
        $this->Cell($width_Typ, 6, $this->nf($total_out).' EUR', 0, 1, 'R');

        $this->Cell($width_Referenz, 6, 'Neuer Stand', 'T', 0, 'L');
        $this->Cell($width_Typ, 6, $this->nf($sub).' EUR', 'T', 1, 'R');

        $this->ln(20);

        $this->SetFont('helvetica', 'B', $h);
        $this->Cell(30, 7, 'Erstellt / Gez. von :', 0, 0, 'L');

        $this->SetFont('helvetica', '', $h);
        $this->Cell(0, 7, $created_by.' - '.$this->report->user->member->roles->first()->name[$this->locale], '', 1);

        $this->SetFont('helvetica', 'B', $h);
        $this->Cell(30, 7, 'Ort, Datum : ', 0, 0, 'L');

        $this->SetFont('helvetica', '', $h);
        $this->Cell(0, 7, setting('organization.city').', '.$this->report->created_at->isoFormat('LLLL'), '', 1, 'L');

        $this->ln(2);

        if ($this->report->audits->count() > 0) {
            $this->SetFont('helvetica', 'B', $hH1);
            $this->Cell(40, 7, 'Prüfungen', 0, 1, 'L');
            foreach ($this->report->audits as $audit) {

                if ($audit->isAudited()) {
                    $this->SetFont('helvetica', 'B', $h);
                    $this->Cell(30, 7, 'Geprüft / Gez. von :', 0, 0, 'L');

                    $this->SetFont('helvetica', '', $h);
                    $this->Cell(0, 7, $audit->user->member->fullName().' - '.$audit->user->member->roles->first()->name[$this->locale], '0', 1);

                    $this->SetFont('helvetica', 'B', $h);
                    $this->Cell(30, 7, 'Ort, Datum : ', 0, 0, 'L');

                    $this->SetFont('helvetica', 'B', $h);
                    $this->Cell(30, 7, 'Ergebnis: ', 0, 0, 'L');

                    $approved = $audit->is_approved ? 'Freigegeben' : 'Nicht freigegeben';

                    $this->Cell(0, 7, $approved, 0, 1);

                    if (! $audit->is_approved) {
                        $this->SetFont('helvetica', 'B', $h);
                        $this->Cell(0, 5, 'Begründung', 0, 1);
                        $this->SetFont('helvetica', '', $h);
                        $this->MultiCell(0, 7, $audit->reason, '', 'L');
                    }

                } else {
                    $this->SetFont('helvetica', 'B', $h);
                    $this->Cell(30, 7, 'Prüfer(in):', 0, 0, 'L');

                    $this->SetFont('helvetica', '', $h);
                    $this->Cell(0, 7, $audit->user->member->fullName().' - '.$audit->user->member->roles->first()->name[$this->locale], '', 1);

                    $this->SetFont('helvetica', 'B', $h);
                    $this->Cell(30, 7, 'Ort, Datum : ', 0, 0, 'L');

                    $this->SetFont('helvetica', '', $h);
                    $this->Cell(0, 7, 'Prüfung nicht abgeschlossen', '', 1);

                }

                $this->ln(2);

            }
        }

        return $this->Output($this->filename); // 'D' = Download, 'I' = Inline
    }
}
