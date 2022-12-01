
<style>
.que-ans{
    padding-left: 15px;
}
.title-que span{
    display: inline-block;
}
.title-que p{
    margin: 0;
}
.title-que div:first-child{
    display: flex;
    align-items: baseline;
}
.title-que span{
    padding-right: 5px;
}
.title-que div p:first-child{
    padding-bottom: 10px;
}
.pl-2{
    padding-left: 20px;
}
</style>
<div id="question-list">
    @if(!empty($questionData))
    @foreach ($questionData as $key => $questions)
    <div class="que-ans">
        <div class="title-que">
            <div>
                <span>{{$loop->iteration}})</span>
            <div>
            <?php echo $questions->question_en; ?>
            <?php echo $questions->question_ch; ?>
        </div>
    </div>
</div>

<p class="pl-2"> English Answer</p>
<ul>
    <li><p><?php echo $questions->answers->answer1_en; ?></p></li>
    <li><p><?php echo $questions->answers->answer2_en; ?></p></li>
    <li><p><?php echo $questions->answers->answer3_en; ?></p></li>
    <li><p><?php echo $questions->answers->answer4_en; ?></p></li>
</ul>

<p class="pl-2"> Chinese Answer </p>
<ul>
    <li><p><?php echo $questions->answers->answer1_ch; ?></p></li>
    <li><p><?php echo $questions->answers->answer2_ch; ?></p></li>
    <li><p><?php echo $questions->answers->answer3_ch; ?></p></li>
    <li><p><?php echo $questions->answers->answer4_ch; ?></p></li>
</ul>
</div>
    @endforeach
@endif
</div>


<script src="{{ asset('js/jquery/3.4.1/jquery.min.js') }}"></script>
<script src="{{ asset('ckeditor_wiris/wiris/integration/WIRISplugins.js?viewer=image')}}"></script>

<script>
if ("com" in window && "wiris" in window.com && "js" in window.com.wiris && "JsPluginViewer" in window.com.wiris.js) {
    com.wiris.js.JsPluginViewer.parseDocument();
}
<script>
