@extends('template')

@section('header')
@endsection

@section('contenu')
<form action="{{route('grades.store')}}" method="POST">
    @csrf
    <label for="grade">Note :</label>
    <input type="number" name="grade" id="grade">
    <label for="coefficient">coefficient :</label>
    <input type="number" name="coefficient" id="coefficient">
    <select id="course" name="course">
        @foreach($gradesArray as $module)
            @foreach($module as $courseName => $course)
                @if($courseName != 'average')
                    <option value="{{$courseName}}">{{$courseName}}</option>
                @endif
            @endforeach
        @endforeach
    </select>
    <input type="submit" value="Ajouter">
</form>

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