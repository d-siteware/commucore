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
                                            {{ setting('organization.street') }}
                                        </span>
                            </li>
                            <li>
                                        <span class="text">
                                        <a href="mailto:{{ setting('organization.email') }}">{{ setting('organization.email') }}</a>
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
                                <a href="mailto:{{ setting('organization.email') }}">{{ setting('organization.email') }}</a>
                            </li>
                            <li>
                                <a href="{{ setting('organization.link') }}">{{ setting('organization.name') }}</a>
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
                        <h3 class="heading">{{ setting('organization.name') }}</h3>
                        <p>
                           {{ \App\Models\Membership\Member::leaderBoardHtml() }}
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

