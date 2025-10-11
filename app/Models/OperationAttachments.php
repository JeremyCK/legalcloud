<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationAttachments extends Model
{
    use HasFactory;

    
    protected $fillable = ["key_id", "status", "created_by", "created_at", "updated_at", "branch", "file_ori_name", "file_new_name", "s3_file_name", "is_migrated", "no_file", "deleted_at", "deleted_by", "attachment_type", "entity"];

    protected $table = 'operation_attachment';
    protected $primaryKey = 'id';
}
