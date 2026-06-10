<?php

declare(strict_types=1);

return [
    'title' => 'Members overview',
    'header' => 'Here you will find a sortable overview of all members. In the submenu, members can be edited, payments recorded, or members marked as inactive. The latter replaces deleting the entry.',
    'table' => [
        'header' => [
            'name' => 'Name',
            'phone' => 'Mobile number',
            'status' => 'Status',
            'fee_status' => 'Fee status',
            'birthday' => 'Birthday',
        ],
    ],
    'con' => [
        'men' => [
            'edit' => 'Edit',
            'payment' => 'Record payment',
            'delete' => 'Cancel',
            'reactivate' => 'Activate',
        ],
    ],
    'widget' => [
        'birthday' => [
            'card' => [
                'table' => [
                    'header' => [
                        'member' => 'Member',
                        'birthday' => 'Date of birth',
                        'newage' => 'Age',
                    ],
                ],
                'heading' => 'Upcoming birthdays for :name',
            ],
        ],
    ],
    'fee-type' => [
        'label' => 'Fee status',
        'free' => 'Fee exempt',
        'standard' => 'Standard fee',
        'discounted' => 'Reduced fee',
    ],
    'apply' => [
        'dsgvo' => [
            'section' => [
                'label' => 'Consents',
                'text' => 'In order to ensure the data protection-compliant handling of your data, we ask for the following consents. You can revoke them at any time. Further information can be found in our privacy policy.',
            ],
            'gdpr' => [
                'label' => 'Data protection',
                'description' => 'I consent to my personal data provided in the application being stored and processed for the purpose of processing my membership application and managing my membership.',
                'required' => 'This consent is required for the application to proceed.',
            ],
            'newsletter' => [
                'label' => 'Notifications',
                'description' => 'I agree to be informed by email about events, club activities and important information of the association.',
            ],
            'photo' => [
                'label' => 'Photo/Video',
                'description' => 'I agree that photos or videos taken during club events in which I may be visible may be used for club purposes (e.g. website, newsletter or club documentation).',
            ],
        ],
        'expired' => ['title' => 'Expired', 'text' => 'The link to confirm the email address has expired. Please try again or contact us.'],
        'invalid' => ['title' => 'Invalid', 'text' => 'This link is not valid or no longer exists.'],
        'verify' => [
            'title' => 'Confirm email address',
            'greeting' => 'Hello :name!',
            'summary' => 'We have recorded the following data. Please confirm your email address to continue.',
            'submit' => 'Save confirmation with data protection consents',
            'mail' => [
                'subject' => 'Your membership application for :organization has been recorded!',
                'greeting' => 'Dear :name,',
                'line1' => 'We have received your membership application. Please confirm your email address to continue.',
                'action' => 'Confirm email address',
                'expires' => 'The link is valid for 48 hours',
                'line2' => 'By confirming your email address, your membership application for :organization will be submitted.',
            ],
        ],
        'pending' => [
            'title' => 'Membership application',
            'text' => 'Thank you for your application. You will shortly receive an email from us so that you can confirm your email address.',
        ],
        'validation' => [
            'email' => [
                'application_pending' => 'A membership application has already been submitted with this email address.',
                'already_member' => 'This email address is already registered as a member.',

            ],
        ],
        'done' => [
            'title' => 'Done',
            'text' => 'Your application has been submitted successfully. Thank you! We will get back to you.',
        ],
        'discount' => [
            'label' => 'Apply for reduced membership fee',
            'reason' => [
                'label' => 'Reason for reduction',
            ],
        ],
        'fee' => [
            'text' => 'I have been informed about the monthly membership fee of :sum EUR and undertake to pay it.',
            'label' => 'Paying members must pay a monthly amount of :sum EUR. Members over 75 years of age are exempt from the fee obligation.',
            'payment' => [
                'banktt' => 'The due contribution is to be paid to the specified account.',
                'paypals' => 'The contribution can be sent to one of the PayPal accounts. Please select "Send to friends" as the method, otherwise 1.8% will be deducted as a fee by PayPal.',
                'paypal' => 'The contribution can be sent to the PayPal account :iban. Please select "Send to friends" as the method, otherwise 1.8% will be deducted as a fee.',
            ],
        ],
        'full_fee' => [
            'label' => 'Paying members must pay a monthly amount of :sum EUR.',
        ],
        'discounted_fee' => [
            'label' => 'Members can apply for a reduced monthly contribution of :sum EUR.',
        ],
        'free_fee' => [
            'label' => 'Members over :age years of age are exempt from the fee obligation.',
        ],
        'email' => [
            'none' => 'I don\'t have an email address!',
            'without' => [
                'text' => 'If you do not have an email address, you can print this form, sign it and send it by post to the following address:',
            ],
            'benefits' => 'Members with an email address automatically receive notifications about events and have access to the notice board.',
            'note' => [
                'header' => 'Important!',
                'content' => 'To submit via the web program, you must provide your email address. If you do not have an email address, select the postal service.',
            ],
        ],
        'checkAndSubmit' => 'Review information and submit form',
        'printAndSubmit' => 'Print form',
        'title' => 'Application for membership of :name',
        'text' => 'We are pleased that you would like to become a member of :name.',
        'process' => 'Admission follows the following procedure:',
        'step1' => [
            'label' => 'Step 1',
            'text' => 'As a first step, please fill out the following form.',
        ],
        'via' => [
            'web' => 'Send via web',
            'postal' => 'Postal delivery',
        ],
        'step2' => [
            'label' => 'Step 2',
            'text' => 'Check your details',
        ],
        'click' => [
            'button' => 'Click the button',
            'checkbox' => 'Click the checkbox',
        ],
        'step3a' => [
            'label' => 'Step 3a',
            'text' => 'As a first step, please fill out the following form.',
        ],
        'step3b' => [
            'label' => 'Step 3b',
            'text' => 'Click the "Print form" button.',
        ],
        'step4a' => [
            'label' => 'Step 4a',
            'text' => 'You will receive an email from the system with a one-time confirmation link.',
        ],
        'step4b' => [
            'label' => 'Step 4b',
            'text' => 'Click the [Print form] button to create a PDF version of the form.',
        ],
        'step5a' => [
            'label' => 'Step 5a',
            'text' => 'By clicking the link, you confirm that the registration actually comes from you.',
        ],
        'step5b' => [
            'label' => 'Step 5b',
            'text' => 'Print the form, sign it and send it to the address provided on the form.',
        ],
        'step6' => [
            'label' => 'Step 6',
            'text' => 'We will review your details and contact you personally if further information is needed.',
        ],
        'step7' => [
            'label' => 'Step 7',
            'text' => 'Finally, the management team will decide on your admission, and you will receive a notification by email or post as per your chosen method.',
        ],
        'submission' => [
            'success' => [
                'head' => 'Success!',
                'msg' => 'We have received your application and will review it. Thank you!',
            ],
            'failed' => [
                'head' => 'Error!',
                'msg' => 'Unfortunately, an error has occurred. Please try again.',
            ],
        ],
        'print' => [
            'title' => 'Application for membership of :name',
            'greeting' => 'Dear Sir or Madam,',
            'text' => 'I hereby apply for membership of :name',
            'regards' => 'Yours sincerely',
            'overview' => [
                'person' => 'About me',
                'contact' => 'My contact information',
            ],
            'filename' => 'Application_Membership_Hungarian_Colony_Berlin_mid-:id:tm.pdf',
        ],
    ],
    'birth_date' => 'Date of birth',
    'birth_place' => 'Place of birth',
    'name' => 'Last name',
    'first_name' => 'First name',
    'email' => 'Email',
    'phone' => 'Phone',
    'mobile' => 'Mobile number',
    'address' => 'Address',
    'zip' => 'Postal code',
    'city' => 'City',
    'country' => 'Country',
    'locale' => 'Preferred language',
    'gender' => 'Gender',
    'deduction_reason' => 'Older than :age years',
    'type' => [
        'label' => 'Membership type',
        'exempt' => 'Excluded',
        'standard' => 'Member',
        'applicant' => 'Applicant',
        'board' => 'Board',
        'advisor' => 'Advisor',
    ],
    'linked_user' => 'Linked to user account',
    'unlink_user' => 'Unlink',
    'left_at' => 'Date of leaving',
    'section' => [
        'admins' => 'To be completed by the board',
        'person' => 'Person',
        'address' => 'Address',
        'phone' => 'Phone',
        'fees' => 'Fee',
        'payments' => 'Payments',
        'deduction' => 'Reduction',
        'email' => 'Email address',
    ],
    'update' => [
        'success' => [
            'title' => 'Success',
            'content' => 'The member data has been updated successfully.',
        ],
    ],
    'date' => [
        'applied_at' => 'Membership applied on',
        'verified_at' => 'Email verified on',
        'entered_at' => 'Membership confirmed on',
        'left_at' => 'Left on',
        'gdpr_consent_at' => 'Privacy confirmed on',
        'newsletter_consent_at' => 'Newsletter confirmed on',
        'photo_consent_at' => 'Photo/Video confirmed on',
    ],
    'btn' => [
        'sendVerificationMail' => [
            'label' => 'Send verification reminder',
        ],
        'addMember' => 'Create new',
        'sendAcceptanceMail' => [
            'label' => 'Accept application and send email',
        ],
        'sendAcceptance' => [
            'label' => 'Accept application',
        ],
        'setEnteredAt' => [
            'label' => 'Accepted on',
        ],
        'inviteAsUser' => [
            'label' => 'Invite member as user',
        ],
        'cancelMembership' => [
            'label' => 'Cancel membership',
        ],
    ],
    'accordion' => [
        'optionals' => [
            'label' => 'Optional information',
        ],
    ],
    'appliance_received' => [
        'mail' => [
            'subject' => 'Your membership application has been received!',
            'greeting' => 'Hello :name,',
            'text' => 'we have received your membership application and thank you for your interest in our community. We will review your application as soon as possible and get back to you.',
        ],
    ],
    'cancel' => [
        'modal' => [
            'title' => 'Cancel membership',
            'text' => 'Please confirm the cancellation of membership.',
        ],
        'confirm_text_input' => [
            'label' => 'Please enter the last name to confirm',
        ],
        'btn' => [
            'final' => [
                'label' => 'Cancel membership permanently',
            ],
        ],
    ],
    'optional-data' => [
        'text' => 'Additional information can be provided here.',
    ],
    'familystatus' => [
        'label' => 'Marital status',
        'single' => 'Single',
        'married' => 'Married',
        'divorced' => 'Divorced',
        'n_a' => 'Not specified',
    ],
    'show' => [
        'title' => 'Member overview: :name',
        'created_at' => 'Created on',
        'updated_at' => 'Last edited on',
        'about' => 'Personal information',
        'membership' => 'Membership',
        'change_requests' => 'Change requests',
        'payments' => 'Payments',
        'store' => 'Save',
        'documents' => 'Documents',
        'fee_msg' => [
            'exempted' => 'Fee exempt',
            'paid' => 'Fee paid',
        ],
        'invitation_sent' => 'Invitation has been sent',
        'member' => [
            'reactivate' => 'Reactivate member',
        ],
        'select_user' => 'Select user',
        'empty_user_list' => 'No users found',
        'heading' => 'Show member data',
        'attached' => [
            'success' => [
                'head' => 'Success!',
                'msg' => 'The user :name has been linked successfully.',
            ],
            'placeholder' => 'Select user',
            'failed' => [
                'head' => 'Error!',
                'msg' => 'The user could not be linked.',
            ],
        ],
        'detached' => [
            'success' => [
                'head' => 'Success!',
                'msg' => 'The link to user :name has been removed successfully.',
            ],
        ],
    ],
    'register' => [
        'title' => 'Set password for registration',
        'page_title' => 'Complete registration',
        'password_requirements' => 'The password should meet the following criteria:',
        'password' => 'Password',
        'password_confirm' => 'Confirm password',
        'submit' => 'Complete registration',
        'checkLength' => 'At least 8 characters',
        'checkCapital' => 'At least one capital letter',
        'checkNumbers' => 'At least one number',
        'checkSpecial' => 'At least one special character (!"$§%(){}[])',
    ],
    'notifications' => [
        'new_applicant' => [
            'intro' => 'New application',
            'subject' => 'New application',
            'text' => 'A new application has been received.',
            'cta' => 'View in dashboard',
            'reply_subject' => 'Your application for membership of :name',
        ],
    ],
    'widgets' => [
        'applicants' => [
            'title' => 'New membership applications',
            'empty_search' => 'No matching entry',
            'empty_list' => 'No open applications',
            'modal' => [
                'title' => 'View application',
                'reject' => [
                    'title' => 'Rejection',
                    'subtitle' => 'Rejection must be justified',
                    'reason_label' => 'Reason',
                    'reason_placeholder' => 'Unfortunately, your application ...',
                    'confirm_btn' => 'Send rejection',
                ],
                'fields' => [
                    'applied_at' => 'Applied on :date',
                    'email' => 'Email',
                    'birth_date' => 'Birthday',
                    'phone' => 'Phone',
                    'address' => 'Address',
                    'gdpr' => 'Data protection',
                    'newsletter' => 'Newsletter',
                    'photo_consent' => 'Photo/Video',

                ],
                'btn' => [
                    'cancel' => 'Cancel',
                    'reject' => 'Reject',
                    'accept' => 'Accept',
                ],
            ],
            'confirm' => [
                'deletion' => [
                    'title' => 'Success',
                    'text' => 'The selected applications have been deleted',
                ],
            ],
            'options' => [
                'label' => 'Options',
                'deletion' => [
                    'confirm' => 'Please confirm the deletion of the selected applications!',
                    'btn' => [
                        'label' => 'Delete',
                    ],
                ],
                'edit' => [
                    'btn' => [
                        'label' => 'Edit',
                    ],
                ],
            ],
            'search' => [
                'label' => 'Search applications',
            ],
            'tab' => [
                'header' => [
                    'from' => 'Date',
                    'name' => 'Name',
                ],
            ],
        ],
    ],
    'application' => [
        'errors' => [
            'name-required' => 'Please enter the last name',
        ],
    ],
    'index' => [
        'search-placeholder' => 'Search',
    ],
    'create' => [
        'title' => 'Create member',
        'message' => [
            'success' => 'Member created successfully',
            'fail' => 'Member could not be created. Ask admin for log entries!',
        ],
    ],
    'backend' => [
        'cancel' => [
            'success' => [
                'head' => 'Membership cancelled',
                'msg' => 'The membership has been cancelled successfully.',
            ],
            'forbidden' => [
                'head' => 'No permission',
                'msg' => 'You are not authorized to cancel this membership. (:error)',
            ],
            'modal' => [
                'title' => 'Cancel membership',
                'subtitle' => 'Cancel membership of :name. This action cannot be undone.',
                'date_label' => 'Date of leaving',
                'confirm' => 'Cancel now',
            ],
        ],

        'pseudonymize' => [
            'success' => [
                'head' => 'Member pseudonymized',
                'msg' => 'The member\'s data has been pseudonymized successfully.',
            ],
            'forbidden' => [
                'head' => 'No permission',
                'msg' => 'You are not authorized to pseudonymize this member. (:error)',
            ],
            'modal' => [
                'title' => 'Pseudonymize member',
                'subtitle' => 'All personal data of :name will be irrevocably deleted.',
                'confirm' => 'Pseudonymize now',
            ],
            'scheduled' => [
                'head' => 'Automatic pseudonymization',
                'msg' => ':count member(s) have been pseudonymized.',
            ],
        ],
        'create' => [
            'heading' => 'Create new member',
            'btn' => [
                'submit' => 'Save member',
            ],
        ],
        'form' => [
            'no-user-found' => 'No user found',
        ],
        'attach' => [
            'failed' => [
                'head' => 'Error',
                'msg' => 'User could not be linked.',
            ],
        ],
        'invitation' => [
            'sent' => [
                'head' => 'Success',
                'msg' => 'Invitation has been sent.',
            ],
            'failed' => [
                'head' => 'Error',
                'msg' => 'Invitation was not sent: :error',
            ],
        ],
        'application' => [
            'accepted' => [
                'head' => 'Success',
                'msg' => 'Membership has been accepted.',
            ],
        ],
        'delete' => [
            'success' => [
                'head' => 'Success',
                'msg' => 'Membership has been cancelled.',
            ],
            'user_deleted' => [
                'msg' => 'User has been deleted.',
            ],
            'user_failed' => [
                'msg' => 'Error deleting user :id.',
            ],
        ],

        'reactivate' => [
            'success' => [
                'head' => 'Success',
                'msg' => 'Membership has been restored.',
            ],
        ],
    ],
    'fees' => [
        'overview_title' => 'Membership fees overview',
        'year' => 'Year',
        'search_member_placeholder' => 'Search member...',
        'show_inactive' => 'Show inactive',
        'pdf_export' => 'PDF export',
        'csv_export' => 'CSV export',
        'members' => 'Members',
        'paid' => 'Paid',
        'open' => 'Open',
        'transactions' => 'Transactions',
        'payments' => 'Payments',
        'member' => 'Member',
        'type' => 'Type',
        'date' => 'Date',
        'status' => 'Status',
        'receipt' => 'Receipt',
        'status_booked' => 'booked',
        'status_submitted' => 'submitted',
        'send' => 'Send',
    ],
    'documents' => [

        'btn' => [
            'upload' => 'Upload document',
            'save' => 'Save',
            'download' => 'Download',
            'cancel' => 'Cancel',
        ],

        'upload' => [
            'title' => 'Upload new document',
            'file_label' => 'File (PDF, JPG, PNG, TIF)',
            'notes_label' => 'Note (optional)',
        ],

        'category' => [
            'label' => 'Category',
            'placeholder' => 'Choose category…',
            'membership_form' => 'Membership application',
            'sepa' => 'SEPA direct debit mandate',
            'privacy' => 'Privacy policy',
            'id_document' => 'ID document',
            'other' => 'Other',
        ],

        'table' => [
            'name' => 'Filename',
            'category' => 'Category',
            'size' => 'Size',
            'uploaded_by' => 'Uploaded by',
            'last_accessed' => 'Last accessed',
            'actions' => 'Actions',
        ],

        'confirm' => [
            'delete' => 'Really delete document? This action cannot be undone.',
        ],

        'upload_success' => 'The document has been uploaded successfully.',
        'delete_success' => 'The document has been deleted.',
        'empty' => 'No documents have been saved for this member yet.',

        'errors' => [
            'unauthorized' => 'You do not have permission for this action.',
            'upload_failed' => 'An error occurred while uploading. Please try again.',
            'file_not_found' => 'The file was not found in storage.',
            'invalid_file_type' => 'Only PDF, JPG, PNG and TIF/TIFF are allowed.',
            'file_too_large' => 'The file may not exceed 10 MB.',
            'mime_not_allowed_for_category' => 'This file type is not allowed for the selected category.',
        ],

    ],
    'export' => [
        'title' => 'Export members',
        'description' => 'Select the export type and desired filters. The download will start after clicking the button.',
        'type_label' => 'Export type',
        'filter_label' => 'Filter',
        'preview_count' => 'Members matching filter criteria',
        'btn_download' => 'Download',
        'btn_download_empty' => 'No members found',
        'btn_label' => 'Export',
        'type' => [
            'stammdaten' => 'Master data',
            'stammdaten_desc' => 'Name, address, contact details',
            'members_all' => 'All member data',
            'members_all_desc' => 'All fields including roles, fee type and membership status',
            'full' => 'Full export (ZIP)',
            'full_desc' => 'All data + attached documents as ZIP archive',
        ],

        'filter' => [
            'only_active' => 'Only active members (no leaving date)',
            'include_pseudonymized' => 'Include pseudonymized members',
            'member_types' => 'Member types',
        ],
    ],
    'import' => [
        'btn_label' => 'Import',
        'page_title' => 'Import members',
        'mail' => [
            'subject' => 'Member import completed',
            'heading' => 'Import completed',
            'greeting' => 'Hello :name,',
            'intro' => 'The member import from :date has been completed successfully.',
            'imported' => 'Imported',
            'skipped' => 'Skipped (duplicates)',
            'errors' => 'Errors',
            'duration' => 'Duration',
            'error_details' => 'Error details',
            'error_row' => 'Row :row',
            'backup_info' => 'A backup of the member data was created before the import.',
            'backup_download' => 'Download backup',
            'backup_expiry' => 'The download link is valid for 24 hours.',
            'footer' => 'If you have any questions, please contact the administrator.',
            'failed_subject' => 'Member import failed',
            'failed_heading' => 'Import failed',
            'failed_greeting' => 'Hello :name,',
            'failed_intro' => 'The member import could not be completed.',
            'failed_footer' => 'Please check the ZIP file and try again.',

        ],
        'title' => 'Import members',
        'description' => 'Import member data from a CSV or ZIP file.',
        'btn_back' => 'Back',
        'btn_cancel' => 'Cancel',

        'upload' => [
            'title' => 'Upload file',
            'description' => 'Select the import type and upload the corresponding file.',
            'type_label' => 'Import type',
            'file_label_csv' => 'Select CSV file',
            'file_label_zip' => 'Select ZIP file',
            'zip_hint' => 'ZIP files are verified for authenticity (checksum). Only exports from CommuCore are accepted.',
            'error_heading' => 'Error reading file',
            'btn_upload' => 'Read file',
            'btn_uploading' => 'Reading…',
            'dropzone_heading_csv' => 'Drop CSV file here or click',
            'dropzone_heading_zip' => 'Drop ZIP file here or click',
            'remove_file' => 'Remove file',
            'zip_async_hint' => 'ZIP imports are processed in the background. You will receive an email when the import is complete.',
            'zip_job_dispatched' => 'Import started',
            'zip_job_description' => 'The ZIP file is being processed in the background. You will receive an email once the import is complete.',
            'template_hint' => 'No file yet? Download an empty template:',
            'template_download' => 'Download CSV template',
        ],

        'mapping' => [
            'title' => 'Map fields',
            'description' => 'Map the CSV columns to CommuCore fields.',
            'col_csv' => 'CSV column',
            'col_commucore' => 'CommuCore field',
            'fields_mapped' => 'Fields mapped',
            'btn_confirm' => 'Confirm mapping',
            'enum_modal_title' => 'Map unknown values',
            'enum_modal_description' => 'The following values could not be automatically mapped. Please map them manually or select "Skip".',
            'enum_skip' => 'Skip',
            'enum_modal_confirm' => 'Apply mapping',
        ],

        'preview' => [
            'title' => 'Preview & Backup',
            'description' => ':total rows found, :duplicates duplicates detected.',
            'total_rows' => 'Total rows',
            'new_rows' => 'New',
            'duplicate_rows' => 'Duplicates',
            'duplicate' => 'Duplicate',
            'new' => 'New',
            'more_rows' => '… and :count more rows',
            'backup_required' => 'Backup required',
            'backup_description' => 'A backup of the current member data will be created automatically before the import.',
            'backup_created' => 'Backup created',
            'backup_download' => 'Download backup',
            'btn_backup' => 'Create backup & continue',
            'btn_backup_loading' => 'Creating backup…',
            'btn_continue' => 'Start import',
        ],

        'log' => [
            'skipped' => [
                'label' => 'Skipped',
                'duplicate' => 'Duplicate',
                'error' => 'Error',
            ],
            'completed' => [
                'label' => 'Import completed',
            ],
        ],

        'import' => [
            'title' => 'Perform import',
            'description' => ':count members will be imported.',
            'warning_heading' => 'Warning',
            'warning_text' => 'The import cannot be automatically undone. Rollback is only possible via the created backup.',
            'confirm' => 'Really start import?',
            'btn_start' => 'Import :count members',
            'in_progress' => 'Import in progress…',
            'success_heading' => 'Import completed successfully',
            'btn_finish' => 'Finish',
            'rollback_confirm' => 'Really perform rollback? All imported data will be deleted.',
            'btn_rollback' => 'Perform rollback',
            'btn_rolling_back' => 'Rollback in progress…',
        ],
    ],
    'status' => [
        'active' => 'Active',
        'inactive' => 'Left',
    ],
];
