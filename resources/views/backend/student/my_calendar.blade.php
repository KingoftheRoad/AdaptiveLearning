@extends('backend.layouts.app')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" integrity="sha512-KXkS7cFeWpYwcoXxyfOumLyRGXMp7BTMTjwrgjMg0+hls4thG2JGzRgQtRfnAuKTn2KWTDZX4UdPg+xTs8k80Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<style>
		#calendar .btn.btn-primary {
			background: #000 !important;
			border-color: #000 !important;
		}
	</style>
    @section('content')
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec1 pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.my_calender')}}</h2>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12 bg-white p-2">
								
            					<div id="calendar"></div>
						</div>
					</div>
				</div>
		      </div>
			</div>
		</div>
		@include('backend.layouts.footer')
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js" integrity="sha512-o0rWIsZigOfRAgBxl4puyd0t6YKzeAw9em/29Ag7lhCQfaaua/mDwnpE2PVzwqJ08N7/wqrgdjc2E0mwdSY2Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script type="text/javascript">
			var calendar = '';

    (function () {
        'use strict';
        // ------------------------------------------------------- //
        // Calendar
        // ------------------------------------------------------ //
        $(function () {
            // page is ready

            calendar = $('#calendar').fullCalendar({
		                themeSystem: 'bootstrap4',
		                // emphasizes business hours
		                businessHours: false,
		                defaultView: 'month',
		                // event dragging & resizing
		                displayEventTime : false,
		                editable: false,
		                timeFormat: 'h(:mm) T',
		                // header
		                header: {
		                    left: 'title',
		                    center: 'month,agendaWeek,agendaDay',
		                    right: 'today prev,next'
		                },
		                events: [
								@if(!empty($examList))
									@foreach($examList as $exam)
									{  
										title: 'Exam ({{ $exam->title }})',
										date : '{{ $exam->publish_date }}',
										start: '{{ $exam->publish_date }}',
										end: '{{ $exam->publish_date }}',
										className: 'fc-bg-default',
									},
									@endforeach
								@endif
					],
					dayRender: function( date, cell ) {
					},
					monthRender: function( date, cell ) {
					},
		            eventRender: function(event, element,date) {
		                if (event.icon) {
		                    element.find(".fc-title").prepend("<i class='fa fa-" + event.icon + "'></i>");
		                }
		            },
		            dayClick: function() {

		            },
		            eventClick: function(event, jsEvent, view) {
		                $('#modal-view-event').modal("toggle");
		            },
		        })
		    });
		  	$(document).on('click','#calendar button[aria-label=next],#calendar button[aria-label=prev]',function () {
				$('#calendar').fullCalendar('removeEvents');
				
		  		var month_date=$('#calendar').fullCalendar( 'getDate');
		  		var month = month_date._d.getMonth() + 1;
		  		var year = month_date._d.getFullYear();
				  $.ajax({
					url: BASE_URL + '/selectMonthData',
					method: "POST",
					data: { "month": month,"year": year,'_token': $('meta[name="csrf-token"]').attr('content')},
					success: function (data) {
            			var examList=data.examList;
            			var GroupList=data.examList.GroupList;
						$.each(examList, function (key, value) {
							if(key=='GroupList')
							{

							}
							else
							{
								var title_lbl=value.title;
								var group_ids=value.group_ids;

								if(value.is_group_test==1)
								{
									title_lbl=GroupList[group_ids];
								}
								var eventData = {
									title: 'Exam ('+title_lbl+')',
									date : value.publish_date,
									start: value.publish_date,
									end: value.publish_date,
									className: 'fc-bg-default',
								};
								$('#calendar').fullCalendar('renderEvent', eventData, true);
							}
						});
					}
					});
		  	})
		}) (jQuery);
		</script>
@endsection