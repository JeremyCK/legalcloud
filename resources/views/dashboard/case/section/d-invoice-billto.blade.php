 {{-- @foreach ($InvoiceBillingParty as $index => $details)
     <div class="btn-group">
         <button type="button" class="btn @if ($details->completed == 1)btn-success @else btn-default  @endif btn-lg text-left">{{ $details->customer_name }} <br/>Invoice no: ({{ $details->invoice_no }}) @if ($details->completed == 1)
                 <br/>(Completed)
             @endif
         </button>
         <button type="button" class="btn @if ($details->completed == 1)btn-success @else btn-default @endif btn- dropdown-toggle" data-toggle="dropdown">
             <span class="caret"></span>
             <span class="sr-only">Toggle Dropdown</span>
         </button>
         <div class="dropdown-menu" style="padding:0">

             <div class="dropdown-divider" style="margin:0"></div>
             <a class="dropdown-item btn-success" href="javascript:void(0)"
                 onclick="invoicePrintMode({{ $details->id }});" style="color:white"><i class="cil-print"
                     style="margin-right: 10px;"></i> <span></span>Print Invoice</a>
             <div class="dropdown-divider" style="margin:0"></div>

             <a class="dropdown-item btn-warning" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                 data-keyboard="false"                  onclick="window.pendingPartyId = {{ $details->id }};" data-toggle="modal" data-target="#modalAddBilltoInfo" style="color:white;margin:0"><i
                     style="margin-right: 10px;" class="cil-calendar"></i>Update information</a>
             <div class="dropdown-divider" style="margin:0"></div>
             @if ($details->completed == 0)
                 <a class="dropdown-item btn-danger" href="javascript:void(0)"
                     onclick="removeBillto({{ $details->id }});" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-x"></i>Remove</a>
             @endif


         </div>
     </div>
 @endforeach --}}
 @foreach ($InvoiceBillingParty as $index => $details)
     <div class="btn-group">
         <button type="button"
             class="btn @if ($details->completed == 1) btn-success @else btn-default @endif btn-lg text-left">{{ $details->customer_name }}
             <br />Invoice no: ({{ $details->invoice_no }}) @if ($details->completed == 1)
                 <br />(Completed)
             @endif
         </button>
         <button type="button"
             class="btn @if ($details->completed == 1) btn-success @else btn-default @endif btn- dropdown-toggle"
             data-toggle="dropdown">
             <span class="caret"></span>
             <span class="sr-only">Toggle Dropdown</span>
         </button>
         <div class="dropdown-menu" style="padding:0">
             @if ($details->bill_party_id == 0)
                 <a class="dropdown-item bg-purple" href="javascript:void(0)" data-backdrop="static"
                     data-keyboard="false" data-keyboard="false" data-toggle="modal" onclick="AddPartyInvoiceMode({{ $details->invoice_id }});"
                     data-target="#modalAddBillto" onclick="" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-user"></i>Add Invoice recipient</a>
             <div class="dropdown-divider" style="margin:0"></div>
             @endif
             
             @if ($details->id != null)
              <a class="dropdown-item btn-success" href="javascript:void(0)"
                 onclick="invoicePrintMode({{ $details->invoice_id }});" style="color:white"><i class="cil-print"
                     style="margin-right: 10px;"></i> <span></span>Print Invoice</a>
             <div class="dropdown-divider" style="margin:0"></div>

             <a class="dropdown-item btn-warning" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                 data-keyboard="false"                  onclick="window.pendingPartyId = {{ $details->id }};" data-toggle="modal" data-target="#modalAddBilltoInfo" style="color:white;margin:0"><i
                     style="margin-right: 10px;" class="cil-calendar"></i>Update information</a>
             <div class="dropdown-divider" style="margin:0"></div>
             
             @if ($details->invoice_id != null)
                 <a class="dropdown-item btn-info" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                     data-toggle="modal" data-target="#modalEditSplitInvoiceDetail" onclick="editSplitInvoiceDetail({{ $details->invoice_id }});" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-pencil"></i>Edit Split Invoice Detail</a>
                 <div class="dropdown-divider" style="margin:0"></div>
                 
                 <a class="dropdown-item btn-primary" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                     data-toggle="modal" data-target="#modalUpdateInvoiceDate" onclick="loadInvoiceDate({{ $details->invoice_id }}, '{{ $details->invoice_no }}');" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-calendar"></i>Update Invoice Date</a>
                 <div class="dropdown-divider" style="margin:0"></div>
             @endif
             @endif

             

             @if ($details->completed == 0 && $details->id != null)
                 <a class="dropdown-item btn-danger" href="javascript:void(0)"
                     onclick="removeBillto({{ $details->invoice_id }});" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-x"></i>Remove Bill To</a>
             @endif

            @if ($details->invoice_no != $details->main_invoice_no)
                 <a class="dropdown-item btn-danger" href="javascript:void(0)"
                     onclick="removeInvoice({{ $details->invoice_id }});" style="color:white;margin:0"><i
                         style="margin-right: 10px;" class="cil-x"></i>Remove invoice</a>
             @endif


         </div>
     </div>
 @endforeach
