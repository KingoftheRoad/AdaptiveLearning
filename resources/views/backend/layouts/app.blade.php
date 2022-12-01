<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php 
        $SettingData = \App\Helpers\Helper::getSettingData();
    @endphp
    @if(!empty($SettingData))
    <title>{{ $SettingData->site_name }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($SettingData->fav_icon) }}">
    @endif
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-confirm/jquery-confirm.min.css') }}">
    <link href="{{ asset('css/jquery-ui.css')}}" rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/Timepicker/jquery.timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables/jquery.dataTables.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
        
    <!-- multi select css -->
    <link href="{{ asset('css/multiselect_dropdown/bootstrap-multiselect.css')}}" rel="stylesheet" />
    <!-- End multi select css -->
        
    <link rel="stylesheet" href="{{asset('js/jstree/dist/themes/default/style.min.css')}}" type="text/css"/>

    @if(env('ALP_SERVER')=='localhost')
    <link href="{{ asset('css/backend-style.css') }}" rel="stylesheet">
    @else
    <link href="{{ asset('css/backend-style.min.css') }}" rel="stylesheet">
    @endif
    
    <link rel="stylesheet" href="{{asset('css/select2.min.css')}}"/>
    <link href="{{asset('css/jquery.timepicker.min.css')}}" rel="stylesheet"/>

    <script src="{{ asset('js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ asset('toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/jquery/3.4.1/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.cookie.min.js')}}"></script>
    <script>        
        var BASE_URL = "{{ URL::to('/') }}";
        var CSRF_TOKEN = "{{ csrf_token() }}";
        var APP_ENV = "{{env('APP_ENV','local')}}";
        var ALP_SERVER = "{{env('ALP_SERVER')}}";
        var ALP_CHAT_BASE_URL = "{{env('ALP_CHAT_BASE_URL')}}";
        var APP_LANGUAGE = "{{app()->getLocale()}}";
    </script>

    <!-- Start Load language js file -->
    @if(app()->getLocale() == 'en')
        @if(env('ALP_SERVER')=='localhost')
        <script type="text/javascript" src="{{ asset('js/languages/language_en.js')}}"></script>
        @else
        <script type="text/javascript" src="{{ asset('js/languages/language_en.min.js')}}"></script>
        @endif
    @endif
    @if(app()->getLocale() == 'ch')
        @if(env('ALP_SERVER')=='localhost')
        <script type="text/javascript" src="{{ asset('js/languages/language_ch.js')}}"></script>
        @else
        <script type="text/javascript" src="{{ asset('js/languages/language_ch.min.js')}}"></script>
        @endif
    @endif
    <!-- End Load language js file -->

    <!-- Start Load js for firebase configurations -->
    <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
    <script type="text/javascript" src="{{asset('js/firebase/firebase_configurations.js')}}"></script>
    @if(env('ALP_SERVER')=='localhost')
        <script type="text/javascript" src="{{asset('js/firebase/firebase_operations.js')}}"></script>
    @else
        <script type="text/javascript" src="{{asset('js/firebase/firebase_operations.min.js')}}"></script>
    @endif
    <!-- End Load js for firebase configurations -->
</head>
<body>
    <div class="loader"></div>
    <div id="cover-spin"></div>
    @yield('content')
    <?php if(!empty(Session::get('sidebar_option'))){ ?>
        <script>
            $('#content').addClass('<?php echo Session::get('sidebar_option'); ?>');
        </script>
    <?php }else{ ?>
        <script> $('#content').addClass('sidebar-close');</script>
    <?php }?>

    <?php if(!empty(Session::get('sidebar'))){ ?>
        <script>
            $('#sidebar').addClass('<?php echo Session::get('sidebar'); ?>');
        </script>
    <?php }else{ ?>
        <script> $('#sidebar').addClass('active');</script>
    <?php }?>

    <!--  JS File -->
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/additional-methods.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}" ></script>
    
	{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script> --}}
    <script src={{asset('js/drop-zone/dropzone.min.js')}}></script>
	
    <!-- js for multiselect Start -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script> --}}
    <script src="{{ asset('js/multiselect_dropdown/bootstrap-multiselect.js') }}"></script>
    <!-- js for multiselect End -->

    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/jquery-confirm/jquery-confirm.min.js') }}"></script>
    <script src="{{ asset('js/Timepicker/jquery.timepicker.min.js') }}"></script>
    {{-- <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script> --}}
    <!-- <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script> -->
    <script src="{{ asset('js/MathJX/tex-mml-chtml.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_SVG"></script>

    <!-- <script src="https://www.wiris.net/demo/plugins/app/WIRISplugins.js?viewer=image&async=true"></script> -->
    <!-- <script src="{{ asset('wiris/integration/WIRISplugins.js?viewer=image&async=true')}}"></script> -->
    <!-- <script src="{{ asset('ckeditor_wiris/integration/WIRISplugins.js?viewer=image&async=true')}}"></script> -->
    
    <!-- <script src="{{ asset('ckeditor_wiris/wiris/integration/WIRISplugins.js?viewer=svg&async=true')}}"></script> -->

    <!-- <script src="{{ asset('wiris/integration/WIRISplugins.js?viewer=svg&async=true')}}"></script> -->
    <!-- <script src="{{ asset('ckeditor_wiris/ckeditor5/plugins/wiris/integration/WIRISplugins.js?viewer=image&async=true')}}"></script> -->
    <!-- <script type="text/javascript" src="{{ asset('ckeditor_wiris/js/wirislib.js')}}"></script> -->

    {{-- <script src="{{ asset('wirislibeditor/js/wirislib.js')}}"></script> --}}
    {{-- Convert and display proper math formula into ui page --}}
    {{-- <script src="{{ asset('js/MathJX/mathscript.js') }}"></script> --}}
        
    <!-- For DataTable Value Export to Pdf Start -->
    <!-- <script src="{{ asset('js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('js/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/datatables/buttons.html5.min.js') }}"></script> -->
    <!-- End For DataTable Value Export to Pdf ENd -->

    <!-- Start For Searchable Dropdown -->
    <script src="{{asset('js/select2.min.js')}}"></script>
    <!-- End For Searchable Dropdown -->

    <!-- <script src="{{ asset('js/main.js') }}"></script> -->
	<!-- <script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script> -->
    <script src="{{ asset('js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/datatables/moment.js') }}"></script>
    <script src="{{ asset('js/datatables/datetime-moment.js') }}"></script>

    {{-- for inpt mask --}}
    <script src="{{ asset('js/input-mask/jquery.inputmask.bundle.js') }}"></script>
    <script src="{{ asset('js/input-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('js/input-mask/input_mask_validation.js') }}"></script>

    <script src="{{asset('js/jquery.timepicker.min.js')}}"></script>

    @if(env('ALP_SERVER')=='localhost')
    <script src="{{ asset('js/script.js') }}" defer></script>
    @else
    <script src="{{ asset('js/script.min.js') }}" defer></script>
    @endif

    <script src="{{ asset('js/scroll.js') }}" defer></script>
</body>
</html>
