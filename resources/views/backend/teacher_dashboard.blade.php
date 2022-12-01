@extends('backend.layouts.app')
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
        <div id="content" class="pl-2 pb-5">
          @include('backend.layouts.header')
          <div class="sm-right-detail-sec pl-5 pr-5">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
                  <div class="sec-title">
                  <h2 class="mb-4 main-title">{{__('languages.welcome_to')}} <?php if(Auth::user()->name_en) { echo  App\Helpers\Helper::decrypt(Auth::user()->name_en);  } else { echo  Auth::user()->name; } ?></h2>
                      {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p> --}}
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
	  </div>
    @include('backend.layouts.footer')
@endsection