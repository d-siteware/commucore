<p>Viele Grüße,<br>{{ setting('organization.name') }}</p>
</div>
</td>
</tr>
</table>
</td>
</tr><!-- end tr -->
<!-- 1 Column Text + Button : END -->
</table>
<!--

FOOTER

-->
<table align="center"
       role="presentation"
       cellspacing="0"
       cellpadding="0"
       border="0"
       width="100%"
       style="margin: auto;"
>
    <tr>
        <td valign="middle"
            class="bg_light footer email-section"
        >
            <table>
                <tr>
                    <td valign="top"
                    width="50%"
                    style="padding-top: 20px; padding-right:5px"
                    >
                        <a href=" {{ route('mailing-list.unsubscribe', $token) }})">{{ __('mails.unsubscribe_link_label') }}</a>
                    </td>
                </tr>
                <tr>
                    <td valign="top"
                        width="50%"
                        style="padding-top: 20px; padding-right:5px"
                    >
                        <h3 class="heading">{{ __('mails.contact') }}</h3>
                        <ul>
                            <li>
                                        <span class="text">
                                            Hanns-Eisler-Str. 44<br>10409 Berlin <br> Tel: +49 163 377 20 91
                                        </span>
                            </li>
                            <li>
                                        <span class="text">
                                        <a href="mailto:szia@magyar-kolonia-berlin.org">szia@magyar-kolonia-berlin.org</a>
                                        </span>
                            </li>
                        </ul>
                    </td>
                    <td valign="top"
                        width="50%"
                        style="padding-top: 20px; padding-left:20px;"
                    >
                        <h3 class="heading">Internet</h3>
                        <ul>
                            <li>
                                <a href="mailto:{{ setting('organization.email') }}">szia@magyar-kolonia-berlin.org</a>
                            </li>
                            <li>
                                <a href="{{ setting('organization.link') }}">{{ setting('organization.name') }}</a>
                            </li>
                            <li>
                                <a href="#">Facebook</a>
                            </li>
                        </ul>

                    </td>
                </tr>
                <tr>
                    <td colspan="2"
                        valign="top"
                        width="100%"
                        style="padding-top: 20px;"
                    >
                        <h3 class="heading">Magyar Kolónia Berlin e. V.</h3>
                        <p>
                            <span class="text">{{ __('mails.president') }} <strong>Robotka József</strong></span> |
                            <span class="text">{{ __('mails.president.deputy') }} <strong>Temesi Mátyás</strong></span> |
                            <span class="text">{{ __('mails.treasury') }} <strong>Körtvélyesy Dániel</strong></span> |
                            <span class="text">{{ __('mails.cultural.director') }} <strong>László Levente</strong></span> |
                            <span class="text">{{ __('mails.social.affairs') }} <strong>Heuer Judith</strong></span> |
                            <span class="text">{{ __('mails.social.affairs.deputy') }} <strong>Kovács Ágnes</strong></span>
                            <span class="text">{{ __('mails.secretariat.hu') }} <strong>Simó Enikö</strong></span> |
                            <span class="text">{{ __('mails.secretariat.de') }} <strong>Hoffmann Andreas</strong></span> |
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr><!-- end: tr -->
    @if(isset($unsubscribe))
        <tr>
            <td class="bg_light"
                style="text-align: center;"
            >
                <p>No longer want to receive these email? You can
                    <a href="#"
                       style="color: rgba(0,0,0,.8);"
                    >Unsubscribe here
                    </a>
                </p>
            </td>
        </tr>
    @endif
</table>

</div>
</center>
</body>
</html>

