<script>
jQuery(document).ready(function()
	{
    var jqxhr = jQuery.post( "assignment_update.php", function() {
		// var jqxhr = jQuery.post( "<?php echo JUri::root(true) . '/assignment_update.php' ?>", function() {
			alert( "success" );
		})
			.done(function() {
				alert( "second success" );
			})
			.fail(function(xhr, status, error) {
				alert( "fail" );
				alert(`Error: ${error}`);
				alert(`Status: ${status}`)
			})
			.always(function() {
				alert( "finished" );
			});

		// Set another completion function for the request above
		jqxhr.always(function() {
			alert( "second finished" );
		});
	}
);
</script>
