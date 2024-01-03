$(document).ready(function(){
    $(document).on('click', '.are_you_sure', function(e){
        var result = confirm('هل انت متأكد ؟ ')
        if(!result){
            return false;
        }
    })

});