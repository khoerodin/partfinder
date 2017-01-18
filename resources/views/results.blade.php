@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">    
        <div class="col-md-12">
            @include('finder')
            
            <div style="margin: 10px 0 10px 0;">
            @if(count($results)>0)
                Found {{number_format($results->total())}} 
                @if($results->total()>1)
                    part numbers contains
                @else
                    part number contain
                @endif
                "{{strtoupper($q)}}" keyword
                @if($results->total()>0 && ($q <> '' || $q <> null) )
                    · <a 
                            onclick="event.preventDefault();
                            document.getElementById('excel-form').submit();"
                            style="cursor: pointer;" 
                        >Download</a> as Excel file
                        <form id="excel-form" action="{{ url('/excel') }}" method="get">
                            <input type="hidden" name="q" value="{{$q or ''}}" id="excel_vars">
                        </form>
                @endif
            @endif            
            <div>

            @foreach ($results as $result)
            <div class="panel panel-default"  style="margin-top: 15px;">
                <div class="panel-heading"><strong>#{{ (($results->currentPage() - 1 ) * $results->perPage() ) + $loop->iteration }}</strong></div>
                <div class="panel-body">
                    <span class="text-primary" style="font-weight: bold; text-decoration: underline;margin-bottom: 15px;">DISCOVERED PART NUMBER</span>
                    <div class="row" style="margin-bottom: 25px;margin-top: 5px;">                        
                        <div class="col-md-2"><strong>PART NUMBER</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{strtoupper($result->part_number)}} </div>

                        <div class="col-md-2"><strong>MANCODE</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{strtoupper($result->man_code)}} </div>

                        <div class="col-md-2"><strong>MANUFACTURER</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{strtoupper($result->man_name)}} </div>
                    </div>
                    <hr>
                    <span class="text-primary" style="font-weight: bold; text-decoration: underline;margin-bottom: 15px;">SOURCE</span>
                    <div class="row" style="margin-top: 5px;margin-bottom: 20px;">
                        <div class="col-md-2"><strong>CATALOG NO</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{substr($result->created_at->format('Y'),2,2).str_pad($result->item_code,5,0,STR_PAD_LEFT)}} </div>
                        <div class="col-md-2"><strong>INC</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{strtoupper($result->inc)}} </div>
                        <div class="col-md-2"><strong>ITEM NAME</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{strtoupper($result->item_name)}} </div>

                        <div class="col-md-2"><strong>SHORT DESCRIPTION</strong></div>
                        <div class="col-md-10"><strong>:</strong> {{strtoupper($result->short_desc)}}</div>
                    </div>
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-2"><strong>PO TEXT<span class="pull-right" style="margin-right: -35px;">:</span></strong></div>
                        <div class="col-md-10" style="
                            margin-top: -23px;
                            font-family: monospace;
                            white-space: pre;
                            margin-left: 198px;">{{strtoupper($result->po_text)}}</div>
                    </div>
                    <div class="row">
                        
                        <div class="col-md-4"><strong>PART NUMBER</strong></div>
                        <div class="col-md-4"><strong>MANCODE</strong></div>
                        <div class="col-md-4"><strong>MANUFACTURER</strong></div>
                        @foreach ($result->partnumbers as $partnumber)
                        <div class="col-md-4">{{strtoupper($partnumber->part_number)}}</div>
                        <div class="col-md-4">{{strtoupper($partnumber->man_code)}}</div>
                        <div class="col-md-4">{{strtoupper($partnumber->man_name)}}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
            @if(count($results)>0)
                {{ $results->appends(request()->input())->links() }}
            @endif
        </div>
    </div>
</div>
@endsection