@extends('app')

@section('content')
<div id="tournament-show"
     data-code="{{ $tournament->code }}"
     data-tournament="{{ json_encode($tournament) }}">
</div>
@endsection
