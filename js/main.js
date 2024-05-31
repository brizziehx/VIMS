const dropdown = document.querySelector('.dropdown');
const mainContent = document.querySelector('.main-content');

mainContent.addEventListener('click', (e) => {
    if(e.target.tagName === 'IMG') {
    // if(e.target.tagName === 'IMG' || e.target.tagName === 'LI' || e.target.tagName === 'A' || e.target.tagName === 'UL') {
        dropdown.style.display = 'block';
        dropdown.style.right = '0';
        dropdown.style.overflow = 'auto'
    } else {
        dropdown.style.display = 'none';
    }
});

$(document).ready(function() {
    // all search inputs
    $('#searchInput').on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable #trow").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    // notification
    $(".new-notification").click(function() {
        $(this).find('.selected-notification').slideToggle();
    })

    //report
    $('#length').change(function() {
        var selectedOption = $(this).val();
        if(selectedOption == 'Monthly_all') {
            $('#months_all').removeClass('hidden');
            $('div#months').css("margin-right", "22px");
            $('#start_date_all, #end_date_all').addClass('hidden');
        } else if(selectedOption == 'Annually_all') {
            $('#months').addClass('hidden');
            $('#start_date_all, #end_date_all').addClass('hidden');
        } else if(selectedOption == 'Pick_all') {
            $('#start_date_all, #end_date_all').removeClass('hidden');
            $('#months_all').addClass('hidden');
        }
    });

    $("#type_all").change(function() {
        var selectedOption = $(this).val();
        if(selectedOption == 'Trip') {
            $("#routes").removeClass('hidden');
        } else {
            $("#routes").addClass('hidden');
        }
    })

    $("#for_route").change(function() {
        var selectedOption = $(this).val();
        if(selectedOption == 'single') {
            $("#all_routes").removeClass('hidden');
        } else {
            $("#all_routes").addClass('hidden');
        }
    })
    

    $("#vehicle").select2({
        placeholder: "Select Vehicle",
        allowClear: true
    });

    
    $('#length_individual').change(function() {
        var selectedOption = $(this).val();
        if(selectedOption == 'Monthly') {
            $('#months_individual').removeClass('hidden');
            $('#start_date, #end_date').addClass('hidden');
        } else if(selectedOption == 'Annually') {
            $('#months_individual').addClass('hidden');
            $('#start_date, #end_date').addClass('hidden');
        } else if(selectedOption == 'Pick') {
            $('#start_date, #end_date').removeClass('hidden');
            $('#months_individual').addClass('hidden');
        }
    });
});
