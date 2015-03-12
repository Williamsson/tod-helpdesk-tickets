jQuery(document).ready(function(){
		console.log("yep");
		jQuery('#ticketsTable').DataTable({
	        "order": [[ 2, "desc" ]]
	    });
});