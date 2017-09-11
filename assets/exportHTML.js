jQuery(document).ready(function ($) {

    function printData()
    {
        var divToPrint=document.getElementById("rsvp-table");
        newWin= window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();
    }

    $('button').on('click',function(){
        printData();
    })

});