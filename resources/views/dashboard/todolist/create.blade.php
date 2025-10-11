@extends('dashboard.base')

@section('content')


<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12  ">


                <!-- <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Purchaser</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse show" id="collapseOne" role="tabpanel" data-parent="#accordion">
                        <form id="form_purchaser" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseVendor" aria-expanded="true" aria-controls="collapseVendor">Vendor</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseVendor" role="tabpanel" data-parent="#accordion">
                        <form id="form_vendor" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseTitle" aria-expanded="true" aria-controls="collapseTitle">Title</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseTitle" role="tabpanel" data-parent="#accordion">
                        <form id="form_title" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePropertyAddress" aria-expanded="true" aria-controls="collapsePropertyAddress">Property Address</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePropertyAddress" role="tabpanel" data-parent="#accordion">
                        <form id="form_property_address" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseVendorFinancier" aria-expanded="true" aria-controls="collapseVendorFinancier">Vendor Financier</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseVendorFinancier" role="tabpanel" data-parent="#accordion">
                        <form id="form_vendor_financier" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePurchaserFinancier" aria-expanded="true" aria-controls="collapsePurchaserFinancier">Puchaser Financier</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePurchaserFinancier" role="tabpanel" data-parent="#accordion">
                        <form id="form_purchaser_financier" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePurchaserSolicitors" aria-expanded="true" aria-controls="collapsePurchaserSolicitors">Purchaser Solicitors</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePurchaserSolicitors" role="tabpanel" data-parent="#accordion">
                        <form id="form_purchaser_solicitors" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseAgent" aria-expanded="true" aria-controls="collapseAgent">Agent</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseAgent" role="tabpanel" data-parent="#accordion">
                        <form id="form_agent" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseBanker" aria-expanded="true" aria-controls="collapseBanker">Banker</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseBanker" role="tabpanel" data-parent="#accordion">
                        <form id="form_banker" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePurchaserFinancierSolicitors" aria-expanded="true" aria-controls="collapsePurchaserFinancierSolicitors">Purchaser Financier Solicitors</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePurchaserFinancierSolicitors" role="tabpanel" data-parent="#accordion">
                        <form id="form_purchaser_financier_solicitors" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseLoanSum" aria-expanded="true" aria-controls="collapseLoanSum">Loan Sum</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseLoanSum" role="tabpanel" data-parent="#accordion">
                        <form id="form_loan_sum" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePurchasePrice" aria-expanded="true" aria-controls="collapsePurchasePrice">Purchase Price</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePurchasePrice" role="tabpanel" data-parent="#accordion">
                        <form id="form_purchase_price" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseOurLawyer" aria-expanded="true" aria-controls="collapseOurLawyer">Our Lawyer</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseOurLawyer" role="tabpanel" data-parent="#accordion">
                        <form id="form_our_lawyer" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseOurMarketing" aria-expanded="true" aria-controls="collapseOurMarketing">Our Marketing</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseOurMarketing" role="tabpanel" data-parent="#accordion">
                        <form id="form_our_marketing" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseOurClerk" aria-expanded="true" aria-controls="collapseOurClerk">Our Clerk</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseOurClerk" role="tabpanel" data-parent="#accordion">
                        <form id="form_our_clerk" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseEarnestDeposit" aria-expanded="true" aria-controls="collapseEarnestDeposit">Earnest Deposit</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseEarnestDeposit" role="tabpanel" data-parent="#accordion">
                        <form id="form_earnest_deposit" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseBalanceDeposit" aria-expanded="true" aria-controls="collapseBalanceDeposit">Balance Deposit</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseBalanceDeposit" role="tabpanel" data-parent="#accordion">
                        <form id="form_balance_deposit" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseRPGT" aria-expanded="true" aria-controls="collapseRPGT">Balance Deposit</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseRPGT" role="tabpanel" data-parent="#accordion">
                        <form id="form_rpgt" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseDifferentialSum" aria-expanded="true" aria-controls="collapseDifferentialSum">Differential Sum</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseDifferentialSum" role="tabpanel" data-parent="#accordion">
                        <form id="form_differential_sum" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseBalancePurchasePrice" aria-expanded="true" aria-controls="collapseBalancePurchasePrice">Balance Purchase Price</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseBalancePurchasePrice" role="tabpanel" data-parent="#accordion">
                        <form id="form_balance_purchase_price" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseBalancePurchasePrice" aria-expanded="true" aria-controls="collapseBalancePurchasePrice">Date</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseBalancePurchasePrice" role="tabpanel" data-parent="#accordion">
                        <form id="form_balance_purchase_price" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Completion Date</th>
                                        <td><input type="date" id="completion_date" name="completion_date" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Extended Completion Date</th>
                                        <td><input type="date" id="extended_completion_date" name="extended_completion_date" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Property Address</th>
                                        <td><textarea class="form-control" id="property_address" name="property_address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>Land Office</th>
                                        <td><textarea class="form-control" id="land_office" name="land_office" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseMaintenanceOffice" aria-expanded="true" aria-controls="collapseMaintenanceOffice">Maintenance Office</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseMaintenanceOffice" role="tabpanel" data-parent="#accordion">
                        <form id="form_maintenance_office" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseLDHNBranch" aria-expanded="true" aria-controls="collapseLDHNBranch">LHDN branch</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseLDHNBranch" role="tabpanel" data-parent="#accordion">
                        <form id="form_ldhn_branch" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseDeveloper" aria-expanded="true" aria-controls="collapseDeveloper">Developer</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseDeveloper" role="tabpanel" data-parent="#accordion">
                        <form id="form_developer" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>

                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseProprietor" aria-expanded="true" aria-controls="collapseProprietor">Proprietor</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseProprietor" role="tabpanel" data-parent="#accordion">
                        <form id="form_proprietor" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseLiquidator" aria-expanded="true" aria-controls="collapseLiquidator">Liquidator</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseLiquidator" role="tabpanel" data-parent="#accordion">
                        <form id="form_liquidator" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseValuer" aria-expanded="true" aria-controls="collapseValuer">Valuer</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseValuer" role="tabpanel" data-parent="#accordion">
                        <form id="form_valuer" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePOTSolicors" aria-expanded="true" aria-controls="collapsePOTSolicors">POT solicitors</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePOTSolicors" role="tabpanel" data-parent="#accordion">
                        <form id="form_pot_solicors" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePOCSolicors" aria-expanded="true" aria-controls="collapsePOCSolicors">POC solicitors</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePOCSolicors" role="tabpanel" data-parent="#accordion">
                        <form id="form_poc_solicors" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div>


                <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapsePOCSolicors" aria-expanded="true" aria-controls="collapsePOCSolicors">POC solicitors</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapsePOCSolicors" role="tabpanel" data-parent="#accordion">
                        <form id="form_poc_solicors" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Name</th>
                                        <td><input type="text" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>NRIC</th>
                                        <td><input type="text" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td><textarea class="form-control" id="address" name="address" rows="3" placeholder=""></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div> -->

                <!-- <div class="card accordion" id="accordion" role="tablist">
                    <div class="card-header">
                        <h4><a data-toggle="collapse" href="#collapseTable" aria-expanded="true" aria-controls="collapseTable">POC solicitors</a> <span class="cil-check"></span></h4>
                    </div>
                    <div class="card-body collapse " id="collapseTable" role="tabpanel" data-parent="#accordion">
                        <form id="form_poc_solicors" class="form-horizontal" action="" method="post">

                            <table class="table table-striped table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Earnest Deposit</th>
                                        <td><input type="number" id="name" name="name" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Balance Deposit</th>
                                        <td><input type="number" id="nric" name="nric" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Differential Sum</th>
                                        <td><input type="number" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>HP</th>
                                        <td><input type="text" id="hp_no" name="hp_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Tel No</th>
                                        <td><input type="text" id="tel_no" name="tel_no" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Fax</th>
                                        <td><input type="text" id="fax" name="fax" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><input type="email" id="email" name="email" class="form-control" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>

                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit"> Submit</button>
                            <button class="btn btn-sm btn-danger" type="reset"> Reset</button>
                        </div>
                    </div>

                </div> -->

                <div class="card">
                    <div class="card-header">
                        <h4>Open New Case</h4>
                    </div>
                    <div class="card-body">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <form action="{{ route('todolist.store') }}" method="POST">
                            @csrf
                            <table class="table  table-bordered datatable">
                                <tbody>
                                    <tr>
                                        <th>Bank</th>
                                        <td>
                                            <select id="bank" class="form-control" name="bank" required>
                                                <option value="">-- Please select the template -- </option>
                                                @foreach($banks as $index => $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Client Name</th>
                                        <td>
                                            <input type="text" id="client_name"  name="client_name" class="form-control" required/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Client Phone Number</th>
                                        <td>
                                            <input type="text" id="client_phone_no"  name="client_phone_no" class="form-control" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Property Address</th>
                                        <td><textarea class="form-control" id="property_address" name="property_address" rows="3" placeholder="" required></textarea></td>
                                    </tr>
                                    <tr>
                                        <th>Referral Name</th>
                                        <td>
                                            <input type="text" id="referral_name"  name="referral_name" class="form-control" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Referral Phone Number</th>
                                        <td>
                                            <input type="text" id="referral_phone_no"  name="referral_phone_no" class="form-control" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Referral Email</th>
                                        <td>
                                            <input type="email" id="referral_email"  name="referral_email" class="form-control" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Purchase Price</th>
                                        <td>
                                            <input type="number" value="0" id="purchase_price"  name="purchase_price" class="form-control" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Remark</th>
                                        <td><textarea class="form-control" id="remark" name="remark" rows="3" placeholder="" ></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button class="btn btn-primary" type="submit">Save</button>
                            <a class="btn btn-primary" href="{{ route('todolist.index') }}">Return</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('javascript')


@endsection