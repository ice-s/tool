@extends('CRUD::layout')

@section('content')
    <?php
//        dd()
    ?>
    <div class="wrapper-editor mt-2">
        @include('CRUD::common.flash_message')
        <form action="{{route('crud.generate')}}" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group bmd-form-group is-filled">
                        <label for="exampleSelect1" class="bmd-label-floating required" >Table Name</label>
                        <select class="form-control" id="tables" name="table" required>
                            @foreach($tables as $table)
                                <option value="{{$table}}" @if(request('table') == $table) selected @endif>{{$table}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Model Name</label>
                        <input type="text" class="form-control" name="{{request('table')}}[model_name]" required value="{{request('config')['model_name']?? null}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="bmd-label-floating">Path Name</label>
                        <input type="text" class="form-control" name="{{request('table')}}[model_path]" value="{{request('config')['model_path']?? null}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="{{request('table')}}[hasAuth]" @if(!empty(request('config')['hasAuth'])) checked @endif> Auth
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="{{request('table')}}[hasSoftDelete]" @if(!empty(request('config')['hasAuth'])) checked @endif> Soft Delete
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="{{request('table')}}[hasOverride]" @if(!empty(request('config')['hasAuth'])) checked @endif> Override file
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="{{request('table')}}[hasMockData]" @if(!empty(request('config')['hasAuth'])) checked @endif> Mock Data
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
                        <th class="th-sm"><strong>Filter</strong></th>
                        <th class="th-sm"><strong>Validation</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cols as $key => $col)
                    <tr>
                        <td>
                            <div class="form-group pt-0 mb-0">
                                <input type="text" class="form-control" name="{{request('table')}}[cols][{{$key}}][name]" value="{{$key}}" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="form-group pt-0 mb-0">
                                <input type="text" class="form-control" name="{{request('table')}}[cols][{{$key}}][display]"
                                       @if(!empty(request('config')['cols'][$key]['display']))
                                        value="{{request('config')['cols'][$key]['display']}}"
                                       @else
                                        value="{{ str_replace('_', ' ', ucfirst($key)) }}"
                                       @endif required>
                            </div>
                        </td>
                        <td>
                            <div class="form-group pt-0 mb-0">
                                <input type="text" class="form-control" name="{{request('table')}}[cols][{{$key}}][type]"  value="{{$col['type']}}" readonly>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="{{request('table')}}[cols][{{$key}}][primary]" value="1"
                                        @if(!empty(request('config')['cols'][$key]['primary']))
                                            checked
                                        @endif
                                        @if(empty(request('config')))
                                            @if($key == 'id') checked @endif
                                        @endif
                                    >
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <label>
                                    <input data-key="{{$key}}" class="fillable" type="checkbox"  name="{{request('table')}}[cols][{{$key}}][fillable]" value="1"
                                        @if(!empty(request('config')['cols'][$key]['fillable']))
                                            checked
                                        @endif
                                        @if(empty(request('config')))
                                            @if($key !== 'created_at' && $key !== 'updated_at' ) checked @endif
                                        @endif
                                    >
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"  name="{{request('table')}}[cols][{{$key}}][filter]" value="1"
                                        @if(!empty(request('config')['cols'][$key]['filter']))
                                           checked
                                        @endif
                                        @if(empty(request('config')))
                                           checked
                                        @endif
                                    >
                                </label>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <label>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#v{{$key}}"
                                    id="show-validate-{{$key}}"
                                    @if(empty(request('config')['cols'][$key]['fillable']))
                                        style="display:none"
                                    @endif>
                                        Open
                                    </button>
                                </label>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @foreach($cols as $key => $col)
                <div class="modal fade" id="v{{$key}}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Validate {{$key}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th class=""><strong>Required</strong></th>
                                        <th class=""><strong>Min</strong></th>
                                        <th class=""><strong>Max</strong></th>
                                        <th class=""><strong>Length</strong></th>
                                        <th class=""><strong>Type</strong></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="{{request('table')}}[cols][{{$key}}][validation][required]"
                                                        @if(!empty(request('config')['cols'][$key]['validation']['required']))
                                                            checked
                                                        @endif
                                                        >
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" name="{{request('table')}}[cols][{{$key}}][validation][min]" style="width: 50px"
                                                value="{{request('config')['cols'][$key]['validation']['min']?? ''}}">
                                            </td>
                                            <td>
                                                <input type="number" name="{{request('table')}}[cols][{{$key}}][validation][max]" style="width: 50px"
                                                value="{{request('config')['cols'][$key]['validation']['max']?? ''}}">
                                            </td>
                                            <td>
                                                <input type="number" name="{{request('table')}}[cols][{{$key}}][validation][len]" style="width: 50px"
                                                value="{{request('config')['cols'][$key]['validation']['len']?? ''}}">
                                            </td>
                                            <td>
                                                <select class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][type]">
                                                    <?php $checkType = request('config')['cols'][$key]['validation']['type']?? ''?>
                                                    <option value=""></option>
                                                    <option value="string" @if($checkType == 'string') selected @endif>string</option>
                                                    <option value="number" @if($checkType == 'number') selected @endif>number</option>
                                                    <option value="boolean" @if($checkType == 'boolean') selected @endif>boolean</option>
                                                    <option value="integer" @if($checkType == '"intege') selected @endif>integer</option>
                                                    <option value="float" @if($checkType == 'float') selected @endif>float</option>
                                                    <option value="array" @if($checkType == 'array') selected @endif>array</option>
                                                    <option value="date" @if($checkType == 'date') selected @endif>date</option>
                                                    <option value="url" @if($checkType == 'url') selected @endif>url</option>
                                                    <option value="email" @if($checkType == 'email') selected @endif>email</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <span><a target="_blank" href="https://www.antdv.com/components/form-model/">Antdv Form validate</a></span>
{{--                                <div class="row">--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <div class="checkbox">--}}
{{--                                                <label>--}}
{{--                                                    <input type="checkbox" name="{{request('table')}}[cols][{{$key}}][validation][hasValidate]"> Has Validation--}}
{{--                                                </label>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Require</label>--}}
{{--                                            <select class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][require]">--}}
{{--                                                <option value="false"></option>--}}
{{--                                                <option value="true">Yes</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Trigger</label>--}}
{{--                                            <select class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][trigger]">--}}
{{--                                                <option value=""></option>--}}
{{--                                                <option value="blur">blur</option>--}}
{{--                                                <option value="change">change</option>--}}
{{--                                                <option value="['change', 'blur']">change,blur</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Length</label>--}}
{{--                                            <input type="number" class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][len]">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Min</label>--}}
{{--                                            <input type="number" class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][min]">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Max</label>--}}
{{--                                            <input type="number" class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][max]">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Message</label>--}}
{{--                                            <input type="text" class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][messsage]">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <label class="bmd-label-floating">Type</label>--}}
{{--                                            <select class="form-control" name="{{request('table')}}[cols][{{$key}}][validation][type]">--}}
{{--                                                <option value=""></option>--}}
{{--                                                <option value="string">string</option>--}}
{{--                                                <option value="number">number</option>--}}
{{--                                                <option value="boolean">boolean</option>--}}
{{--                                                <option value="boolean">boolean</option>--}}
{{--                                                <option value="method">method</option>--}}
{{--                                                <option value="regexp">regexp</option>--}}
{{--                                                <option value="integer">integer</option>--}}
{{--                                                <option value="float">float</option>--}}
{{--                                                <option value="array">array</option>--}}
{{--                                                <option value="object">object</option>--}}
{{--                                                <option value="enum">enum</option>--}}
{{--                                                <option value="date">date</option>--}}
{{--                                                <option value="url">url</option>--}}
{{--                                                <option value="hex">hex</option>--}}
{{--                                                <option value="email">email</option>--}}
{{--                                                <option value="any">any</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                </div>--}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <button name="action[save]" class="btn btn-outline-primary btn-block btn-primary"><strong>Save</strong>
                <i class="fas fa-paper-plane-o ml-1"></i>
            </button>
            <button name="action[generate]" class="btn btn-outline-primary btn-block btn-primary"><strong>Generate</strong>
                <i class="fas fa-paper-plane-o ml-1"></i>
            </button>
        </form>
    </div>
@endsection
@section('pagescript')
    <script>
        $(document).ready(function () {
            $("#tables").on('change', function(e){
                e.preventDefault();
                var value = $(this).val();
                window.location = '/crud?table='+value;
            });

            $(".fillable").on('change', function(e){
                var key = $(this).data('key');
                if($(this).is(":checked")) {
                    $("#show-validate-" + key).show();
                }else{
                    $("#show-validate-" + key).hide();
                }
            })
        })
    </script>
@stop
