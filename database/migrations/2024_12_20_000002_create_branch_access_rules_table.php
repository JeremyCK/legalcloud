<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_access_rules', function (Blueprint $table) {
            $table->id();
            $table->string('role');                    // 'maker', 'sales', 'lawyer', etc.
            $table->integer('user_id')->nullable();     // Specific user ID (for sales role)
            $table->integer('branch_id')->nullable();   // Specific branch ID
            $table->json('accessible_branches');        // Array of accessible branch IDs
            $table->string('rule_type');               // 'user_specific', 'branch_specific', 'role_default'
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();    // Human-readable description
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['role', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index(['branch_id', 'is_active']);
        });
        
        // Insert default rules based on current hardcoded logic
        $this->insertDefaultRules();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_access_rules');
    }
    
    /**
     * Insert default branch access rules
     */
    private function insertDefaultRules(): void
    {
        $rules = [
            // Maker role - Branch 3 can only access itself
            [
                'role' => 'maker',
                'user_id' => null,
                'branch_id' => 3,
                'accessible_branches' => json_encode([3]),
                'rule_type' => 'branch_specific',
                'description' => 'Maker from branch 3 can only access branch 3',
            ],
            
            // Maker role - Branches 5 and 6 can access each other
            [
                'role' => 'maker',
                'user_id' => null,
                'branch_id' => 5,
                'accessible_branches' => json_encode([5, 6]),
                'rule_type' => 'branch_specific',
                'description' => 'Maker from branch 5 can access branches 5 and 6',
            ],
            [
                'role' => 'maker',
                'user_id' => null,
                'branch_id' => 6,
                'accessible_branches' => json_encode([5, 6]),
                'rule_type' => 'branch_specific',
                'description' => 'Maker from branch 6 can access branches 5 and 6',
            ],
            
            // Sales role - Special user access
            [
                'role' => 'sales',
                'user_id' => 51,
                'branch_id' => null,
                'accessible_branches' => json_encode([5, 6]),
                'rule_type' => 'user_specific',
                'description' => 'Sales user 51 can access branches 5 and 6',
            ],
            [
                'role' => 'sales',
                'user_id' => 32,
                'branch_id' => null,
                'accessible_branches' => json_encode([5, 6]),
                'rule_type' => 'user_specific',
                'description' => 'Sales user 32 can access branches 5 and 6',
            ],
            [
                'role' => 'sales',
                'user_id' => 13,
                'branch_id' => null,
                'accessible_branches' => json_encode([13]),
                'rule_type' => 'user_specific',
                'description' => 'Sales user 13 can only access their own branch',
            ],
        ];
        
        foreach ($rules as $rule) {
            DB::table('branch_access_rules')->insert($rule);
        }
    }
};
