$(document).ready(function() {
	$('#example')
    .removeClass( 'display' )
    .addClass('table table-striped table-bordered responsive-table');
    
    $('#example').DataTable({
              "lengthMenu": [ 15, 30, 45, 60, 75 ],
               
   });

    $('#example tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#example').DataTable();
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } ); 


});
