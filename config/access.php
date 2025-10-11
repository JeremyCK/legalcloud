<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Unified Access Control Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all access control rules for the application.
    | Rules are organized by type and can be easily modified without code changes.
    |
    */

    'branch_rules' => [
        'role_based' => [
            'admin' => [
                'default_behavior' => 'all_branches',
                'standalone' => false,
            ],
            'management' => [
                'default_behavior' => 'all_branches',
                'standalone' => false,
            ],
            'account' => [
                'default_behavior' => 'all_branches',
                'standalone' => false,
            ],
            'sales' => [
                'standalone' => false,
                'hq_branches' => [1, 2],
                'special_combinations' => [
                    5 => [5, 6],
                    6 => [5, 6],
                ],
            ],
            'receptionist' => [
                'standalone' => false,
                'hq_branches' => [1, 2],
            ],
            'chambering' => [
                'standalone' => false,
                'hq_branches' => [1, 2],
            ],
            'lawyer' => [
                'standalone' => false,
                'special_combinations' => [
                    1 => [1, 2, 3, 4, 6],
                    4 => [1, 2, 3, 4, 6],
                    6 => [1, 2, 3, 4, 6],
                ],
            ],
            'clerk' => [
                'standalone' => false,
                'special_combinations' => [
                    1 => [1, 2, 3, 4, 6],
                    4 => [1, 2, 3, 4, 6],
                    6 => [1, 2, 3, 4, 6],
                ],
            ],
            'maker' => [
                'standalone' => false,
                'special_combinations' => [
                    5 => [5, 6],
                    6 => [5, 6],
                ],
                'specific_branches' => [
                    3 => [3],
                ],
            ],
        ],
        
        'user_specific' => [
            // User 32 - Special access to branch 5
            32 => [
                'branches' => [5],
                'standalone' => false,
            ],
            // User 90 - Access to branches 1, 5, 6
            90 => [
                'branches' => [1, 5, 6],
                'standalone' => false,
            ],
            // User 22 - Access to own branch and branch 1
            22 => [
                'branches' => [1],
                'standalone' => false,
            ],
            // User 94 - Access to own branch and branch 1
            94 => [
                'branches' => [1],
                'standalone' => false,
            ],
            // User 80 - Access to branches 4, 6, 1
            80 => [
                'branches' => [4, 6, 1],
                'standalone' => false,
            ],
            // User 106, 110, 119 - Access to branches 5, 6
            106 => ['branches' => [5, 6], 'standalone' => false],
            110 => ['branches' => [5, 6], 'standalone' => false],
            119 => ['branches' => [5, 6], 'standalone' => false],
            // User 112, 113, 120 - Access to branches 1, 5, 6
            112 => ['branches' => [1, 5, 6], 'standalone' => false],
            113 => ['branches' => [1, 5, 6], 'standalone' => false],
            120 => ['branches' => [1, 5, 6], 'standalone' => false],
            // User 13 - Access to branch 2
            13 => ['branches' => [2], 'standalone' => false],
            // User 29, 105 - Access to branches 1, 2, 4, 6
            29 => ['branches' => [1, 2, 4, 6], 'standalone' => false],
            105 => ['branches' => [1, 2, 4, 6], 'standalone' => false],
            // User 93, 139 - Access to all branches
            93 => ['branches' => [1, 2, 3, 4, 5, 6], 'standalone' => false],
            139 => ['branches' => [1, 2, 3, 4, 5, 6], 'standalone' => false],
            // User 49 - Access to branches 1, 2, 4, 5, 6
            49 => ['branches' => [1, 2, 4, 5, 6], 'standalone' => false],
            // User 72 - Access to branches 1, 2
            72 => ['branches' => [1, 2], 'standalone' => false],
            // User 116 - Access to own branch, 6, 1
            116 => ['branches' => [6, 1], 'standalone' => false],
            // User 64, 144 - Access to own branch, 4, 6
            64 => ['branches' => [4, 6], 'standalone' => false],
            144 => ['branches' => [4, 6], 'standalone' => false],
            // User 138 - Access to own branch and 1
            138 => ['branches' => [1], 'standalone' => false],
            // User 188 - Access only to own branch
            188 => ['branches' => [], 'standalone' => true],
            // User 38, 21, 25 - Access to own branch and 1
            38 => ['branches' => [1], 'standalone' => false],
            21 => ['branches' => [1], 'standalone' => false],
            25 => ['branches' => [1], 'standalone' => false],
            // User 103 - Access to own branch, 1, 2, 6
            103 => ['branches' => [1, 2, 6], 'standalone' => false],
            // User 104, 121, 143 - Access to own branch and 6
            104 => ['branches' => [6], 'standalone' => false],
            121 => ['branches' => [6], 'standalone' => false],
            143 => ['branches' => [6], 'standalone' => false],
        ],
    ],

    'user_rules' => [
        'role_based' => [
            'admin' => [
                'default_behavior' => 'all_users',
            ],
            'management' => [
                'default_behavior' => 'all_users',
            ],
            'account' => [
                'default_behavior' => 'all_users',
            ],
            'sales' => [
                'default_behavior' => 'own_branch_users',
            ],
            'receptionist' => [
                'default_behavior' => 'own_branch_users',
            ],
            'chambering' => [
                'default_behavior' => 'own_branch_users',
            ],
            'lawyer' => [
                'default_behavior' => 'own_branch_users',
            ],
            'clerk' => [
                'default_behavior' => 'own_branch_users',
            ],
            'maker' => [
                'default_behavior' => 'own_branch_users',
            ],
        ],
        
        'user_specific' => [
            // User 103 - Can access users 103, 90
            103 => ['accessible_users' => [103, 90]],
            // User 5, 39, 137 - Can access only themselves
            5 => ['accessible_users' => [5]],
            39 => ['accessible_users' => [39]],
            137 => ['accessible_users' => [137]],
            // User 63 - Can access users 63, 79
            63 => ['accessible_users' => [63, 79]],
            // User 32 - Can access multiple users
            32 => ['accessible_users' => [32, 143, 127, 119, 118, 90, 110, 112, 113]],
            // User 106 - Can access users 90, 106
            106 => ['accessible_users' => [90, 106]],
            // User 38 - Can access only themselves
            38 => ['accessible_users' => [38]],
            // User 90 - Can access users 90, 106
            90 => ['accessible_users' => [90, 106]],
            // User 112 - Can access users 11, 112, 90, 118
            112 => ['accessible_users' => [11, 112, 90, 118]],
            // User 110 - Can access users 32, 90
            110 => ['accessible_users' => [32, 90]],
            // User 127, 118 - Can access multiple users
            127 => ['accessible_users' => [127, 143, 119, 32, 90, 110, 112, 113]],
            118 => ['accessible_users' => [118, 143, 119, 32, 90, 110, 112, 113]],
            // User 14 - Can access all users (Moon)
            14 => ['accessible_users' => []], // Empty array means all users
            // User 13 - Can access users 13, 141, 63, 79
            13 => ['accessible_users' => [13, 141, 63, 79]],
            // User 80 - Can access users 80, 89, 116, 138
            80 => ['accessible_users' => [80, 89, 116, 138]],
            // User 29 - Can access only themselves
            29 => ['accessible_users' => [29]],
            // User 104 - No specific user access
            104 => ['accessible_users' => []],
            // User 102, 136 - Can access users 80, 89, 116, 138
            102 => ['accessible_users' => [80, 89, 116, 138]],
            136 => ['accessible_users' => [80, 89, 116, 138]],
            // User 115 - No specific user access
            115 => ['accessible_users' => []],
            // User 120, 143 - Can access users 118, 143, 90
            120 => ['accessible_users' => [118, 143, 90]],
            143 => ['accessible_users' => [118, 143, 90]],
            // User 111 - Can access user 89
            111 => ['accessible_users' => [89]],
            // User 119 - Can access users 90, 110, 106, 118, 32
            119 => ['accessible_users' => [90, 110, 106, 118, 32]],
            // User 121 - Can access users 13, 141
            121 => ['accessible_users' => [13, 141]],
            // User 49 - No specific user access
            49 => ['accessible_users' => []],
            // User 93, 139 - No specific user access
            93 => ['accessible_users' => []],
            139 => ['accessible_users' => []],
            // User 113 - Can access users 103, 90
            113 => ['accessible_users' => [103, 90]],
            // User 125 - Can access only themselves
            125 => ['accessible_users' => [125]],
            // User 126 - Can access users 126, 125
            126 => ['accessible_users' => [126, 125]],
            // User 147 - Can access users 147, 125
            147 => ['accessible_users' => [147, 125]],
            // User 140 - Can access users 140, 126
            140 => ['accessible_users' => [140, 126]],
            // User 122 - Can access users 122, 142
            122 => ['accessible_users' => [122, 142]],
            // User 142 - Can access users 142, 122
            142 => ['accessible_users' => [142, 122]],
            // User 144 - Can access users 144, 29
            144 => ['accessible_users' => [144, 29]],
        ],
    ],

    'case_rules' => [
        'user_specific' => [
            // User 5, 39, 137 - Can access cases 3632, 3633
            5 => ['accessible_cases' => [3632, 3633]],
            39 => ['accessible_cases' => [3632, 3633]],
            137 => ['accessible_cases' => [3632, 3633]],
            // User 38 - Can access case 3667
            38 => ['accessible_cases' => [3667]],
            // User 29 - Can access cases 1280, 1281, 2956
            29 => ['accessible_cases' => [1280, 1281, 2956]],
            // User 119 - Can access cases 2670, 2743
            119 => ['accessible_cases' => [2670, 2743]],
            // User 121 - Can access case 2674
            121 => ['accessible_cases' => [2674]],
        ],
    ],

    'permission_rules' => [
        'default_deny' => true, // Default to deny unless explicitly allowed
        'cache_ttl' => 300, // 5 minutes cache
        'evaluation_order' => [
            'exclusive_branch',
            'exclude_branch', 
            'role_based',
            'branch_based',
            'user_based',
        ],
    ],

    'cache' => [
        'enabled' => true,
        'ttl' => [
            'user_access_info' => 300, // 5 minutes
            'branch_access' => 600,    // 10 minutes
            'permission_access' => 300, // 5 minutes
        ],
        'tags' => [
            'user_access',
            'branch_access',
            'permission_access',
        ],
    ],

    'logging' => [
        'enabled' => true,
        'level' => 'info',
        'log_access_denials' => true,
        'log_permission_checks' => false,
    ],
];
