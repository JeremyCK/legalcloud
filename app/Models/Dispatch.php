<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    
    protected $fillable = ["case_id", "client_id", "contact_name", "contact_no", "return_to_office_datetime", "received_by", "courier_id", "job_desc", "status", "created_by", "created_at", "updated_at", "case_ref", "client_name", "dispatch_no", "branch", "send_to", "file_ori_name", "file_new_name", "dispatch_type", "remark", "s3_file_name", "is_migrated", "no_file", "deleted_at", "deleted_by"];

    protected $table = 'dispatch';
    protected $primaryKey = 'id';
}
