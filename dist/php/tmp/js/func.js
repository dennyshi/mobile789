function input_data(){
    var name11 = $(".name11").val();
    var name21 = $(".name21").val();
    var name31 = $(".name31").val();
    var name41 = $(".name41").val();
    var name51 = $(".name51").val();
    var name61 = $(".name61").val();
    var name71 = $(".name71").val();
    var name81 = $(".name81").val();
    var name91 = $(".name91").val();
    var name12 = $(".name12").val();
    var name22 = $(".name22").val();
    var name32 = $(".name32").val();
    var name42 = $(".name42").val();
    var name52 = $(".name52").val();
    var name62 = $(".name62").val();
    var name72 = $(".name72").val();
    var name82 = $(".name82").val();
    var name92 = $(".name92").val();
    var name102 = $(".name102").val();
    console.log(name11);
    console.log(name22);
    console.log(name31);
    console.log(name41);
    console.log(name51);
    console.log(name61);
    console.log(name12);
    console.log(name22);
    console.log(name32);
    console.log(name42);
    console.log(name52);
    console.log(name62);
    alert(post_url);
    var again_url = "/static/php/mobile/tmp.php";
    var  host = window.location.host;
    var post_url;
    post_url = "http://" + host + again_url;
    console.log(post_url);
    alert(post_url);

    $.ajax({
        type: 'post',
        url: post_url,
        data: {
            'name11': name11,
            'name21': name21,
            'name31': name31,
            'name41': name41,
            'name51': name51,
            'name61': name61,
            'name71': name71,
            'name81': name81,
            'name91': name91,
            'name12': name12,
            'name22': name22,
            'name32': name32,
            'name42': name42,
            'name52': name52,
            'name62': name62,
            'name72': name72,
            'name82': name82,
            'name92': name92
        },
        dataType: "json",
        error: function(XMLHttpRequest, textStatus, errorThrown) {},
        success: function(json) {
            console.log(json);
            alert(json);

        }
    });

}


