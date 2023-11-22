@extends('layout.main')
@section('title')
    Billings - {{$workspace->title}}
@endsection

@section('content')
    <div class="container b-container">
        <h2 class="h2 my-4">Workspace - {{$workspace->title}}</h2>
        <h3 class="h3 center my-4">Bills</h3>
        <form method="get">
            @csrf
            <div class="d-flex align-items-center mb-4">
                <label for="selectId">Select</label>
                <select id="selectId" class="mx-2 form-select" name="month" >
                    @foreach($target_months as $month)
                        @if($month->month == $selected_month->month)
                            <option selected value="{{$month}}">{{$month->format('Y M')}}</option>
                        @else
                            <option value="{{$month}}">{{$month->format('Y M')}}</option>
                        @endif
                    @endforeach
                </select>
                <button class="btn btn-primary">Filter</button>
            </div>
        </form>
        <table class="table border shadow-sm">
            <tr>
                <th>
                    Token
                </th>
                <th>Time</th>
                <th>Per sec.</th>
                <th>Total</th>
            </tr>
            @foreach($target_tokens as $target)
                @for($i = 0; $i < $target['bills']->count(); $i++)
                    <tr>
                        <td>
                            @if($i == 0)
                                <h5 class="h5 fw-bold">{{$target['title']}}</h5>
                            @endif
                            <p class="p">#{{$target['bills'][$i]->id}} Service</p>
                        </td>
                        <td>{{$target['bills'][$i]->time}}sec.</td>
                        <td>{{$target['bills'][$i]->per_sec}}$</td>
                        <td>{{$target['bills'][$i]->total}}$</td>
                    </tr>
                @endfor
            @endforeach
        </table>
        <div class="d-flex justify-content-between">
            <h4 class="h4">Total:</h4>
            @if($quota)
                <p class="h5">{{$quota->limit}}$/{{$total_cost}}$</p>
            @else
                <p class="h5">{{$total_cost}}$</p>
            @endif
        </div>

        <div class="py-4"></div>
    </div>
@endsection
