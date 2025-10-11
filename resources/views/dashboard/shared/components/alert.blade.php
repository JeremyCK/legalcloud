@if (Session::has('message'))
    {{-- <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div> --}}
    <div id="div-alert" class="alert alert-success alert-dismissible fade show row" role="alert">
        <div class="col-10">{{ Session::get('message') }}</div>
        <div class="col-2  float-right"><button type="button" class="btn-close float-right" onclick="closeAlert()"
                data-coreui-dismiss="alert" aria-label="Close"></button></div>

    </div>
@endif

<script>
    function closeAlert() {
        $("#div-alert").hide();
    }

    var x = setInterval(function() {
        closeAlert()
    }, 5000);
</script>
