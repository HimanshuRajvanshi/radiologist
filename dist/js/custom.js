function updateStatus(typ,value,id) {
    var urlbase = FULL_PATH+"/status/update/"+typ+"/"+value+"/"+id;
     $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : urlbase,
        type: "GET",
        success: function(response){
            console.log(response);
            if(response.error ==0){
                toastr.success(response.success_message);
                setTimeout(function(){
                    console.log(typ)
                        if(typ =='offer'){
                            window.location = FULL_PATH+'/get/offer'; 
                        }else if(typ == 'blog') {
                            window.location = FULL_PATH+'/get/blog'; 
                        } else {
                            window.location = FULL_PATH+'/get/video'; 
                        }
                     }, 1800);
            }else{
                toastr.error('Somethink wrong please try again');
            }
        },
    });
    
}
