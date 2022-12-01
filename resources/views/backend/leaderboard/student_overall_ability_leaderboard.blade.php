<div class="leaders">
    @if($studentList->isNotEmpty())
        @foreach($studentList as $student)
        @php
            $randomColor = implode(',',\App\Helpers\Helper::RandomColorGenerator());
        @endphp

        <div class="leader" style="animation-delay: 0s;">
            <div class="leader-wrap">
                 <div class="leader-ava" > {{-- style="background-color:rgb(<?= $randomColor; ?>);" --}}
                    {{-- <span class="leaderboard_rank" style="background-color:rgb(<?= $randomColor; ?>);">{{$loop->iteration}}</span> --}}
                    @if(!empty($student->profile_photo))
                        <img src="{{asset($student->profile_photo)}}"  class="credit_point_image" alt="credit Point">
                    @else
                        <img src="{{asset('images/credit.png')}}"  class="credit_point_image" alt="credit Point">
                    @endif
                </div>
                <div class="leader-content">
                    <div class="leader-name">{{$student->DecryptNameEn}}</div>
                    <div class="leader-score">
                        <div class="leader-score_title"><b>{{round($student->overall_ability,2)}} ({{$student->NormalizedOverAllAbility ?? 0}})%</b></div>
                    </div>
                </div>
            </div>
            <div class="leader-bar" style="animation-delay: 0.4s;">
                <div class="bar" style="background-color:rgb(<?= $randomColor; ?>); width: <?= $student->NormalizedOverAllAbility ?? 0?>%;"></div>
            </div>
        </div>
        @endforeach
    @else
        <b><p align="center">{{__('languages.no_any_data')}}</p></b>                               
    @endif
</div>