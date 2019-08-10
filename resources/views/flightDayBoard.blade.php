

@foreach ($flightDays as $day)
	<hr>
    <h2 style="margin-top: 20px;">{{ $day['DATE'] }}</h2>
    @foreach($day['USER'] as $user)
    	<div  style="margin-top: 10px;"></div>
    	<b>{!! $user !!}</b><br>
    	@if ($day['OBSERVATION'][$loop->index] != '')
    		<i><small>{{ $day['OBSERVATION'][$loop->index] }}</small></i><br>
    	@endif
    @endforeach
@endforeach