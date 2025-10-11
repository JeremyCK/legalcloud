<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="LegalCloud">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LegalCloud</title>
    <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-icon-180x180.png">
    <!-- <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png"> -->
    <!-- <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png"> -->
    <!-- <link rel="manifest" href="assets/favicon/manifest.json"> -->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Icons-->
    <link href="{{ asset('css/free.min.css') }}" rel="stylesheet">
    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css?0009') }}" rel="stylesheet">
    <link href="{{ asset('css/external.css?0001') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/flag.min.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/jquery.toast.min.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/external-master.css') }}" rel="stylesheet"> -->


    <link href='https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet'
        type='text/css'>
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/pro-style.css') }}" rel="stylesheet"> -->
    <style>
        div.loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(16, 16, 16, 0.5);
        }

        @-webkit-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-webkit-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-moz-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-ms-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-moz-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-webkit-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @-o-keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes uil-ring-anim {
            0% {
                -ms-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -ms-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -webkit-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .uil-ring-css {
            margin: auto;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            width: 200px;
            height: 200px;
        }

        .uil-ring-css>div {
            position: absolute;
            display: block;
            width: 160px;
            height: 160px;
            top: 20px;
            left: 20px;
            border-radius: 80px;
            box-shadow: 0 6px 0 0 #ffffff;
            -ms-animation: uil-ring-anim 1s linear infinite;
            -moz-animation: uil-ring-anim 1s linear infinite;
            -webkit-animation: uil-ring-anim 1s linear infinite;
            -o-animation: uil-ring-anim 1s linear infinite;
            animation: uil-ring-anim 1s linear infinite;
        }

        .cke_notification
        {
            display: none;
        }
    </style>
    @yield('css')
</head>

<body class="c-app">
    <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
        {{-- @include('dashboard.shared.nav-builder') --}}
        @include('dashboard.shared.nav-builder-v2')
        @include('dashboard.shared.sidebar')
        @include('dashboard.shared.header')
        <div class="c-body">
            <main class="c-main">
                @yield('content')
            </main>
            @include('dashboard.shared.footer')
        </div>
    </div>

    <div id="div_full_screen_loading" class="loading" style="display:none;z-index:1060;">
        <div class='uil-ring-css' style='transform:scale(0.79);'>
            <div></div>
        </div>
    </div>

    <!-- CoreUI and necessary plugins--> 
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <!-- <script src="{{ asset('js/coreui.pro.bundle.min.js') }}"></script> -->
    <script src="{{ asset('js/coreui-utils.js') }}"></script>
    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script src="{{ asset('js/external.js?0001') }}"></script>
    {{-- <script src="{{ asset('js/dropzone.min.js') }}"></script> --}}

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.0.0.min.js?001"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert22.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        retriveNotification();
        // let myVar = setInterval(retriveNotification, 180000);
        // let myVar = setInterval(retriveNotification, 50000);;
        let myVar = setInterval(retriveNotification, 100000);

        $('form').submit(function() {
            $(this).find(':submit').attr('disabled', 'disabled');
            //the rest of your code
            setTimeout(() => {
                $(this).find(':submit').attr('disabled', false);
            }, 3000)
        });

        // window.addEventListener('focus', function(event) {
        //     alert(33)
        // });

        // window.addEventListener('blur', function(event) {
        //     alert(334)
        // });

        function myTimer() {
            const d = new Date();
            // document.getElementById("demo").innerHTML = d.toLocaleTimeString();
            // console.log(1);
        }

        function retriveNotification() {
            if (document.hidden) {
                return;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/retriveNotification',
                data: null,
                success: function(data) {
                    console.log(data.data);

                    strDiv = '';

                    if (data.data.length > 0) {
                        $("#side_Voucher").html('<span style="margin-left:10px" class="label bg-danger">' + data
                            .notificationVoucherAccount.length + '</span>');
                        $(".notification-badge").html('<span class=" badge badge-pill badge-danger">' + data
                            .data.length + '</span>');
                        $("#notification_count").html(data.data.length);

                        for (var i = 0; i < data.data.length; i++) {
                            //     strDiv += ` <a class="dropdown-item" href="/voucher/` + data.data[i].parameter2 + `/edit">
                        // <svg class="c-icon mr-2 text-success">
                        //     <use xlink:href="{{ url('/icons/sprites/free.svg#cil-user-follow') }}"></use>
                        //   </svg> ` + data.data[i].name + ` ` + data.data[i].desc + `</a>`;

                            strDiv +=
                                ` <a class="dropdown-item" href="javascript:void(0)" onclick="OpenNotification( ` + data.data[i].id + `);">
                                <i class="c-icon mr-2 text-success cil-user"></i> ` + data.data[i].name + ` ` + data.data[i].desc + `</a>`;
                        }



                    } else {
                        $("#side_Voucher").html('');
                        $(".notification-badge").html('');
                        $("#notification_count").html(0);

                        strDiv += ` <a class="dropdown-item" href="javascript:void(0)">
          <svg class="c-icon mr-2 text-success">
              <use xlink:href="javascript:void(0)"></use>
            </svg>No notification</a>`;
                    }


                    $("#notification_list").html(strDiv);

                    strDiv = '';

                    if (data.notification_receipt.length > 0) {
                        $("#side_Voucher").html('<span style="margin-left:10px" class="label bg-danger">' + data
                            .notification_receipt.length + '</span>');
                        $(".notification-receipt-badge").html('<span class=" badge badge-pill badge-danger">' +
                            data.notification_receipt.length + '</span>');
                        $("#notification_receipt_count").html(data.notification_receipt.length);

                        for (var i = 0; i < data.notification_receipt.length; i++) {
                            //     strDiv += ` <a class="dropdown-item" href="/voucher/` + data.data[i].parameter2 + `/edit">
                        // <svg class="c-icon mr-2 text-success">
                        //     <use xlink:href="{{ url('/icons/sprites/free.svg#cil-user-follow') }}"></use>
                        //   </svg> ` + data.data[i].name + ` ` + data.data[i].desc + `</a>`;

                            strDiv +=
                                ` <a class="dropdown-item" href="javascript:void(0)" onclick="OpenNotification( ` +
                                data.data[i].id + `);">
          <i class="c-icon cil-user"></i> ` + data.data[i].name + ` ` + data.data[i].desc + `</a>`;
                        }



                    } else {
                        // $("#side_Account").html('');
                        $(".notification-receipt-badge").html('');
                        $("#notification_receipt_count").html(0);

                        strDiv += ` <a class="dropdown-item" href="javascript:void(0)">
          <svg class="c-icon mr-2 text-success">
              <use xlink:href="javascript:void(0)"></use>
            </svg>No notification</a>`;
                    }

                    $("#notification_receipt_list").html(strDiv);


                    // $('ul.pagination').replaceWith(data.links);
                }
            });
        }

        function deleteOperation($id, type) {
            var formData = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                title: 'Delete this record?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                  var formData = new FormData();

                  formData.append('id', $id);
                  formData.append('type', type);

                    $.ajax({
                        type: 'POST',
                        url: '/deleteOperation',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                              Swal.fire('Success!', data.message, 'success');
                                // location.reload();
                                // toastController(data.message);
                                reloadTable();
                            } else {
                              Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function OpenNotification(id) {
          $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/openNotification/' + id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    window.location.href = '/voucher/' + data.data + '/edit';

                }
            });
        }

        function closeUniversalModal() {
            $('.btn_close_all').click();
            // $(".modal-backdrop").remove();
            $('body').removeClass('modal-open');
        }


        
        function forceLogout()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '{{ url('/logout') }}',
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    window.location.href = '/login';
                }
            });
        }

        function thousandSeparator(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }


        function toastController($message, $status='success') {

            $heading = 'Success';

            if ($status != 'success')
            {
                $heading = 'Warning'
            }
            $.toast({
                text: $message, // Text that is to be shown in the toast
                heading: $heading, // Optional heading to be shown on the toast
                icon: $status, // Type of toast icon
                showHideTransition: 'slide', // fade, slide or plain
                allowToastClose: true, // Boolean value true or false
                hideAfter: 5000, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
                stack: 5, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
                position: 'top-right', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values


                textAlign: 'left', // Text alignment i.e. left, right or center
                loader: true, // Whether to show loader or not. True by default
                loaderBg: '#9EC600', // Background color of the toast loader
                beforeShow: function() {}, // will be triggered before the toast is shown
                afterShown: function() {}, // will be triggered after the toat has been shown
                beforeHide: function() {}, // will be triggered before the toast gets hidden
                afterHidden: function() {} // will be triggered after the toast has been hidden
            });

            // Swal.fire('success', 'Case tran
        }
    </script>
    @yield('javascript')
</body>

</html>
