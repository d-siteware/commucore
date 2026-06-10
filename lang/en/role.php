<?php

declare(strict_types=1);

return [

    'page' => [
        'title' => 'Roles of :name',
        'heading' => 'Available roles',
    ],

    'leadership' => [
        'btn_add' => 'Add new leadership position',
        'empty_member_list' => 'No members found',
        'empty_roles_list' => 'No roles found',
    ],

    'create' => [
        'form' => [
            'header' => 'Assign leadership function',
            'select_member.label' => 'Select member',
            'select_role.label' => 'Assign role',
            'title' => 'Assign role',
            'btn_add_new_role' => [
                'label' => 'New',
            ],
            'option_add_new_role' => 'Create new role',
            'option_select_role' => 'Select role',
            'profile_image' => 'Profile image',
            'designated_at' => 'Appointed on',
            'designated_at.placeholder' => 'Date',
            'about_me' => 'About me',
            'btn_add_member' => 'Assign role to member',
            'btn_update_member' => 'Update role',
        ],
        'modal' => [
            'title' => 'Create new role',
            'name' => 'Name',
            'description' => 'Description',
            'can_manage_accounting' => 'Can manage accounts',
            'can_audit_accounting' => 'Can audit accounting',
            'can_represent_organization' => 'Is authorized to represent',
            'button' => 'Save',
        ],
    ],

    'validation' => [
        'error_required' => [
            'role_id' => 'Please select a role',
            'member_id' => 'Please select a member',
            'designated_at' => 'The date of appointment is required',
        ],
    ],

    'toast' => [
        'msg' => [
            'leaderrole' => [
                'updated' => 'Data has been updated successfully',
                'revoked' => 'Role has been revoked successfully',
                'assigened' => 'The role has been assigned to the member',

            ],
        ],
    ],

];
