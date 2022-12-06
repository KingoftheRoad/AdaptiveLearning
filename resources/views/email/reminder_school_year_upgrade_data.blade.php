<p>Hi,{{$school->DecryptNameEn ?? $school->name}}<p>
<p></p>
<p>Please be reminded to upload the new student css file for the new curriculum year {{$curriculum_year}}. <a href="{{$upload_student_url}}">Click here</a> to upload it otherwise, the users of your school will not be able to access the data of {{$curriculum_year}} after 1st Sept {{((int)date('Y')+1)}}.</p>
<p>You will also need to re-assign teachers to classes for the new curriculum year. <a href="{{$teacher_class_assignment_url}}">Click here.</a></p>