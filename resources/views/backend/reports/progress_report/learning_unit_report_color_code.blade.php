<div class="row study_status_colors">
    <div class="study_status_colors-sec">
        <strong>{{__('languages.study_status')}}:</strong>
    </div>
    <div class="study_status_colors-sec">
        <span class="dot-color" style="background-color:{{$ColorCodes['accomplished_color']}};border-radius: 50%;display: inline-block;"></span>
        <label>{{__('languages.accomplished')}} ({{__('languages.mastered')}})</label>
    </div>
    <div class="study_status_colors-sec">
        <span class="dot-color" style="background-color:{{$ColorCodes['not_accomplished_color']}};border-radius: 50%;display: inline-block;"></span>
        <label>{{__('languages.not_accomplished')}}</label>
    </div>
</div>