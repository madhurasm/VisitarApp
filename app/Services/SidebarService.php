<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SidebarService
{
    /**
     * Get the menu items for the admin sidebar.
     *
     * @return array
     */
    public function getAdminSidebarMenu()
    {
        // Get the current user (you may also consider using policies or permissions)
        $user = Auth::user();

        // Sample logic to dynamically generate menu items based on user role or permissions

        if ($user && $user->type == 'user'){
            $menu = [
                [
                    'route' => route('admin.dashboard'),
                    'name' => __('Dashboard'),
                    'icon' => 'bx bx-home-circle',
                    'child' => [],
                    'all_routes' => ['admin.dashboard'],
                ]
            ];
        }

        if ($user && $user->type == 'entity'){
            $menu = [
                [
                    'route' => route('admin.dashboard'),
                    'name' => __('Dashboard'),
                    'icon' => 'bx bx-home-circle',
                    'child' => [],
                    'all_routes' => ['admin.dashboard'],
                ],
                [
                    'route' => route('admin.users.index'),
                    'name' => __('Receptionist'),
                    'icon' => 'bx bx-user-circle',
                    'child' => [],
                    'all_routes' => ['admin.user.index', 'admin.user.show', 'admin.user.add'],
                ],
                [
                    'route' => route('admin.hosts.index'),
                    'name' => __('Hosts'),
                    'icon' => 'bx bx-user-pin',
                    'child' => [],
                    'all_routes' => ['admin.host.index', 'admin.host.show', 'admin.host.add'],
                ],
//            [
//                'route' => route('admin.contents.index'),
//                'name' => __('Contents'),
//                'icon' => 'bx bx-detail',
//                'child' => [],
//                'all_routes' => ['admin.contents.index', 'admin.contents.show'],
//            ],
            ];
        }

        // Example: Add a "Settings" item only for users with admin privileges
        if ($user && $user->type == 'admin') {
            $menu[] = [
                'route' => 'javascript:;',
                'name' => __('Entity Management'),
                'icon' => 'bx bx-briefcase',
                'child' => [
                    [
                        'route' => route('admin.entity.index'),
                        'name' => __('Entities'),
                        'icon' => '',
                        'all_routes' => ['admin.entity.index', 'admin.entity.show', 'admin.entity.add'],
                    ],
                    [
                        'route' => route('admin.entity-sites.index'),
                        'name' => __('Sites'),
                        'icon' => '',
                        'all_routes' => ['admin.entity-sites.index', 'admin.entity-sites.add'],
                    ],
                ],
                'all_routes' => ['admin.entity.index', 'admin.entity.show', 'admin.entity.add', 'admin.entity-sites.index', 'admin.entity-sites.add'],
            ];
        }

        if ($user && $user->type == 'entity') {
            $menu[] = [
                'route' => route('admin.entity-sites.index'),
                'name' => __('Sites'),
                'icon' => 'bx bx-map',
                'child' => [],
                'all_routes' => ['admin.entity-sites.index', 'admin.entity-sites.edit'],
            ];
        }

        if ($user && ($user->type == 'entity' || $user->type == 'admin')) {
            $menu[] = [
                'route' => 'javascript:;',
                'name' => __('Personal Settings'),
                'icon' => 'bx bxs-badge-check',
                'child' => [
                    [
                        'route' => route('admin.change-password'),
                        'name' => __('Change Password'),
                        'icon' => '',
                        'all_routes' => ['admin.change-password'],
                    ],
                    [
                        'route' => route('admin.profile'),
                        'name' => __('Profile'),
                        'icon' => '',
                        'all_routes' => ['admin.profile'],
                    ],
                ],
                'all_routes' => ['admin.change-password', 'admin.profile'],
            ];
        }

        if ($user && $user->type == 'admin'){
            $menu[] = [
                'route' => 'javascript:;',
                'name' => __('General Settings'),
                'icon' => 'bx bx-cog',
                'child' => [
                    [
                        'route' => route('admin.site-settings'),
                        'name' => __('Site Settings'),
                        'icon' => '',
                        'all_routes' => ['admin.site-settings'],
                    ],
                    [
                        'route' => route('admin.contents.index'),
                        'name' => __('Contents'),
                        'icon' => '',
                        'all_routes' => ['admin.contents.index', 'admin.contents.show'],
                    ],
//                    [
//                        'route' => route('admin.version-settings'),
//                        'name' => __('Version Settings'),
//                        'icon' => '',
//                        'all_routes' => ['admin.version-settings'],
//                    ],
//                    [
//                        'route' => route('admin.credentials'),
//                        'name' => __('Credentials'),
//                        'icon' => '',
//                        'all_routes' => ['admin.credentials'],
//                    ],
                ],
                'all_routes' => ['admin.version-settings', 'admin.site-settings', 'admin.credentials'],
            ];
        }elseif ($user && $user->type == 'entity'){
            $menu[] = [
                'route' => 'javascript:;',
                'name' => __('General Settings'),
                'icon' => 'bx bx-cog',
                'child' => [
                    [
                        'route' => route('admin.site-settings'),
                        'name' => __('Entity Settings'),
                        'icon' => '',
                        'all_routes' => ['admin.site-settings'],
                    ],
                    [
                        'route' => route('admin.contents.index'),
                        'name' => __('Contents'),
                        'icon' => '',
                        'all_routes' => ['admin.contents.index', 'admin.contents.show'],
                    ],
//                    [
//                        'route' => route('admin.version-settings'),
//                        'name' => __('Version Settings'),
//                        'icon' => '',
//                        'all_routes' => ['admin.version-settings'],
//                    ],
//                    [
//                        'route' => route('admin.credentials'),
//                        'name' => __('Credentials'),
//                        'icon' => '',
//                        'all_routes' => ['admin.credentials'],
//                    ],
                ],
                'all_routes' => ['admin.version-settings', 'admin.site-settings', 'admin.credentials'],
            ];
        }

        // Add a logout option at the end of the menu
        $menu[] = [
            'route' => route('admin.logout'),
            'name' => __('Logout'),
            'icon' => 'bx bx-power-off',
            'child' => [],
            'all_routes' => [],
        ];

        return $menu;
    }
}
