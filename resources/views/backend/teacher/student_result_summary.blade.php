<table id="student-report-summary-table" class="display" style="width:100%" border="2">
    <thead>
        @if(!empty($records['heading']) && isset($records['heading']))
            <tr>
                @foreach($records['heading'] as $heading)
                <th class="text-center">{{$heading}}</th>
                @endforeach
            </tr>
        @endif
    </thead>
    <tbody>
        
        @if(!empty($records) && isset($records))
            @foreach($records as $key=>$record)
                @if($key == 'heading')
                    @continue
                @endif
                <tr>
                    @foreach($record as $key => $Data)
                        <td class="text-center">{{$Data}}</td>
                    @endforeach
                </tr>
            @endforeach
        @endif
    </tbody>
</table>