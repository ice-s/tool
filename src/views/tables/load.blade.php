@extends('CRUD::layout')

@section('content')
    <div class="wrapper-editor mt-2">
        <form action="{{route('crud.table-view')}}" method="get">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group bmd-form-group is-filled">
                        <label for="exampleSelect1" class="bmd-label-floating required" >Table Name</label>
                        <select class="form-control" id="exampleSelect1" name="table" required>
                            @foreach($tables as $table)
                                <option value="{{$table}}" @if(request('table') == $table) selected @endif>{{$table}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-primary btn-block btn-primary"><strong>Load</strong>
                <i class="fas fa-paper-plane-o ml-1"></i>
            </button>
        </form>
    </div>
@endsection
