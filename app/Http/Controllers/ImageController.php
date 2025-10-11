<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\BankReconRecord;
use App\Models\Branch;
use App\Models\CaseAccountTransaction;
use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\caseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseBillMain;
use App\Models\OfficeBankAccount;
use App\Models\TransferFeeDetails;
use App\Models\TransferFeeDetailsDelete;
use App\Models\TransferFeeMain;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{

    public static function verifyImage($file)
    {
        $allowedMimeTypes = ['jpeg', 'jpg', 'gif', 'png', 'bmp', 'svg+xml'];
        $extension = $file->getClientOriginalExtension();

        if (!in_array($extension, $allowedMimeTypes)) {
            return false;
        }
        else
        {
             return true;
        }
    }

    public static function resizeImg($file, $location, $filename)
    {
        $width = 1000; // your max width
        $height = 1000; // your max height

        $imgFile = Image::make($file->getRealPath());
        $imgFile->height() > $imgFile->width() ? $width = null : $height = null;

        $imgFile->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $resource = $imgFile->stream()->detach();

        $path = Storage::disk('Wasabi')->put(
            $location . '/' . $filename ,
            $resource
        );

        // $fiel = Storage::disk('Wasabi')->put($location, $resource);

        return $location . '/' . $filename;
        
    }
}
