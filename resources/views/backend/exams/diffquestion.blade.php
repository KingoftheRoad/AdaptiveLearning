@extends('backend.layouts.app')
    @section('content')
        <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
          <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
                <div class="container-fluid">
                    @if(session()->has('success_msg'))
                    <div class="alert alert-success">
                        {{ session()->get('success_msg') }}
                    </div>
                    @endif
                    @if(session()->has('error_msg'))
                    <div class="alert alert-danger">
                        {{ session()->get('error_msg') }}
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="sec-title">
                                <h2 class="mb-4 main-title">{{__('Exams Attempt')}}</h2>
                            </div>
                            <hr class="blue-line">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12 attmp-main-timer">
                            <div class="attmp-timer-inr">
                                <h5>Exam Time : </h5>
                                <p>01:00:00</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
                            <div class="attmp-main-que">
                                <h4>Q-1</h4>
                                <p class="attmp-que">11 + 11 = ? ?</p>
                                <img src="" class="que-graph">
                            </div>
                            <div class="attmp-main-answer">
                                <div class="attmp-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_1" value="1" class="radio mr-2" checked="">
                                    <div class="answer-title mr-2">A</div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                            <div class="anser-detail pl-2">
                                                <p>10</p>                                                  
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="attmp-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_1" value="2" class="radio mr-2">
                                    <div class="answer-title mr-2">B</div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                            <div class="anser-detail pl-2">
                                                <p>20</p>                                                   
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="attmp-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_1" value="2" class="radio mr-2">
                                    <div class="answer-title mr-2">B</div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                            <div class="anser-detail pl-2">
                                                <p>22</p>                                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="attmp-ans pl-2 pb-2">
                                    <input type="radio" name="ans_que_1" value="2" class="radio mr-2">
                                    <div class="answer-title mr-2">B</div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                            <div class="anser-detail pl-2">
                                                <p>30</p>                                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="attmp-main-explain">
                                <div class="attmp-expln-inner">
                                    <h5>Explain</h5>
                                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-sm-12 attmp-all-button">
                            <div class="attmp-prev-btn attmp-butns">
                                <a href="" class="prev-btn">Prev</a>
                            </div>
                            <div class="attmp-next-btn attmp-butns">
                                <a href="" class="next-btn">Next</a>
                            </div>
                            <div class="attmp-submit-btn attmp-butns">
                                <a href="" class="submit-btn">Submit</a>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection