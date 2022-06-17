@extends('template')

@section('header')
@endsection

@section('contenu')

    <form action="{{ url('deadlines/add') }}" method="post">
        @csrf
        <label for="nameDeadline">Nom</label>
        <input type="text" name="nameDeadline">

        <label for="descriptionDeadline">Description</label>
        <input type="text" name="descriptionDeadline">

        <label for="examen">Examen</label>
        <input type="radio" name="typeDeadline" id="examen" value="Examen">
        <label for="examen">Rendu</label>
        <input type="radio" name="typeDeadline" id="rendu" value="Rendu">

        <label for="startDeadline">Start</label>
        <input type="date" name="startDeadline">

        <label for="endDeadline">End</label>
        <input type="date" name="endDeadline">

        <select name="coursesList" id="coursesList">
        @foreach ($coursesList as $course)
            <option value="{{$course->name}}">{{$course->name}}</option>
        @endforeach
        </select>

        <select name="groups" id="groups">
        @foreach ($userGroups as $userGroup)
            <option value="{{$userGroup->id}}">{{$userPromotion[0]->name . '-' . $userGroup->name}}</option>
        @endforeach 
        </select>

        <input type="submit" name="submit" value="Ajouter"/>
    </form>


    <div class="row mb-3">
    <form action="{{ url('deadlines') }}" method="post">
    @csrf
        <label for="groupName" class="col-md-1 col-form-label text-md-end">{{ __('Class') }}</label>
        <div class="col-md-6">
            <select name="groupName" id="group-name" onchange="this.form.submit()">
                @foreach ($userGroups as $userGroup)
                    @if ($groupSelected == $userGroup->name)
                        <option selected value="{{$userGroup->id}}" {{ old('groupName') == $userGroup->id ? "selected":""}}>{{$userPromotion[0]->name . '-' . $userGroup->name}}</option>
                    @else 
                        <option value="{{$userGroup->id}}" {{ old('groupName') == $userGroup->id ? "selected":""}}>{{$userPromotion[0]->name . '-' . $userGroup->name}}</option>
                    @endif
                @endforeach 
            </select>
        </div>
    </form>
    </div>
    <table>
        <th>Nom</th>
        <th>Cours</th>
        <th>Description</th>
        <th>Début</th>
        <th>Fin</th>
        @foreach ($deadlines as $deadline)
            <tr>
                <td>{{$deadline->name}}</td>
                <td>{{$deadline->course->name}}</td>
                <td>{{$deadline->description}}</td>
                <td>{{$deadline->start_date}}</td>
                <td>{{$deadline->end_date}}</td>
            </tr>
        @endforeach
        
    </table>
    


@endsection
