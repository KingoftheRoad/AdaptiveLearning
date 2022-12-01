
@php
    if(Auth::user()->role_id == 1){
        $color = '#A5A6F6';
    }else if(Auth::user()->role_id==2){
        $color = '#f7bfbf';
    }else if(Auth::user()->role_id==3){
        $color = '#d8dc41';
    }else if(Auth::user()->role_id == 7){
        $color = '#BDE5E1';
    }else{
        $color = '#a8e4b0';
    }
    $languageList = \App\Models\Languages::all();
@endphp
<nav class="navbar navbar-expand-lg" style="background-color:{{$color}};">
    <div class="container-fluid">

    <button type="button" id="sidebarCollapse" class="btn btn-primary tonggel-btn">
        <i class="fa fa-bars"></i>
        <span class="sr-only">Toggle Menu</span>
    </button>
    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-bars"></i>
    </button>
    <div class="langague-dropdown">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                {{ Config::get('languages')[App::getLocale()] }}
            </a>
            <ul class="dropdown-menu">
                @foreach ($languageList as $language)
                    @if ($language['code'] != App::getLocale())
                    <li>
                        <a href="{{ route('lang.switch', $language['code']) }}">{{$language['name']}}</a>
                    </li>
                    @endif
                @endforeach
            </ul>
        </li>
    </div>
    <div class="super-admin-title">
    <h4>{{ auth()->user()->id }} : {{ (auth()->user()->name_en) ? App\Helpers\Helper::decrypt(auth()->user()->name_en) : auth()->user()->name }}</h4>
    </div>
    
    <!-- <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="nav navbar-nav ml-auto">
        <li class="nav-item active">
            <a class="nav-link" href="#">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Subject</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Knowledge tree</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Text book</a>
        </li>
        <li class="nav-item">
            <a class="nav-link nav-icon" href="#"><img src="{{ asset('images/frame.png') }}" alt="icon"></a>
        </li>
        <li class="nav-item">
            <a class="nav-link nav-icon" href="#"><img src="{{ asset('images/men-icon.png') }}" alt="icon"></a>
        </ul>
    </div> -->
    </div>
</nav>