@if(\App\Helpers\Helper::isStudentLogin())
<div class="row pb-4">
    <div class="col-sm-12 col-md-12 col-lg-12">
        {{-- <a href="{{ route('student.student-profiles',auth()->user()->id) }}" class="btn-search @if(request()->is('student/students-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a> --}}
        <a href="{{ route('student-profiles',auth()->user()->id) }}" class="btn-search @if(request()->is('student-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a>
        <a href="{{ route('credit-point-history',auth()->user()->id) }}" class="btn-search @if(request()->is('credit-point-history/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.credit_point_history') }}</a>
        <a href="{{ route('student.progress-report.learning-units',auth()->user()->id) }}" class="btn-search @if(request()->is('student/progress-report/learning-units/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_units') }} {{__('languages.report_text')}}</a>
        <a href="{{ route('student.progress-report.learning-objective',auth()->user()->id) }}" class="btn-search @if(request()->is('student/progress-report/learning-objective/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_objectives') }} {{__('languages.report_text')}}</a>
    </div>
</div>
@else
<div class="row pb-4">
    <div class="col-sm-12 col-md-12 col-lg-12">
        {{-- <a href="{{ route('teacher.student-profiles',$studentId) }}" class="btn-search @if(request()->is('teacher/students-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a> --}}
        <a href="{{ route('student-profiles',$studentId) }}" class="btn-search @if(request()->is('student-profile/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.my_class.personal_details') }}</a>
        <a href="{{ route('credit-point-history',$studentId) }}" class="btn-search @if(request()->is('credit-point-history/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.credit_point_history') }}</a>
        <a href="{{ route('student.progress-report.learning-units',$studentId) }}" class="btn-search @if(request()->is('student/progress-report/learning-units/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_units') }} {{__('languages.report_text')}}</a>
        <a href="{{ route('student.progress-report.learning-objective',$studentId) }}" class="btn-search @if(request()->is('student/progress-report/learning-objective/*')) active-btn @else white-font inactive-btn @endif">{{ __('languages.learning_objectives') }} {{__('languages.report_text')}}</a>
    </div>
</div>
@endif