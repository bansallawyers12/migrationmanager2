<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keyword" content="CRM">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CRM | Client Details</title>
    <link rel="icon" type="image/png" href="{{asset('img/favicon.png')}}">
    <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/iziToast.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/fullcalendar.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/summernote-bs4.css')}}">
    <link rel="stylesheet" href="{{asset('css/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-timepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/bootstrap-formhelpers.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/intlTelInput.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/components.css')}}">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('css/dataTables_min_latest.css')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="{{asset('js/jquery_min_latest.js')}}"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #343a40; line-height: 1.6; }
        .main-wrapper { position: relative; }
        .main-navbar { position: fixed; top: 0; width: 100%; z-index: 1000; background-color: #fff; height: 70px; }
        
        /* Modern topbar layout */
        .main-topbar { 
            display: grid !important; 
            grid-template-columns: 1fr minmax(400px, 640px) auto !important; 
            align-items: center !important; 
            gap: 16px !important; 
            position: sticky !important; 
            top: 0 !important; 
            z-index: 1000 !important; 
            height: 70px !important; 
            padding: 10px 16px !important; 
            background: #ffffff !important; 
            border-bottom: 1px solid #dfe3e6 !important; 
            box-shadow: 0 2px 8px rgba(0,0,0,.06) !important;
            transition: transform .2s ease-in-out !important;
        }
        /* Hide on scroll (keep a small top gap visible) */
        .main-topbar.is-hidden {
            transform: translateY(calc(-100% + 6px)) !important;
        }
        /* Collapsed state */
        .main-topbar.is-collapsed { 
            grid-template-columns: auto !important; 
            height: 48px !important; 
            padding: 6px 12px !important; 
        }
        .main-topbar.is-collapsed .topbar-left,
        .main-topbar.is-collapsed .topbar-center,
        .main-topbar.is-collapsed .topbar-right { display: none !important; }
        .main-topbar .topbar-toggle { 
            display: inline-flex !important; align-items: center !important; justify-content: center !important; 
            width: 36px !important; height: 36px !important; border-radius: 8px !important; 
            background: #f3f5f7 !important; border: 1px solid #e9ecef !important; color: #495057 !important; 
        }
        .main-topbar:not(.is-collapsed) .topbar-toggle { display: none !important; }
        .topbar-left .icon-group { display: flex !important; gap: 10px !important; align-items: center !important; }
        .icon-btn { 
            display: inline-flex !important; align-items: center !important; justify-content: center !important; 
            width: 36px !important; height: 36px !important; border-radius: 8px !important; 
            background: transparent !important; border: none !important; color: #495057 !important; 
            transition: all .15s ease !important; text-decoration: none !important; 
        }
        .icon-btn:hover { background: #f3f5f7 !important; color: #0d6efd !important; }
        .icon-btn i { font-size: 18px !important; }
        /* Search */
        .topbar-center .search-container { position: relative !important; width: 100% !important; max-width: 480px !important; }
        .search-input { 
            width: 100% !important; height: 40px !important; padding: 0 16px 0 44px !important; 
            border: 1px solid #e9ecef !important; border-radius: 20px !important; 
            background: #f8f9fa !important; font-size: 14px !important; 
            transition: all .15s ease !important; 
        }
        .search-input:focus { 
            outline: none !important; border-color: #0d6efd !important; 
            background: #fff !important; box-shadow: 0 0 0 3px rgba(13,110,253,.1) !important; 
        }
        .search-icon { 
            position: absolute !important; left: 14px !important; top: 50% !important; 
            transform: translateY(-50%) !important; color: #6c757d !important; pointer-events: none !important; 
        }
        .topbar-right { display: flex !important; align-items: center !important; gap: 12px !important; }
        /* Dropdown */
        .icon-dropdown { position: relative !important; }
        .icon-dropdown-menu { 
            position: absolute !important; top: 48px !important; left: 0 !important; 
            background: #fff !important; border: 1px solid #e9ecef !important; 
            border-radius: 8px !important; min-width: 200px !important; 
            padding: 6px 0 !important; display: none !important; 
            box-shadow: 0 12px 24px rgba(0,0,0,.08) !important; 
        }
        .icon-dropdown .icon-dropdown-menu.show { display: block !important; }
        .icon-dropdown-menu .dropdown-item { padding: 8px 12px !important; color: #343a40 !important; }
        .icon-dropdown-menu .dropdown-item:hover { background: #f1f5ff !important; color: #0d6efd !important; }
        /* Profile */
        .profile-dropdown { position: relative !important; }
        .profile-trigger img { width: 36px !important; height: 36px !important; border-radius: 50% !important; object-fit: cover !important; }
        .profile-trigger { display: inline-flex !important; align-items: center !important; justify-content: center !important; width: 44px !important; height: 44px !important; border-radius: 50% !important; }
        .profile-menu { position: absolute !important; right: 0 !important; top: 48px !important; background: #fff !important; border: 1px solid #e9ecef !important; border-radius: 8px !important; min-width: 200px !important; padding: 6px 0 !important; display: none !important; box-shadow: 0 12px 24px rgba(0,0,0,.08) !important; }
        .profile-dropdown .profile-menu.show { display: block !important; }
        .profile-menu a { display: block !important; padding: 8px 12px !important; color: #343a40 !important; text-decoration: none !important; }
        .profile-menu a:hover { background: #f1f5ff !important; color: #0d6efd !important; }

        /* When topbar is hidden, reclaim space for content (leave 6px gap) */
        body.topbar-hidden .crm-container { margin-top: 6px !important; }
        .crm-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 0;
            min-height: calc(100vh - 70px);
            padding: 15px;
            gap: 20px;
        }
        .main-sidebar {
            position: fixed;
            top: 70px;
            left: 0;
            width: 60px;
            height: calc(100vh - 70px);
            background-color: #fff;
            transition: width 0.3s ease;
            z-index: 900;
            overflow: hidden;
        }
        .sidebar-expanded {
            width: 220px !important;
        }
        .main-content {
            flex: 1;
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            min-width: 0;
            margin-left: 80px;
            transition: margin-left 0.3s ease;
        }
        .activity-feed {
            flex: 0 0 300px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-height: calc(100vh);
            overflow-y: auto;
        }
        .client-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #dee2e6; }
        .client-header h1 { font-size: 1.8em; font-weight: 600; color: #212529; margin: 0; }
        .client-rating { display: inline-block; background-color: #e9ecef; color: #495057; padding: 3px 8px; font-size: 0.8em; border-radius: 4px; margin-left: 10px; vertical-align: middle; }
        .client-status { display: flex; align-items: center; gap: 15px; }
        .status-badge { background-color: #cfe2ff; color: #0d6efd; padding: 5px 10px; border-radius: 15px; font-weight: 500; font-size: 0.9em; }
        .btn { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9em; font-weight: 500; transition: background-color 0.2s ease, box-shadow 0.2s ease; }
        .btn i { margin-right: 5px; }
        .btn-primary { background-color: #0d6efd; color: white; }
        .btn-primary:hover { background-color: #0b5ed7; box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3); }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-secondary:hover { background-color: #5c636a; }
        .btn-block { display: block; width: 100%; margin-top: 15px; }
        .content-tabs { margin-bottom: 25px; border-bottom: 1px solid #dee2e6; display: flex; gap: 5px; }
        .tab-button { background-color: transparent; border: none; border-bottom: 3px solid transparent; padding: 10px 18px; cursor: pointer; font-size: 0.95em; color: #6c757d; transition: color 0.2s ease, border-color 0.2s ease; margin-bottom: -1px; }
        .tab-button:hover { color: #0d6efd; }
        .tab-button.active { color: #0d6efd; border-bottom-color: #0d6efd; font-weight: 600; }
        .content-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background-color: #ffffff; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; margin-bottom: 15px; }
        .card h3 { font-size: 1.1em; font-weight: 600; margin-bottom: 15px; color: #343a40; display: flex; align-items: center; }
        .card h3 i { margin-right: 8px; color: #6c757d; }
        .field-group { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #f1f3f5; font-size: 0.9em; }
        .field-group:last-child { border-bottom: none; }
        .field-label { color: #6c757d; font-weight: 500; padding-right: 10px; }
        .field-value { color: #212529; text-align: right; }
        .activity-feed h2 { font-size: 1.3em; margin-bottom: 20px; font-weight: 600; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; display: flex; align-items: center; }
        .activity-feed h2 i { margin-right: 10px; color: #6c757d; }
        .feed-list { list-style: none; }
        .feed-item { display: flex; gap: 15px; padding: 12px 0; border-bottom: 1px solid #e9ecef; }
        .feed-item:last-child { border-bottom: none; }
        .feed-icon { font-size: 1.1em; color: #6c757d; width: 25px; text-align: center; flex-shrink: 0; padding-top: 2px; }
        .feed-item--email .feed-icon { color: #0d6efd; }
        .feed-item--call .feed-icon { color: #198754; }
        .feed-item--doc .feed-icon { color: #ffc107; }
        .feed-item--note .feed-icon { color: #6f42c1; }
        .feed-item--system .feed-icon { color: #adb5bd; }
        .feed-content p { margin-bottom: 4px; font-size: 0.9em; }
        .feed-content strong { font-weight: 600; }
        .feed-timestamp { font-size: 0.8em; color: #6c757d; }
        .main-footer { position: fixed; bottom: 0; width: 100%; z-index: 800; background: #fff; padding: 10px; }
        @media (max-width: 1200px) {
            .activity-feed { flex: 0 0 280px; }
            .main-content { margin-left: 70px; }
        }
        @media (max-width: 992px) {
            .crm-container { flex-direction: column; margin-top: 0; }
            .main-sidebar { position: relative; top: 0; width: 100%; height: auto; }
            .sidebar-expanded { width: 100%; }
            .main-content { margin-left: 0; width: 100%; }
            .activity-feed { flex: 0 0 auto; width: 100%; max-height: none; }
            .client-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .client-status { width: 100%; justify-content: space-between; }
            .content-tabs { flex-wrap: wrap; }
            .content-grid { grid-template-columns: 1fr; }
            .main-footer { position: relative; }
        }
        .sidebar-mini .main-content { padding-left: 25px !important; }
    </style>
    @yield('styles')
</head>
<body class="sidebar-mini">
    <div class="loader"></div>
    <div class="popuploader" style="display: none;"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            @include('../Elements/Admin/header_client_detail')
            @include('../Elements/Admin/left-side-bar_client_detail_appointment')
            @yield('content')
            @include('../Elements/Admin/footer_client_detail')
        </div>
    </div>
     <?php dd('$$$');?>
    <!-- Scripts -->
    <?php
    if(@Settings::sitedata('date_format') != 'none'){
        $date_format = @Settings::sitedata('date_format');
        if($date_format == 'd/m/Y'){
            $dataformat = 'DD/MM/YYYY';
        } else if($date_format == 'm/d/Y'){
            $dataformat = 'MM/DD/YYYY';
        } else if($date_format == 'Y-m-d'){
            $dataformat = 'YYYY-MM-DD';
        } else{
            $dataformat = 'YYYY-MM-DD';
        }
    } else{
        $dataformat = 'YYYY-MM-DD';
    }
    ?>
    <script>
    var site_url = '{{URL::to('/')}}';
    var dataformat = '{{$dataformat}}';
    </script>
    <script src="{{asset('js/app.min.js')}}"></script>
    <script src="{{asset('js/fullcalendar.min.js')}}"></script>
    <script src="{{asset('js/datatables.min.js')}}"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="{{asset('js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{asset('js/summernote-bs4.js')}}"></script>
    <script src="{{asset('js/daterangepicker.js')}}"></script>
    <script src="{{asset('js/bootstrap-timepicker.min.js')}}"></script>
    <script src="{{asset('js/select2.full.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-formhelpers.min.js')}}"></script>
    <script src="{{asset('js/intlTelInput.js')}}"></script>
    <script src="{{asset('js/custom-form-validation.js')}}"></script>
    <script src="{{asset('js/scripts.js')}}"></script>
    <script src="{{asset('js/iziToast.min.js')}}"></script>
    <script src="{{asset('js/custom.js')}}"></script>
    <script>
        $(document).ready(function () {
            $(".tel_input").on("blur", function() {
                /*if (/^0/.test(this.value)) {
                    //this.value = this.value.replace(/^0/, "")
                } else {
                    //this.value =  "0" + this.value;
                }*/
                this.value =  this.value;
            });

            $('.assineeselect2').select2({
                dropdownParent: $('#checkinmodal'),
            });

            $('.js-data-example-ajaxccsearch').select2({
                closeOnSelect: true,
                ajax: {
                    url: '{{URL::to('/admin/clients/get-allclients')}}',
                    dataType: 'json',
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: data.items
                        };
                    },
                    cache: true
                },
                templateResult: formatRepomain,
                templateSelection: formatRepoSelectionmain
            });

            function formatRepomain (repo) {
                if (repo.loading) {
                    return repo.text;
                }

                var $container = $(
                    "<div dataid="+repo.cid+" class='selectclient select2-result-repository ag-flex ag-space-between ag-align-center')'>" +

                    "<div  class='ag-flex ag-align-start'>" +
                        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

                    "</div>" +
                    "</div>" +
                    "<div class='ag-flex ag-flex-column ag-align-end'>" +

                        "<span class='select2resultrepositorystatistics'>" +

                        "</span>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-repository__title").text(repo.name);
                $container.find(".select2-result-repository__description").text(repo.email);
                if(repo.status == 'Archived'){
                    $container.find(".select2resultrepositorystatistics").append('<span class="ui label  select2-result-repository__statistics">'+repo.status+'</span>');
                } else {
                    $container.find(".select2resultrepositorystatistics").append('<span class="ui label yellow select2-result-repository__statistics">'+repo.status+'</span>');
                }
                return $container;
            }

            function formatRepoSelectionmain (repo) {
                return repo.name || repo.text;
            }

            /*
            $('.js-data-example-ajaxccsearch').on('change', function () {
                var v = $(this).val();
                var s = v.split('/');
                if(s[1] == 'Client'){
                    window.location = '{{URL::to('/admin/clients/detail/')}}/'+s[0]; // redirect
                }else{
                    window.location = '{{URL::to('/admin/leads/history/')}}/'+s[0]; // redirect
                }
                return false;
            });*/

            $('.js-data-example-ajaxccsearch').on('change', function () {
                var v = $(this).val(); //alert(v);
                var s = v.split('/'); //alert(s[1]);
                //console.log('v='+v);
                //console.log('s0='+s[0]+'==s1='+s[1]+'==s2='+s[2]);
                if(s[1] == 'Matter' && s[2] != ''){
                    window.location = '{{URL::to('/admin/clients/detail/')}}/'+s[0]+'/'+s[2]; // redirect
                } else {
                    if(s[1] == 'Client'){
                        window.location = '{{URL::to('/admin/clients/detail/')}}/'+s[0]; // redirect
                    }  else{
                        window.location = '{{URL::to('/admin/leads/history/')}}/'+s[0]; // redirect
                    }
                }
                return false;
            });


            $(document).delegate('.opencheckin', 'click', function(){
                $('#checkinmodal').modal('show');
            });

            $(document).delegate('.visitpurpose', 'blur', function(){
                var visitpurpose = $(this).val();
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/admin/update_visit_purpose',
                    type:'POST',
                    data:{id: appliid,visit_purpose:visitpurpose},
                    success: function(responses){
                        $.ajax({
                            url: site_url+'/admin/get-checkin-detail',
                            type:'GET',
                            data:{id: appliid},
                            success: function(res){
                                $('.popuploader').hide();
                                $('.showchecindetail').html(res);
                            }
                        });
                    }
                });
            });

            $(document).delegate('.savevisitcomment', 'click', function(){
                var visitcomment = $('.visit_comment').val();
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/admin/update_visit_comment',
                    type:'POST',
                    data:{id: appliid,visit_comment:visitcomment},
                    success: function(responses){
                        // $('.popuploader').hide();
                        $('.visit_comment').val('');
                        $.ajax({
                            url: site_url+'/admin/get-checkin-detail',
                            type:'GET',
                            data:{id: appliid},
                            success: function(res){
                                $('.popuploader').hide();
                                $('.showchecindetail').html(res);
                            }
                        });
                    }
                });
            });

            $(document).delegate('.attendsession', 'click', function(){
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/admin/attend_session',
                    type:'POST',
                    data:{id: appliid,waitcountdata: $('#waitcountdata').val()},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        if(obj.status){
                            $.ajax({
                                url: site_url+'/admin/get-checkin-detail',
                                type:'GET',
                                data:{id: appliid},
                                success: function(res){
                                    $('.popuploader').hide();
                                    $('.showchecindetail').html(res);
                                }
                            });
                            $('.checindata #id_'+appliid).remove();
                            alert(obj.message);
                        }
                    }
                });
            });

            $(document).delegate('.completesession', 'click', function(){
                var appliid = $(this).attr('data-id');
                $('.popuploader').show();
                $.ajax({
                    url: site_url+'/admin/complete_session',
                    type:'POST',
                    data:{id: appliid,attendcountdata: $('#attendcountdata').val()},
                    success: function(response){
                        var obj = $.parseJSON(response);
                        if(obj.status){
                            $.ajax({
                                url: site_url+'/admin/get-checkin-detail',
                                type:'GET',
                                data:{id: appliid},
                                success: function(res){
                                    $('.popuploader').hide();
                                    $('.showchecindetail').html(res);
                                }
                            });
                            $('.checindata #id_'+appliid).remove();
                        } else {
                            alert(obj.message);
                        }
                    }
                });
            });

            $(document).delegate('.opencheckindetail', 'click', function(){
                $('#checkindetailmodal').modal('show');
                $('.popuploader').show();
                var appliid = $(this).attr('id');
                $.ajax({
                    url: site_url+'/admin/get-checkin-detail',
                    type:'GET',
                    data:{id: appliid},
                    success: function(responses){
                        $('.popuploader').hide();
                        $('.showchecindetail').html(responses);
                    }
                });
            });

            /* $(".niceCountryInputSelector").each(function(i,e){
                new NiceCountryInput(e).init();
            }); */
            //$('.country_input').flagStrap();

            $(".telephone").intlTelInput();
            $('.drop_table_data button').on('click', function(){
                $('.client_dropdown_list').toggleClass('active');
            });

            $('.client_dropdown_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.client_table_data table tr td').show();
                        $('.client_table_data table tr th').show();
                        $('.client_dropdown_list label.dropdown-option input').prop('checked', true);
                    } else {
                        $('.client_dropdown_list label.dropdown-option input').prop('checked', false);
                        $('.client_table_data table tr td').hide();
                        $('.client_table_data table tr th').hide();
                        $('.client_table_data table tr td:nth-child(1)').show();
                        $('.client_table_data table tr th:nth-child(1)').show();
                        $('.client_table_data table tr td:nth-child(2)').show();
                        $('.client_table_data table tr th:nth-child(2)').show();
                        $('.client_table_data table tr td:nth-child(17)').show();
                        $('.client_table_data table tr th:nth-child(17)').show();
                    }
                }
                else
                {

                    if ($(this).is(":checked")) {
                        $('.client_table_data table tr td:nth-child('+val+')').show();
                        $('.client_table_data table tr th:nth-child('+val+')').show();
                    } else {
                        $('.client_dropdown_list label.dropdown-option.all input').prop('checked', false);
                        $('.client_table_data table tr td:nth-child('+val+')').hide();
                        $('.client_table_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('.drop_table_data button').on('click', function(){
                $('.client_report_list').toggleClass('active');
            });

            $('.client_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.client_report_data table tr td').show();
                        $('.client_report_data table tr th').show();
                        $('.client_report_list label.dropdown-option input').prop('checked', true);
                    } else {
                        $('.client_report_list label.dropdown-option input').prop('checked', false);
                        $('.client_report_data table tr td').hide();
                        $('.client_report_data table tr th').hide();
                        $('.client_report_data table tr td:nth-child(1)').show();
                        $('.client_report_data table tr th:nth-child(1)').show();
                        $('.client_report_data table tr td:nth-child(2)').show();
                        $('.client_report_data table tr th:nth-child(2)').show();
                        $('.client_report_data table tr td:nth-child(11)').show();
                        $('.client_report_data table tr th:nth-child(11)').show();
                    }
                }
                else
                {
                    if ($(this).is(":checked")) {
                        $('.client_report_data table tr td:nth-child('+val+')').show();
                        $('.client_report_data table tr th:nth-child('+val+')').show();
                    } else {
                        $('.client_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.client_report_data table tr td:nth-child('+val+')').hide();
                        $('.client_report_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('.drop_table_data button').on('click', function(){
                $('.application_report_list').toggleClass('active');
            });

            $('.application_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.application_report_data table tr td').show();
                        $('.application_report_data table tr th').show();
                        $('.application_report_list label.dropdown-option input').prop('checked', true);
                    }else{
                        $('.application_report_list label.dropdown-option input').prop('checked', false);
                        $('.application_report_data table tr td').hide();
                        $('.application_report_data table tr th').hide();
                        $('.application_report_data table tr td:nth-child(1)').show();
                        $('.application_report_data table tr th:nth-child(1)').show();
                        $('.application_report_data table tr td:nth-child(2)').show();
                        $('.application_report_data table tr th:nth-child(2)').show();
                        $('.application_report_data table tr td:nth-child(3)').show();
                        $('.application_report_data table tr th:nth-child(3)').show();
                        $('.application_report_data table tr td:nth-child(5)').show();
                        $('.application_report_data table tr th:nth-child(5)').show();
                        $('.application_report_data table tr td:nth-child(7)').show();
                        $('.application_report_data table tr th:nth-child(7)').show();
                    }
                } else {

                    if ($(this).is(":checked")) {
                        $('.application_report_data table tr td:nth-child('+val+')').show();
                        $('.application_report_data table tr th:nth-child('+val+')').show();
                    }
                    else{
                        $('.application_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.application_report_data table tr td:nth-child('+val+')').hide();
                        $('.application_report_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('.drop_table_data button').on('click', function(){
                $('.officevisit_report_list').toggleClass('active');
            });

            $('.officevisit_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.officevisit_report_data table tr td').show();
                        $('.officevisit_report_data table tr th').show();
                        $('.officevisit_report_list label.dropdown-option input').prop('checked', true);
                    }else{
                        $('.officevisit_report_list label.dropdown-option input').prop('checked', false);
                        $('.officevisit_report_data table tr td').hide();
                        $('.officevisit_report_data table tr th').hide();
                        $('.officevisit_report_data table tr td:nth-child(1)').show();
                        $('.officevisit_report_data table tr th:nth-child(1)').show();
                        $('.officevisit_report_data table tr td:nth-child(2)').show();
                        $('.officevisit_report_data table tr th:nth-child(2)').show();
                        $('.officevisit_report_data table tr td:nth-child(4)').show();
                        $('.officevisit_report_data table tr th:nth-child(4)').show();
                    }
                } else {
                    if ($(this).is(":checked")) {
                        $('.officevisit_report_data table tr td:nth-child('+val+')').show();
                        $('.officevisit_report_data table tr th:nth-child('+val+')').show();
                    }
                    else{
                        $('.officevisit_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.officevisit_report_data table tr td:nth-child('+val+')').hide();
                        $('.officevisit_report_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('.drop_table_data button').on('click', function(){
                $('.invoice_report_list').toggleClass('active');
            });

            $('.invoice_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.invoice_report_data table tr td').show();
                        $('.invoice_report_data table tr th').show();
                        $('.invoice_report_list label.dropdown-option input').prop('checked', true);
                    }else{
                        $('.invoice_report_list label.dropdown-option input').prop('checked', false);
                        $('.invoice_report_data table tr td').hide();
                        $('.invoice_report_data table tr th').hide();
                        $('.invoice_report_data table tr td:nth-child(1)').show();
                        $('.invoice_report_data table tr th:nth-child(1)').show();
                        $('.invoice_report_data table tr td:nth-child(2)').show();
                        $('.invoice_report_data table tr th:nth-child(2)').show();
                        $('.invoice_report_data table tr td:nth-child(4)').show();
                        $('.invoice_report_data table tr th:nth-child(4)').show();
                    }
                } else {

                    if ($(this).is(":checked")) {
                        $('.invoice_report_data table tr td:nth-child('+val+')').show();
                        $('.invoice_report_data table tr th:nth-child('+val+')').show();
                    }
                    else{
                        $('.invoice_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.invoice_report_data table tr td:nth-child('+val+')').hide();
                        $('.invoice_report_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('.drop_table_data button').on('click', function(){
                $('.saleforecast_applic_report_list').toggleClass('active');
            });

            $('.saleforecast_applic_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.saleforecast_application_report_data table tr td').show();
                        $('.saleforecast_application_report_data table tr th').show();
                        $('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', true);
                    }else{
                        $('.saleforecast_applic_report_list label.dropdown-option input').prop('checked', false);
                        $('.saleforecast_application_report_data table tr td').hide();
                        $('.saleforecast_application_report_data table tr th').hide();
                        $('.saleforecast_application_report_data table tr td:nth-child(1)').show();
                        $('.saleforecast_application_report_data table tr th:nth-child(1)').show();
                        $('.saleforecast_application_report_data table tr td:nth-child(2)').show();
                        $('.saleforecast_application_report_data table tr th:nth-child(2)').show();
                        $('.saleforecast_application_report_data table tr td:nth-child(4)').show();
                        $('.saleforecast_application_report_data table tr th:nth-child(4)').show();
                    }
                }else{
                    if ($(this).is(":checked")) {
                        $('.saleforecast_application_report_data table tr td:nth-child('+val+')').show();
                        $('.saleforecast_application_report_data table tr th:nth-child('+val+')').show();
                    }
                    else{
                        $('.saleforecast_applic_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.saleforecast_application_report_data table tr td:nth-child('+val+')').hide();
                        $('.saleforecast_application_report_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('.drop_table_data button').on('click', function(){
                $('.interest_service_report_list').toggleClass('active');
            });

            $('.interest_service_report_list label.dropdown-option input').on('click', function(){
                var val = $(this).val();
                if(val == 'all'){
                    if ($(this).is(":checked")) {
                        $('.interest_service_report_data table tr td').show();
                        $('.interest_service_report_data table tr th').show();
                        $('.interest_service_report_list label.dropdown-option input').prop('checked', true);
                    }else{
                        $('.interest_service_report_list label.dropdown-option input').prop('checked', false);
                        $('.interest_service_report_data table tr td').hide();
                        $('.interest_service_report_data table tr th').hide();
                        $('.interest_service_report_data table tr td:nth-child(1)').show();
                        $('.interest_service_report_data table tr th:nth-child(1)').show();
                        $('.interest_service_report_data table tr td:nth-child(2)').show();
                        $('.interest_service_report_data table tr th:nth-child(2)').show();
                        $('.interest_service_report_data table tr td:nth-child(10)').show();
                        $('.interest_service_report_data table tr th:nth-child(10)').show();
                        $('.interest_service_report_data table tr td:nth-child(14)').show();
                        $('.interest_service_report_data table tr th:nth-child(14)').show();
                    }

                }else{

                    if ($(this).is(":checked")) {
                        $('.interest_service_report_data table tr td:nth-child('+val+')').show();
                        $('.interest_service_report_data table tr th:nth-child('+val+')').show();
                    }
                    else{
                        $('.interest_service_report_list label.dropdown-option.all input').prop('checked', false);
                        $('.interest_service_report_data table tr td:nth-child('+val+')').hide();
                        $('.interest_service_report_data table tr th:nth-child('+val+')').hide();
                    }
                }
            });

            $('#personal_details .is_business').hide();
            $('#office_income_share .is_super_agent').hide();
            $('#office_income_share .is_sub_agent').hide();

            $('.modal-body form#addgroupinvoice .is_superagentinv').hide();

            $('#agentstructure input[name="struture"]').on('change', function(){
                var id = $(this).attr('id');
                if(id == 'individual'){
                    $('#personal_details .is_business').hide();
                    $('#personal_details .is_individual').show();
                    $('#personal_details .is_business input').attr('data-valid', '');
                    $('#personal_details .is_individual input').attr('data-valid', 'required');
                }
                else{
                    $('#personal_details .is_individual').hide();
                    $('#personal_details .is_business').show();
                    $('#personal_details .is_business input').attr('data-valid', 'required');
                    $('#personal_details .is_individual input').attr('data-valid', '');
                }
            });

            $('.modal-body form#addgroupinvoice input[name="partner_type"]').on('change', function(){
                var invid = $(this).attr('id');
                if(invid == 'superagent_inv'){
                    $('.modal-body form#addgroupinvoice .is_partnerinv').hide();
                    $('.modal-body form#addgroupinvoice .is_superagentinv').show();
                    $('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', '');
                    $('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', 'required');
                }
                else{
                    $('.modal-body form#addgroupinvoice .is_superagentinv').hide();
                    $('.modal-body form#addgroupinvoice .is_partnerinv').show();
                    $('.modal-body form#addgroupinvoice .is_partnerinv input').attr('data-valid', 'required');
                    $('.modal-body form#addgroupinvoice .is_superagentinv input').attr('data-valid', '');
                }
            });

            $('.modal .modal-body .is_partner').hide();
            $('.modal .modal-body .is_application').hide();
            $('.modal .modal-body input[name="related_to"]').on('change', function(){
                var relid = $(this).attr('id');
                if(relid == 'contact'){
                    $('.modal .modal-body .is_partner').hide();
                    $('.modal .modal-body .is_application').hide();
                    $('.modal .modal-body .is_contact').show();
                    $('.modal .modal-body .is_partner select').attr('data-valid', '');
                    $('.modal .modal-body .is_application select').attr('data-valid', '');
                    $('.modal .modal-body .is_contact select').attr('data-valid', 'required');
                }
                else if(relid == 'partner'){
                    $('.modal .modal-body .is_contact').hide();
                    $('.modal .modal-body .is_application').hide();
                    $('.modal .modal-body .is_partner').show();
                    $('.modal .modal-body .is_contact select').attr('data-valid', '');
                    $('.modal .modal-body .is_application select').attr('data-valid', '');
                    $('.modal .modal-body .is_partner select').attr('data-valid', 'required');
                }
                else if(relid == 'application'){
                    $('.modal .modal-body .is_contact').hide();
                    $('.modal .modal-body .is_partner').hide();
                    $('.modal .modal-body .is_application').show();
                    $('.modal .modal-body .is_contact select').attr('data-valid', '');
                    $('.modal .modal-body .is_partner select').attr('data-valid', '');
                    $('.modal .modal-body .is_application select').attr('data-valid', 'required');
                }
                else{
                    $('.modal .modal-body .is_contact').hide();
                    $('.modal .modal-body .is_partner').hide();
                    $('.modal .modal-body .is_application').hide();
                    $('.modal .modal-body .is_contact input').attr('data-valid', '');
                    $('.modal .modal-body .is_partner input').attr('data-valid', '');
                    $('.modal .modal-body .is_application input').attr('data-valid', '');
                }
            });

            $('#agenttype input#super_agent').on('click', function(){
                if ($(this).is(":checked")) {
                    $('#office_income_share .is_super_agent').show();
                }
                else{
                    $('#office_income_share .is_super_agent').hide();
                }
            });

            $('#agenttype input#sub_agent').on('click', function(){
                if ($(this).is(":checked")) {
                    $('#office_income_share .is_sub_agent').show();
                } else{
                    $('#office_income_share .is_sub_agent').hide();
                }
            });

            $('#internal select[name="source"]').on('change', function(){
                var sourceval = $(this).val();
                if(sourceval == 'Sub Agent'){
                    $('#internal .is_subagent').show();
                    $('#internal .is_subagent select').attr('data-valid', 'required');
                } else{
                    $('#internal .is_subagent').hide();
                    $('#internal .is_subagent select').attr('data-valid', '');
                }
            });

            $('.card .card-body .grid_data').hide();
            $('.card .card-body .document_layout_type a.list').on('click', function(){
                $('.card .card-body .document_layout_type a').removeClass('active');
                $(this).addClass('active');
                $('.card .card-body .grid_data').hide();
                $('.card .card-body .list_data').show();
            });

            $('.card .card-body .document_layout_type a.grid').on('click', function(){
                $('.card .card-body .document_layout_type a').removeClass('active');
                $(this).addClass('active');
                $('.card .card-body .list_data').hide();
                $('.card .card-body .grid_data').show();
            });

            $('.js-data-example-ajax-check').on("select2:select", function(e) {
                var data = e.params.data;
                console.log(data);
                $('#utype').val(data.status);
            });

            $('.js-data-example-ajax-check').select2({
                multiple: true,
                closeOnSelect: false,
                dropdownParent: $('#checkinmodal'),
                ajax: {
                    url: '{{URL::to('/admin/clients/get-recipients')}}',
                    dataType: 'json',
                    processResults: function (data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        return {
                            results: data.items
                        };
                    },
                    cache: true
                },
                templateResult: formatRepocheck,
                templateSelection: formatRepoSelectioncheck
            });

            function formatRepocheck (repo) {
                if (repo.loading) {
                    return repo.text;
                }

                var $container = $(
                    "<div  class='select2-result-repository ag-flex ag-space-between ag-align-center'>" +

                    "<div  class='ag-flex ag-align-start'>" +
                        "<div  class='ag-flex ag-flex-column col-hr-1'><div class='ag-flex'><span  class='select2-result-repository__title text-semi-bold'></span>&nbsp;</div>" +
                        "<div class='ag-flex ag-align-center'><small class='select2-result-repository__description'></small ></div>" +

                    "</div>" +
                    "</div>" +
                    "<div class='ag-flex ag-flex-column ag-align-end'>" +

                        "<span class='select2resultrepositorystatistics'>" +

                        "</span>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-repository__title").text(repo.name);
                $container.find(".select2-result-repository__description").text(repo.email);
                if(repo.status == 'Archived'){
                    $container.find(".select2resultrepositorystatistics").append('<span class="ui label  select2-result-repository__statistics">'+repo.status+'</span>');
                }else{
                    $container.find(".select2resultrepositorystatistics").append('<span class="ui label yellow select2-result-repository__statistics">'+repo.status+'</span>');
                }
                return $container;
            }

            function formatRepoSelectioncheck (repo) {
                return repo.name || repo.text;
            }

            /* $('.timepicker').timepicker({
                minuteStep: 1,
                showSeconds: true,
            }); */
        });

        $(document).ready(function(){
            document.getElementById('countbell_notification').parentNode.addEventListener('click', function(event){
            window.location = "/admin/all-notifications";
        })

        function load_unseen_notification(view = '')
        {
            $.ajax({
                url:"{{URL::to('/admin/fetch-notification')}}",
                method:"GET",
                dataType:"json",
                success:function(data)
                {
                    //$('.showallnotifications').html(data.notification);
                    if(data.unseen_notification > 0)
                    {
                        $('.countbell').html(data.unseen_notification);
                    }
                }
            });
        }

        function load_unseen_messages(view = '')
        {
            load_unseen_notification();
            var playing = false;
            $.ajax({
                url:"{{URL::to('/admin/fetch-messages')}}",
                method:"GET",
                success:function(data)
                {
                    if(data != 0){
                        iziToast.show({
                            backgroundColor: 'rgba(0,0,255,0.3)',
                            messageColor: 'rgba(255,255,255)',
                            title: '',
                            message: data,
                            position: 'bottomRight'
                        });
                        $(this).toggleClass("down");

                        if (playing == false) {
                            document.getElementById('player').play();
                            playing = true;
                            $(this).text("stop sound");

                        } else {
                            document.getElementById('player').pause();
                            playing = false;
                            $(this).text("restart sound");
                        }
                    }
                }
            });
        }

        /*function load_InPersonWaitingCount(view = '') {
            $.ajax({
                url:"{{URL::to('/admin/fetch-InPersonWaitingCount')}}",
                method:"GET",
                dataType:"json",
                success:function(data) {
                    //$('.showallnotifications').html(data.notification);
                    if(data.InPersonwaitingCount > 0){
                        $('.countInPersonWaitingAction').html(data.InPersonwaitingCount);
                    }
                }
            });
        }

        function load_TotalActivityCount(view = '') {
            $.ajax({
                url:"{{URL::to('/admin/fetch-TotalActivityCount')}}",
                method:"GET",
                dataType:"json",
                success:function(data) {
                    if(data.assigneesCount > 0){
                        $('.countTotalActivityAction').html(data.assigneesCount);
                    }
                }
            });
        }*/


        setInterval(function(){
            //load_unseen_notification();
            load_unseen_messages();
            //load_InPersonWaitingCount();
            //load_TotalActivityCount();
        },120000);
    });
    </script>
    <script>
    $(document).ready(function () {
        // Sidebar toggle functionality
        $('.collapse-btn').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-mini');
            $('.main-sidebar').toggleClass('sidebar-expanded');

            if ($('.main-sidebar').hasClass('sidebar-expanded')) {
                $('.main-content').css('margin-left', '220px');
                localStorage.setItem('sidebarState', 'expanded');
            } else {
                $('.main-content').css('margin-left', '80px');
                localStorage.setItem('sidebarState', 'collapsed');
            }
        });

        // Set initial state based on localStorage
        const sidebarState = localStorage.getItem('sidebarState');
        if (sidebarState === 'expanded') {
            $('body').removeClass('sidebar-mini');
            $('.main-sidebar').addClass('sidebar-expanded');
            $('.main-content').css('margin-left', '220px');
        } else {
            $('body').addClass('sidebar-mini');
            $('.main-sidebar').removeClass('sidebar-expanded');
            $('.main-content').css('margin-left', '80px');
        }
    });
    </script>

<div id="checkinmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Create In Person Client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="checkinmodalsave" id="checkinmodalsave" action="{{URL::to('/admin/checkin')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf

					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Search Contact <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax-check" name="contact"></select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<input type="hidden" id="utype" name="utype" value="">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">Office <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control" name="office">
									<option value="">Select</option>
									@foreach(\App\Models\Branch::all() as $of)
										<option value="{{$of->id}}">{{$of->office_name}}</option>
									@endforeach
								</select>

							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Visit Purpose <span class="span_req">*</span></label>
								<textarea class="form-control" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Select In Person Assignee <span class="span_req">*</span></label>
								<?php
								$assignee = \App\Models\Admin::where('role','!=', '7')->get();
								?>
								<select class="form-control assineeselect2" name="assignee">
								@foreach($assignee as $assigne)
									<option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
								@endforeach
								</select>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('checkinmodalsave')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="checkindetailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">In Person Details</h5>
				<a style="margin-left:10px;" href="javascript:;"><i class="fa fa-trash"></i> Archive</a>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showchecindetail">

			</div>
		</div>
	</div>
</div>
    @stack('scripts')
</body>
</html>
