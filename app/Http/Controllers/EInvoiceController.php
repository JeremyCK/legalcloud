<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EInvoiceController extends Controller
{
    /**
     * Save E-invoice record with its invoice items
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveEinvoice(Request $request)
    {
        try {
            // Start database transaction
            DB::beginTransaction();
            
            // Get the authenticated user
            $user = Auth::user();
            
            // Validate required fields
            if (!$request->has('ref_no') || !$request->has('transaction_id')) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Required fields are missing'
                ]);
            }
            
            // Create main e-invoice record
            $einvoiceId = DB::table('einvoice')->insertGetId([
                'ref_no' => $request->ref_no,
                'total_amount' => $request->total_amount ?: 0,
                'transaction_id' => $request->transaction_id,
                'description' => $request->description,
                'batch_status' => $request->batch_status,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Process invoice items if any
            if ($request->has('invoices')) {
                $invoices = json_decode($request->invoices, true);
                
                foreach ($invoices as $invoice) {
                    DB::table('einvoice_items')->insert([
                        'einvoice_id' => $einvoiceId,
                        'invoice_id' => $invoice['id'],
                        'invoice_no' => $invoice['invoice_no'],
                        'invoice_date' => $invoice['invoice_date'],
                        'total_amount' => $invoice['total_amount'],
                        'transfer_amount' => $invoice['transfer_amount'],
                        'sst_amount' => $invoice['sst_amount'],
                        'status' => 'ACTIVE',
                        'created_by' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            // Commit the transaction
            DB::commit();
            
            return response()->json([
                'status' => 1,
                'message' => 'E-invoice record created successfully'
            ]);
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            Log::error('Error creating e-invoice: ' . $e->getMessage());
            
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while saving the e-invoice: ' . $e->getMessage()
            ]);
        }
    }
}