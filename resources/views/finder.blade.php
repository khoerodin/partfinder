<form action="search" method="get">
    <div class="input-group">
      <input type="text" name="q" class="form-control input-lg" placeholder="START TYPING A PART NUMBER" style="text-transform: uppercase;" value="{{$q or ''}}" id="finder">
      <span class="input-group-btn">
        <input id="find" type="submit" disabled class="btn btn-default btn-primary btn-lg" value="FIND">
      </span>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function() { 
    $("#finder").on("input",function ()  {
      	var empty = false;
        $('#finder').each(function() {
            if ($(this).val().length == 0) {
                empty = true;
            }
        });

        if (empty) {
            $('#find').attr('disabled', 'disabled');
        } else {
            $('#find').removeAttr('disabled');
        }
    });
});
</script>