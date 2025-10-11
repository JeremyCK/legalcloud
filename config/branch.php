<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Branch Access Rules Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all branch access rules for different user roles.
    | Rules are organized by role and can be easily modified without code changes.
    |
    */

    'access_rules' => [
        'maker' => [
            // Special branch combinations (e.g., branches 5 and 6 can access each other)
            'special_combinations' => [
                5 => [5, 6],  // Branch 5 can access branches 5 and 6
                6 => [5, 6],  // Branch 6 can access branches 5 and 6
            ],
            
            // Specific branch rules
            'specific_branches' => [
                3 => [3],      // Branch 3 can only access itself
                // Add more specific rules as needed
            ],
        ],
        
        'sales' => [
            // Special user IDs with specific branch access
            'special_users' => [
                51 => [5, 6],  // User 51 can access branches 5 and 6
                32 => [5, 6],  // User 32 can access branches 5 and 6
                13 => [13],    // User 13 can only access their own branch
            ],
        ],
        
        'lawyer' => [
            // Lawyers typically only access their own branch
            'default_behavior' => 'own_branch_only',
        ],
        
        'admin' => [
            // Admins can access all branches
            'default_behavior' => 'all_branches',
        ],
        
        'account' => [
            // Account users can access all branches
            'default_behavior' => 'all_branches',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Branch Management Settings
    |--------------------------------------------------------------------------
    |
    | General settings for branch management
    |
    */
    
    'cache' => [
        'user_access_ttl' => 300,        // 5 minutes
        'all_branches_ttl' => 3600,      // 1 hour
    ],
    
    'defaults' => [
        'branch_column' => 'branch_id',
        'fallback_behavior' => 'own_branch_only',
    ],
];
