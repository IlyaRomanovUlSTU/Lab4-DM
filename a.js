$("#form").submit(function()
{
    $.post(
        "script.php",
        $("#form").serialize(),
        
        function(data)
        {
			$("#result").html(data);
        }
    );
   return false;
});