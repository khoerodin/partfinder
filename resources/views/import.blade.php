@extends('layouts.app')

@section('content')
<style type="text/css">
    #datatables.dataTable{ 
      table-layout:fixed; 
      width: 100%; /* IE6 need define width */
    }
    #datatables.dataTable thead th,
    #datatables.dataTable tfoot th,
    #datatables.dataTable tbody td {
      overflow:hidden;
      white-space:nowrap;
      -o-text-overflow: ellipsis;
      text-overflow: ellipsis;
    }
</style>
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var uploaded_file = '';
    $('#unggah').ajaxForm({
        beforeSend: function() {
            window.onbeforeunload = function() {return '';}
            $("#save-btn-area").empty();
            $("#display_uploaded_table").html("");
            $('#status').html('<span class="text-success">UPLOADING SPREADSHEET... <div class="mini-spinner"></div></span>');
            $('input#file_upload').attr('disabled', 'disabled');
        },
        success: function(xhr) {
            var dest = $('#select_table').val();
            $('#status').html('<span class="text-success">SPREADSHEET UPLOADED &#x2714;<br/>READING AND VALIDATING YOUR DATA... <div class="mini-spinner"></div></span>');
            $.ajax({
                type: 'GET',
                url: 'import/read/'+xhr.file,
                success: function(data){
                    $('#status').html('');      
                    $("#display_uploaded_table").html(data);
                    $(".import_to_db").appendTo("#save-btn-area");
                    $('input#file_upload').removeAttr('disabled');

                    $('#datatables').dataTable( {
                        dom: "<'row'<'col-sm-6'><'col-sm-6'f>>" +
                                "Z<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        oLanguage: {
                            sInfo: "_START_ TO _END_ OF _TOTAL_ ROWS",
                            oPaginate: {
                                sFirst: "FIRST",
                                sLast: "LAST",
                                sNext: "NEXT",
                                sPrevious: "PREVIOUS"
                            },
                            sSearch: "",
                            sSearchPlaceholder: "SEARCH...",
                        },
                    });

                    $("#message").appendTo("div#datatables_wrapper div.row div.col-sm-6:eq(0)");
                    $("#data_counter").appendTo("#counter");

                    uploaded_file = xhr.file;
                    window.onbeforeunload = function() {}
                },
                error: function(xhr){
                    var errors = xhr.responseJSON;
                    $('#status').html('<span class="text-danger">ERROR</span>');
                    $('input#file_upload').removeAttr('disabled');
                    window.onbeforeunload = function() {}
                }
            });
        },
        error: function(xhr) {
            var errors = xhr.responseJSON;
            $('#status').html('<span class="text-danger">ERROR</span>');
            $("#display_uploaded_table").html("");
            $('input#file_upload').removeAttr('disabled');
            window.onbeforeunload = function() {}
        }
    });

    $('#file_upload').change(function() {
      $('#unggah').submit();
    });

    $('#file_upload').click(function() {
        this.value = null;
    });

    // IMPORT to DATABASE
    var uploaded_file = uploaded_file;
    $(document).on('click', '.import_to_db', function() {

        if($(this).hasClass('import_part_number')){
            var url = 'import/import-part-number/'+uploaded_file;
            var data_name = 'PART NUMBER';
        }

        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function(){
                window.onbeforeunload = function() {return '';}
                $('input#file_upload').attr('disabled', 'disabled');
                $('.import_inc').attr('disabled', 'disabled');
                $("#message").html("IMPORTING YOUR <strong>"+data_name+"</strong> DATA... <div class='mini-spinner'></div>");
            },
            success: function(data){
                window.onbeforeunload = function() {}
                $('#status').empty();       
                $("span.group-span-filestyle.input-group-btn > label > span").text('SELECT SPREADSHEET AGAIN');
                $('input#file_upload').removeAttr('disabled');
                $("#save-btn-area").empty();
                $("#display_uploaded_table").empty();
                file_name = $("div.bootstrap-filestyle.input-group input").val();
                $("#status").html("<span class='text-success'>PART NUMBER DATA HAS BEEN IMPORTED SUCCESSFULLY<br/>UPLOADED FILE NAME: <b>"+file_name+"</b></span>");
                $("div.bootstrap-filestyle.input-group input").val('');
            },
            error: function(){
                window.onbeforeunload = function() {}
                $("span.group-span-filestyle.input-group-btn > label > span").text('SELECT SPREADSHEET AGAIN');
                $('input#file_upload').removeAttr('disabled');
                $('.import_inc').removeAttr('disabled');
                $("#message").html("<strong style='color:red;'>ERROR</strong> IMPORTING YOUR <strong>"+data_name+"</strong> DATA");
            }
        });        
    });
});
</script>
<div class="container">
    <div class="row">    
        <div class="col-md-12">
            <h4 class="header">IMPORT PART NUMBER DATA</h4>
            {!! Form::open(array('route' => 'upload','files'=>true, 'class'=>'form-horizontal','id'=>'unggah')) !!}
              <div class="form-group">
                <div class="col-sm-4">
                  <input type="file" class="filestyle" data-buttonBefore="true" name="document" data-icon="false" data-buttonText="SELECT FILE" data-buttonName="btn-primary" data-size="sm" id="file_upload">
                </div>
                <div class="col-sm-6" id="save-btn-area"></div>
              </div>
            {!! Form::close() !!}
       
            <span id="status" style="text-transform: uppercase;"></span>
            <span id="display_uploaded_table"></span>
        </div>
    </div>
</div>
@endsection