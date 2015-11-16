tracker = new function() {
    this.serverUrl = 'http://localhost:3000/user_actions';
    this.setCookie = function setCookie(name, value) {
        var expire = new Date();
        expire.setDate(expire.getDate() + 1);
        cookies = name + '=' + escape(value) + '; path=/ ';
        if(typeof cDay != 'undefined') cookies += ';expires=' + expire.toGMTString() + ';';
        document.cookie = cookies;
    };
    this.getCookie = function getCookie(name) {
        name = name + '=';
        var cookieData = document.cookie;
        var start = cookieData.indexOf(name);
        var cValue = '';
        if(start != -1){
            start += name.length;
            var end = cookieData.indexOf(';', start);
            if(end == -1)end = cookieData.length;
            cValue = cookieData.substring(start, end);
        }
        return unescape(cValue);
    };
    this.send = function(action, label) {
        if (tracker.getCookie('trackerId') == '') {
            var now = new Date();
            var id = now.getFullYear() + (now.getMonth() + 1) + now.getDate() + now.getHours() + now.getMinutes() + now.getSeconds() + Math.floor(Math.random() * 10000)
            tracker.setCookie('trackerId', id);
        }

        jQuery.ajax({
                url: tracker.serverUrl,
                type: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    "user_action": {
                        'tracking_id' : tracker.getCookie('trackerId'),
                        'name' : action,
                        'value' : label
                    }
                })
            })
            .done(function(data, textStatus, jqXHR) {
                console.log('HTTP Request Succeeded: ' + jqXHR.status);
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.log('HTTP Request Failed');
            });
    };
};
