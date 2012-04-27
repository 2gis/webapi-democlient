$(document).ready(function(){
    $('.search-by').hide();

    if ($('#sc').val() == 'where') {
        $('.bycoordinates').hide();
        $('.bytitle').show();
    } else {
        $('.bytitle').hide();
        $('.bycoordinates').show();
    }

    // Search methods switching
    $('.change-method').live('click', function(){
        var method = $(this).attr('data-method');
        $('.search-by').hide();
        $('.' + method).show();

        if ($('#sc').val() == 'where') {
            $('#sc').val('point');
            $('.bytitle').hide();
            $('.bycoordinates').show();
        } else {
            $('#sc').val('where');
            $('.bycoordinates').hide();
            $('.bytitle').show();
        }
    });

    // Search tabs switching
    $('.search-tab').live('click', function(){
        var type = $(this).attr('data-type');
        $('.search-tabs .active').removeClass('active');
        $(this).parent().addClass('active');
        if (type == 'firm-search') {
            $('.geoobjects').hide();
            $('.firm-search').show();
        } else {
            $('.firm-search').hide();
            $('.geoobjects').show();
        }
    });

    // Search placeholders manipulations
    $('.search .textfield').each(function(){
        if ( $(this).val() )
            $(this).prev('.placeholder').hide();
    });

    $('.search .placeholder').live('click', function(){
        $(this).hide().next('.textfield').focus();
    });

    $('.search .textfield').live('focus', function(){
        $(this).prev('.placeholder').hide();
    });

    $('.search .textfield').live('blur', function(){
        if ( !$(this).val() )
            $(this).prev('.placeholder').css({
                display: 'inline'
            });
    });

    $('#what_org').live('change', function(){
        $('#what_coord_org').val($(this).val());
        if ($(this).val()) {
            $('#what_coord_org').prev('.placeholder').hide();
        } else {
            $('#what_coord_org').prev('.placeholder').css({
                display: 'inline'
            });
        }
    });

    $('#what_coord_org').live('change', function(){
        $('#what_org').val($(this).val());
        if ($(this).val()) {
            $('#what_org').prev('.placeholder').hide();
        } else {
            $('#what_org').prev('.placeholder').css({
                display: 'inline'
            });
        }
    });

    $('#where_org').live('change', function(){
        var where = $(this).val();

        if (where != '') {
            where = Url.encode(where);
            var data = $.data(document.body, where);
            if (data != null && data.lon != null && data.lat != null) {
                $('#longitude_org').val(data.lon);
                $('#latitude_org').val(data.lat);
            } else {
                $.getJSON('/geoCoord?where=' + where, null, function (data) {
                    if (data != null && data.lon != null && data.lat != null) {
                        $('#longitude_org').val(data.lon);
                        $('#latitude_org').val(data.lat);
                        $.data(document.body, where, data);
                    } else {
                        $('#longitude_org').val('');
                        $('#latitude_org').val('');
                    }
                });
            }
        }
    });
});

function validate() {
    if ($('#sc').val() == 'point') {
        elms = ['what_coord_org','longitude_org','latitude_org','radius_org'];
        namesId = ['Что','Долгота', 'Широта', 'Радиус'];
        for( var i in elms ) {
            var v = $('#'+elms[i]).val().replace(/^\s+|\s+$/g, '');
            $('#'+elms[i]).val(v);
            var validate = true;

            if(elms[i] != 'radius_org' && !v) {
                alert("Полe '"+namesId[i]+"' не должно быть пустым");
                validate = false;
            }
            if(!(/^[0-9]+$/i.test(v)) && elms[i] == 'radius_org' && validate) {
                alert("Полe '"+namesId[i]+"' может содержать только цифры");
                validate = false;
            }
            if(!(/^\-{0,1}[0-9]+\.?[0-9]*$/i.test(v)) && (elms[i] == 'longitude_org' || elms[i] == 'latitude_org') && validate) {
                alert("Полe '"+namesId[i]+"' может содержать только цифры, знак минуса и точку - разделитель целой и дробной части");
                validate = false;
            }


            if( elms[i] == 'latitude_org' && validate) {
                if( v >= 90 || v <= -90 ){
                    alert("Значения поля '"+namesId[i]+"' не может быть больше 90 и меньше -90");
                    validate = false;
                }
            }

            if( elms[i] == 'longitude_org' && validate) {
                if( v >= 180 || v <= -180 ){
                    alert("Значения поля '"+namesId[i]+"' не может быть больше 180 и меньше -180");
                    validate = false;
                }
            }

            if(elms[i] == 'radius_org' && (v < 1 || v > 40000) && validate) {
                alert("Радиус не может быть меньше 1 и больше 40000 метров");
                validate = false;
            }
            if( !validate ){
                $('#'+elms[i]).focus();
                return false;
            }
        }
        return validate;
    } else {
        elms = ['what_org','where_org'];
        namesId = ['Что','Где'];
        for( var i in elms ) {
            var v = $('#'+elms[i]).val().replace(/^\s+|\s+$/g, '');
            $('#'+elms[i]).val(v);
            var validate = true;

            if(!v) {
                alert("Полe '"+namesId[i]+"' не должно быть пустым");
                validate = false;
            }

            if( !validate ){
                $('#'+elms[i]).focus();
                return false;
            }
        }
        return validate;
    }
}

function validateGeo() {
        var v = $('#what_geo').val().replace(/^\s+|\s+$/g, '');
        var validate = true;

        if(!v) {
            alert("Полe 'Что' не должно быть пустым");
            validate = false;
        }

        if( !validate ){
            $('#what_geo').focus();
            return false;
        }
        return validate;
}

/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/

var Url = {

	// public method for url encoding
	encode : function (string) {
		return escape(this._utf8_encode(string));
	},

	// public method for url decoding
	decode : function (string) {
		return this._utf8_decode(unescape(string));
	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}

