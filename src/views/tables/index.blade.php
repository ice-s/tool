@extends('CRUD::layout')

@section('content')
    <div class="wrapper-editor mt-2">
        @include('CRUD::common.flash_message')
        <form action="{{route('crud.generate')}}" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Table Name</label>
                        <input type="text" class="form-control" name="table" value="{{ request('table')?? null }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Model Name</label>
                        <input type="text" class="form-control" name="model_name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Path Name</label>
                        <input type="text" class="form-control" name="model_path">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="hasAuth"> Auth
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            @csrf
            <table id="dtBasicExample" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="th-sm"><strong>Field Name</strong></th>
                        <th class="th-sm"><strong>Display Name</strong></th>
                        <th class="th-sm"><strong>Type</strong></th>
                        <th class="th-sm"><strong>Primary</strong></th>
                        <th class="th-sm"><strong>Fillable</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cols as $key => $col)
                    <tr>
                        <td>
                            <div class="form-group pt-0 mb-0">
                                <input type="text" class="form-control" name="cols[{{$key}}][name]" value="{{$key}}">
                            </div>
                        </td>
                        <td>
                            <div class="form-group pt-0 mb-0">
                                <input type="text" class="form-control" name="cols[{{$key}}][display]" value="{{ str_replace('_', ' ', ucfirst($key)) }}" required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group pt-0 mb-0">
                                <input type="text" class="form-control" name="cols[{{$key}}][type]"  value="{{$col['type']}}">
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="cols[{{$key}}][primary]" @if($key == 'id') checked @endif value="1">
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"  name="cols[{{$key}}][fillable]" value="1"  @if($key !== 'created_at' && $key !== 'updated_at' ) checked @endif >
                                </label>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-outline-primary btn-block btn-primary"><strong>Generate</strong>
                <i class="fas fa-paper-plane-o ml-1"></i>
            </button>
        </form>
    </div>
@endsection
