<?php

declare(strict_types=1);

return [
    'index' => [
        'page' => [
            'title' => 'Published articles overview',
        ],
    ],

    'type' => [
        'label' => 'Article type',
        'review' => 'Review',
        'other' => 'Other',
        'announcement' => 'Announcement',
        'report' => 'Report',
    ],

    'backend' => [
        'index' => [
            'page' => [
                'title' => 'Articles overview',
            ],
            'btn' => [
                'start_new' => 'New article',
            ],
        ],
    ],

    'body' => 'Content',
    'user_id' => 'User / Author',
    'status' => 'Publication status',
    'label' => 'Internal identifier/title',
    'title' => 'Title',
    'slug' => 'Slug',

    'create' => [
        'page' => [
            'title' => 'Create new article',
        ],
        'btn' => [
            'submit' => 'Create article',
        ],
        'success' => [
            'title' => 'Article created',
            'msg' => 'The article has been created successfully.',
        ],
        'steps' => [
            'head' => 'Header data',
            'content' => 'Content',
            'images' => 'Images',
        ],
        'title_explanation' => 'The title will become the heading of the article and will also be used as a list entry in overviews. It should not be much longer than 100 characters and should not be repeated as a heading in the body text.',
        'slug_explanation' => 'The slug serves as the link to the article. Ideally, it should be the title without spaces or special characters. Clicking (generate slug) will do this for both titles. IMPORTANT: after publication of the article, the slug should only be changed in an emergency.',
        'page_title' => 'Create new article',
        'images_upload_explanation' => 'Upload images to be displayed as a gallery.',
    ],

    'images' => [
        'existing' => 'The following images are linked to the article',
        'no_existing' => 'No images found for this article',
        'upload_explanation' => 'Each article can contain multiple images. In this form, images can be uploaded. Please provide a description and the author of the image, if known.',
        'preview' => 'Preview of uploaded images',
        'image_filename' => 'Image name',
        'image_caption' => 'Description',
        'image_author' => 'Author',
        'image_btn_remove' => 'Remove',
        'empty_list' => 'No images selected',
        'btn' => [
            'upload' => 'Upload images',
            'remove' => 'Remove image',
        ],
        'upload' => 'Upload images',
        'dropzone' => [
            'heading' => 'Drop images here or click to select',
            'text' => 'JPG, PNG, WebP, GIF up to 20 MB',
        ],
    ],

    'section' => [
    'images' => [
        'gallery' => 'Image gallery',
            'header' => 'Upload new image',
        ],
    ],

    'form' => [
        'toasts' => [
            'msg' => [
                'image_removed' => 'Image removed successfully!',
                'post_published' => 'The article has been published!',
                'post_retracted' => 'The article has been retracted!',
            ],
            'heading' => [
                'success' => 'Success!',
                'warning' => 'Warning!',
                'error' => 'Error!',
            ],
            'create_success' => 'The article with :num images has been updated successfully!',
            'edit_success' => 'The article with :num images has been updated successfully!',
            'notifications_sent_success' => 'Publication notifications have been sent',
            'notification_sent_success' => 'Publication notifications have been sent',
            'eventDetachedSuccess' => 'The link to the article has been removed',
            'eventAtachedSuccess' => 'The link to the article has been created',
        ],
    ],

    'show' => [
        'title' => 'Edit article',
        'tabs' => [
            'header' => [
                'main' => 'Header data',
                'content' => 'Content',
                'images' => 'Images',
            ],
        ],
        'tab' => [
            'main' => [
                'btn_make_slug' => 'Create slug',
                'published' => [
                    'header' => 'Article is published',
                    'status_msg' => 'This article was published on :datum.',
                    'btn_reset' => 'Retract',
                    'confirmation_msg' => 'Please confirm that the article should be retracted. It will then no longer be visible on the public part of the site.',
                    'btn_sendMails' => 'Send newsletter',
                    'btn_publish_now' => 'Publish article now',
                ],
                'attached_event' => [
                    'header' => 'Article is linked',
                    'status_msg' => 'This article was published in conjunction with the event :title.',
                ],
                'detach_from_event' => [
                    'confirmation_msg' => 'Please confirm that the article should be detached from the event.',
                ],
                'detach' => [
                    'btn_reset' => 'Detach link',
                ],
                'event' => [
                    'btn_connect_now' => 'Link article to event now',
                ],
            ],
        ],
        'btn' => [
            'save' => 'Save',
        ],
        'label' => [
            'created_at' => 'Created',
            'updated_at' => 'Last modified',
        ],
        'delete' => [
            'confirm_prompt' => 'The article is published. Please confirm deletion. The article and all images will be lost!',
        ],
    ],

    'notification_mail' => [
        'subject' => 'New article published on our website!',
        'header_subscriber' => 'Freshly published: A new article for you',
        'header_member' => 'Freshly published: A new article for you',
        'content_member' => 'we have exciting news for you! A brand new article has just been published on our website – check it out!',
        'content_subscriber' => 'we have exciting news for you – a new article has just been published on our website! Check it out:',
        'btn_link_label' => 'read more',
        'btn_unsubscribe_link_label' => 'You are receiving this email because you subscribed to our updates. Would you like to change your settings or unsubscribe? Click here:',
        'content' => [
            'excerpt' => [
                'header' => 'Preview',
            ],
        ],
    ],

    '' => '',
];
