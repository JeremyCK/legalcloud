<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandOffice extends Model
{
    use HasFactory;

    protected $fillable = ["case_id", "client_id", "land_office", "matter", "smartbox_no", "receipt_no", "received_on", "remark", "status", "created_by", "created_at", "updated_at", "case_ref", "client_name", "branch", "received", "file_ori_name", "file_new_name", "s3_file_name", "is_migrated", "no_file", "deleted_at", "deleted_by"];

    protected $table = 'land_office';
    protected $primaryKey = 'id';
}
