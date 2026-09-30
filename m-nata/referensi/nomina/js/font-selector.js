var $= jQuery;
$(document).ready(function(){


    $.ajax({
        type: "GET",

        url: "https://www.googleapis.com/webfonts/v1/webfonts?key=apikey&sort=popularity&fields=items",
        dataType: "json",

        success: function (result, status, xhr){
            var outputstate = [];
          console.log(result.items);
          for (var i = 0; i<result.items.length; i++){
            var family = result.items[i].family;
            console.log(family);

           outputstate.push('<option value="'+ family +'">'+ family +'</option>');
           $('#_customize-input-font-select').html(outputstate.join(''));
          }
        },
        error: function (xhr, status, error) {
          alert("There was an error loading the Google fonts API: " + status + " " + error + " " + xhr.status + " " + xhr.statusText + "\n\nPlease save your changes and refresh the page to try again.")
        }


        });
    });