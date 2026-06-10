<p>{{ __('mails.footer_greeting') }}<br>{{ setting('organization.name') }}</p>
</div>
</td>
</tr>
</table>
</td>
</tr><!-- end tr -->
<!-- 1 Column Text + Button : END -->
</table>
<!-- FOOTER -->
<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 auto !important; border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important;">
    <tr>
        <td valign="middle" class="bg_light footer email-section" style="background: #fafafa; border-top: 1px solid rgba(0,0,0,.05); color: rgba(0,0,0,.5); padding: 2.5em;">
            <table style="border-spacing: 0 !important; border-collapse: collapse !important; table-layout: fixed !important;">
                @if(isset($token))
                    <tr>
                        <td valign="top" width="50%" style="padding-bottom: 20px; padding-right: 5px; font-size: 8pt; text-align: center;" align="center" colspan="2">
                            <p style="font-size: 8pt; text-align: center; margin-top: 0;">{{ __('post.notification_mail.btn_unsubscribe_link_label') }}</p>
                            <a href="{{ route('mailing-list.unsubscribe', $token) }}" style="color: #096; text-decoration: none; font-size: 10px;">{{ __('mails.unsubscribe_link_label') }}</a>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td valign="top" width="50%" style="padding-top: 20px; padding-right: 5px;">
                        <h3 class="heading" style="color: #000; font-size: 17px; margin-top: 0; font-family: Lato, sans-serif; font-weight: 400;">{{ __('mails.contact') }}</h3>
                        <ul style="margin: 0; padding: 0;">
                            <li style="list-style: none; margin-bottom: 10px;">
                                <span class="text" style="font-size: 10px;">{{ setting('organization.name') }}<br>{{ setting('organization.address') }}<br>{{ setting('organization.zip') }} - {{ setting('organization.city') }}</span>
                            </li>
                            <li style="list-style: none; margin-bottom: 10px;">
                                <span class="text" style="font-size: 10px;">
                                    <a href="mailto:{{ setting('organization.email') }}" style="color: #000; text-decoration: none;">{{ setting('organization.email') }}</a>
                                </span>
                            </li>
                        </ul>
                    </td>
                    <td valign="top" width="50%" style="padding-top: 20px; padding-left: 20px;">
                        <h3 class="heading" style="color: #000; font-size: 17px; margin-top: 0; font-family: Lato, sans-serif; font-weight: 400;">Internet</h3>
                        <ul style="margin: 0; padding: 0;">
                            <li style="list-style: none; margin-bottom: 10px;">
                                <a href="mailto:{{ setting('organization.email') }}" style="color: #000; text-decoration: none; font-size: 10px;">{{ setting('organization.email') }}</a>
                            </li>
                            <li style="list-style: none; margin-bottom: 10px;">
                                <a href="{{ setting('organization.web') }}" style="color: #000; text-decoration: none; font-size: 10px;">{{ setting('organization.web') }}</a>
                            </li>
                           <li style="list-style: none; margin-bottom: 10px;">
                                <a href="{{ setting('organization.web') }}/privacy" style="color: #000; text-decoration: none; font-size: 10px;">{{ __('privacy.title') }}</a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" width="100%" style="padding-top: 20px;">
                        <h3 class="heading" style="color: #000; font-size: 17px; margin-top: 0; font-family: Lato, sans-serif; font-weight: 400;">{{ setting('organization.name') }}</h3>
                        <p style="margin-top: 0;">
                            {{ __('impressum.register_id') }}
                            {{ setting('organization.register_id') }} / {{setting('organization.registered_date')}}
                        </p>
                        <div style="margin-top: 0;">
                            {!! \App\Models\Membership\Member::leaderBoardHtml(app()->getLocale()) !!}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end: tr -->
    @if(isset($unsubscribe))
        <tr>
            <td class="bg_light" style="text-align: center; background: #fafafa;">
                <p style="margin-top: 0; font-size: 10px; color: rgba(0,0,0,.5);">
                    No longer want to receive these emails? You can
                    <a href="#" style="color: rgba(0,0,0,.8); text-decoration: none;">Unsubscribe here</a>
                </p>
            </td>
        </tr>
    @endif
</table>

</div>
</center>
</body>
</html>
