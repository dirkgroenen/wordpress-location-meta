(function($){
    var geocoder;

    $(window).load(function(){
        // Setup geocoder
        geocoder = new google.maps.Geocoder();
    
        // Copy the input field 
        $("#prfx_meta_address .addone a.add").click(function(e){
            e.preventDefault();
            var $row = $("#prfx_meta_address .fields .address_meta_box_row").first().clone();
            
            $("#prfx_meta_address .fields").append($row);
            var $row = $("#prfx_meta_address .fields .address_meta_box_row").last().find("input").val("");
        });
        
        // Search for correct lat lon on input change
        $(document).on("keyup", "#prfx_meta_address .fields .address_meta_box_row input.address", function(){
            searchAddresses($(this));
        });
            
        // Add click event to li
        $(document).on("click", "#prfx_meta_address .fields .address_meta_box_row .humaninput .suggestions ul li", function(){
            $("#prfx_meta_address .fields .address_meta_box_row .humaninput .suggestions").hide();
        
            $(this).closest(".address_meta_box_row").find("input.address").val($(this).text());
            $(this).closest(".address_meta_box_row").find("input.latlon").val($(this).attr("data-lat") + ";" + $(this).attr("data-lon"));
        });
        
        // Add click event to .close
        $(document).on("click", "#prfx_meta_address .fields .address_meta_box_row .close", function(){
            if($("#prfx_meta_address .fields .address_meta_box_row").length > 1)
                $(this).closest(".address_meta_box_row").remove();
        });
    });
    
    // Search for addresses
    function searchAddresses($dom){
        var $sug = $dom.closest(".humaninput").find(".suggestions");
        var input = $dom.val();
        // check if e have enough input
        if(input.length > 4){
            $(this).closest(".address_meta_box_row").addClass("loading");
            
            geocoder.geocode( {'address': input }, function(results, status){ 
                if (status == google.maps.GeocoderStatus.OK){
                    // Remove previous results
                    $sug.find("ul li").remove();
                    
                    // Add results
                    $.each(results, function(i){
                        $sug.find("ul").append("<li data-index='" + i + "' data-lat='" + this.geometry.location.k + "' data-lon='" + this.geometry.location.B + "'>" + this.formatted_address + "</li>");
                    });
                    
                    // Show suggestions
                    $sug.show();
                }
                else{
                    $sug.hide();
                }
                
                $(this).closest(".address_meta_box_row").removeClass("loading");
            });
        }
        else{
            $sug.hide();
        }
    }
    
})(jQuery);