@extends('template')

@section('header')
@endsection

@section('contenu')

    <div class="row mb-3">
    <form action="{{ url('deadlinesFiltered') }}" method="post">
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
