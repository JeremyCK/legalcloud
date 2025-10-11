<div id="dAddAccountItem" class="card" style="display:none">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-plus-circle mr-2"></i>
                Add Account Item
            </h5>
            <button type="button" class="close" onclick="viewMode()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form id="form_account">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="font-weight-bold">Select Account Item</label>
                        <select class="form-control" id="selected_account_id" name="selected_account_id">
                            <option value="0">-- Please select an account item --</option>
                            @foreach($accounts as $account)
                                <option class="account_item_all account_cat_{{$account->account_cat_id}} account_{{$account->id}}" 
                                        value="{{$account->id}}">
                                    {{ $account->name }} - {{ $account->category }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">
                            Choose an account item to add to this quotation template
                        </small>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 text-right">
                    <button class="btn btn-success" onclick="addNewAccount()" type="button">
                        <i class="fas fa-plus mr-2"></i>
                        Add Item
                    </button>
                    <button type="button" class="btn btn-danger" onclick="viewMode()">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>