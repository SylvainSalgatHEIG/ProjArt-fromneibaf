@extends('template')

@section('header')
@endsection

@section('contenu')

@foreach($gradesArray as $moduleName => $moduleData)

    <h1>{{$moduleName}}</h1>

    @foreach($moduleData as $courseName => $courseData)

        @if($courseName != 'average')
            <h3>{{$courseName}}</h3>

            @foreach($courseData['grades'] as $grade => $gradeData)
                Note : {{$gradeData['grade']}}
                 - 
                Coefficient : {{$gradeData['coefficient']}}
                <br>
            @endforeach

            Weighting : {{$courseData['weighting']}}
            <br>
            Moyenne de branche : {{$courseData['average']}}

        <br>
    @endif

@endforeach

---<br>
Moyenne de module : {{$moduleData['average']}}


@endforeach



@endsection