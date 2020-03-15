@extends('CRUD::layout')

@section('content')
    <div class="wrapper-editor mt-2">
        <form action="{{route('crud.table-view')}}" method="get">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Table Name</label>
                        <input type="text" class="form-control" name="table" value="{{ request('table')?? null }}" required>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-primary btn-block btn-primary"><strong>Load</strong>
                <i class="fas fa-paper-plane-o ml-1"></i>
            </button>
        </form>
    </div>
@endsection
