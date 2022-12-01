@extends('frontend.layouts.app')

@section('content')
    @include('frontend.layouts.header')
    <section class="main-section pl-5 pr-5">
        <div class="container-fluid first-section">
            <div class="row">                  
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="adaptive-learning-img">
                        <img src="{{ asset('frontend/images/Logo-tran.png') }}" alt="about-image" class="about-img img-responsive">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="about-right-sec">
                        <div class="detail-sec">
                            <h2 class="title">What is the Adaptive Learning Platform?</h2>
                            <p class="discription">The Adaptive Learning Platform is a multi-purposed, student-centered e-learrning platform that uses Artificial Intelligence to cater for individual student's needs and learning pace.</p>
                        </div>
                        <div class="detail-sec">
                            <h2 class="title">Who is it for?</h2>
                            <p class="discription">It is built for students, parents and teachers in Hong Kong. Its built-in diagnostic design with lesson planning features and report generating functions enable uses to use it for assessment at school or self study at home.</p>
                        </div>
                        <div class="detail-sec">
                            <h2 class="title">What curriculum and subject does the platform cover?</h2>
                            <p class="discription">It is built for students, parents and teachers in The platform has a fully mapped knowledge structure that is tailored for the Hong Kong curriculum. Currently the platform focuses on Mathematics at the senior secondary S. 4 to S.6.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="platform-special-sec pl-5 pr-5">
        <div class="container-fluid">
            <div class="row">                  
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="about-right-sec">
                        <div class="detail-sec">
                            <h2 class="title">What makes this platform special?</h2>
                            <p class="discription">The platform aims to supplement learning and teaching at school by offering more individual attention to each learner's needs. It aims to achieve:</p>
                            <ul>
                                <li>Analysis of students learning motivation</li>
                                <li>Focusing on students learning goals</li>
                                <li>Mastery of students learning process</li>
                                <li>Recognition of students learning difficulties</li>
                                <li>Automatic offer of learning paths that vary according to changes in students learning abilities and attitudes</li>
                            </ul>
                        </div>
                        <div class="detail-sec">
                            <h2 class="title">Who designs it?</h2>
                            <p class="discription">The adaptive Learning Platform is the brainchild of group of dedicated principals at the largest local school principal association, namely the Hong Kong Association of the Heads of Secondary Schools(HKAHSS).</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="adaptive-learning-img">
                        <img src="{{ asset('frontend/images/Rectangle-5.png') }}" alt="about-image" class="about-img img-responsive">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="platform-free-sec pr-5 pl-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="detail-sec">
                        <h2 class="title">Is the platform free?</h2>
                        <p class="discription">The platform hass been generously supported by donations from the WYNG Foundation since 2019 and all local secondary schools can apply to use it free of charge. Please write to ????@hkahss.edu.hk for application.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- <div class="home-section">
    <div class="container-fluid">
            <div class="row">                
                <div class="col-md-6 col-sm-6">
                <h2 class="lg-title">What makes this platform special?</h2>
                <p class="p1">The platform aims to supplement learning and teaching at school by offering more individual attention to each learner's needs. It aims to achieve:</p>
                <ul>
                                    <li class="p1">Analysis of students learning motivation</li>
                                    <li class="p1">Focusing on students learning goals</li>
                                    <li class="p1">Mastery of students learning process</li>
                                    <li class="p1">Recognition of students learning difficulties</li>
                                    <li class="p1">Automatic offer of learning paths that vary according to changes in students learning abilities and attitudes</li>
                                </ul>
                                <h2 class="lg-title">Who designs it?</h2>
                                <p class="p1">The adaptive Learning Platform is the brainchild of group of dedicated principals at the largest local school principal association, namely the Hong Kong Association of the Heads of Secondary Schools(HKAHSS).</p>
                </div>
                <div class="col-md-6 col-sm-6">
                    <img src="{{ asset('frontend/images/Logo-tran.png') }}" alt="about-image" class="about-img img-responsive">
                </div>
                <div class="col-md-12 col-sm-12">
                    
                <h2 class="lg-title">Is the platform free?</h2>
                 <p class="p1">The platform hass been generously supported by donations from the WYNG Foundation since 2019 and all local secondary schools can apply to use it free of charge. Please write to ????@hkahss.edu.hk for application.</p>
                </div>

            </div>
        </div>
    </div> -->
    @include('frontend.layouts.footer')
@endsection