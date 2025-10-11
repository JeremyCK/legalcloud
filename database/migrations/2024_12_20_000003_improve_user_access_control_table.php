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
        // First, create a backup of existing data
        $this->backupExistingData();
        
        // Modify the existing table structure
        Schema::table('user_access_control', function (Blueprint $table) {
            // Change longtext fields to JSON for better performance and validation
            $table->json('user_id_list')->change();
            $table->json('branch_id_list')->change();
            $table->json('role_id_list')->change();
            $table->json('exclusive_branch_list')->change();
            $table->json('exclude_branch_list')->change();
            $table->json('exclude_user_list')->change();
            
            // Add indexes for better performance
            $table->index(['code', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index(['role_id', 'status']);
            
            // Add JSON indexes for MySQL 5.7+
            try {
                DB::statement('ALTER TABLE user_access_control ADD INDEX idx_user_id_list ((CAST(user_id_list AS CHAR(100))))');
                DB::statement('ALTER TABLE user_access_control ADD INDEX idx_branch_id_list ((CAST(branch_id_list AS CHAR(100))))');
                DB::statement('ALTER TABLE user_access_control ADD INDEX idx_role_id_list ((CAST(role_id_list AS CHAR(100))))');
            } catch (\Exception $e) {
                // Indexes might not be supported or already exist, continue
            }
        });
        
        // Restore data with proper JSON format
        $this->restoreDataWithJsonFormat();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert JSON fields back to longtext
        Schema::table('user_access_control', function (Blueprint $table) {
            $table->longText('user_id_list')->change();
            $table->longText('branch_id_list')->change();
            $table->longText('role_id_list')->change();
            $table->longText('exclusive_branch_list')->change();
            $table->longText('exclude_branch_list')->change();
            $table->longText('exclude_user_list')->change();
            
            // Drop indexes
            $table->dropIndex(['code', 'status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['branch_id', 'status']);
            $table->dropIndex(['role_id', 'status']);
        });
        
        // Drop JSON indexes if they exist
        try {
            DB::statement('ALTER TABLE user_access_control DROP INDEX idx_user_id_list');
            DB::statement('ALTER TABLE user_access_control DROP INDEX idx_branch_id_list');
            DB::statement('ALTER TABLE user_access_control DROP INDEX idx_role_id_list');
        } catch (\Exception $e) {
            // Indexes might not exist, ignore error
        }
    }
    
    /**
     * Backup existing data before migration
     */
    private function backupExistingData(): void
    {
        // Create backup table
        Schema::create('user_access_control_backup', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('control_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('code', 200)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('role_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->default(0);
            $table->longText('user_id_list')->nullable();
            $table->longText('branch_id_list')->nullable();
            $table->longText('role_id_list')->nullable();
            $table->longText('exclusive_branch_list')->nullable();
            $table->longText('exclude_branch_list')->nullable();
            $table->longText('exclude_user_list')->nullable();
            $table->tinyInteger('show_in_menu')->default(0);
            $table->string('name', 200)->nullable();
            $table->integer('hierarchy')->default(0);
            $table->string('type_name', 200)->nullable();
        });
        
        // Copy data to backup
        DB::statement('INSERT INTO user_access_control_backup SELECT * FROM user_access_control');
    }
    
    /**
     * Restore data with proper JSON format
     */
    private function restoreDataWithJsonFormat(): void
    {
        // Get all records from backup
        $records = DB::table('user_access_control_backup')->get();
        
        foreach ($records as $record) {
            // Convert comma-separated strings to JSON arrays
            $userList = $this->convertToJsonArray($record->user_id_list);
            $branchList = $this->convertToJsonArray($record->branch_id_list);
            $roleList = $this->convertToJsonArray($record->role_id_list);
            $exclusiveBranches = $this->convertToJsonArray($record->exclusive_branch_list);
            $excludeBranches = $this->convertToJsonArray($record->exclude_branch_list);
            $excludeUsers = $this->convertToJsonArray($record->exclude_user_list);
            
            // Update the record with JSON format
            DB::table('user_access_control')
                ->where('id', $record->id)
                ->update([
                    'user_id_list' => json_encode($userList),
                    'branch_id_list' => json_encode($branchList),
                    'role_id_list' => json_encode($roleList),
                    'exclusive_branch_list' => json_encode($exclusiveBranches),
                    'exclude_branch_list' => json_encode($excludeBranches),
                    'exclude_user_list' => json_encode($excludeUsers),
                ]);
        }
        
        // Drop backup table
        Schema::dropIfExists('user_access_control_backup');
    }
    
    /**
     * Convert comma-separated string to array
     */
    private function convertToJsonArray($value): array
    {
        if (empty($value)) {
            return [];
        }
        
        // If it's already JSON, decode it
        if (is_string($value) && (strpos($value, '[') === 0 || strpos($value, '{') === 0)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        // If it's comma-separated, split it
        if (is_string($value) && strpos($value, ',') !== false) {
            return array_filter(array_map('trim', explode(',', $value)));
        }
        
        // If it's a single value, wrap it in array
        if (!empty($value)) {
            return [$value];
        }
        
        return [];
    }
};
