webpackJsonp([2], [
    function (t, n, o) {
        (function (t) {
            'use strict';
            function n(t, o) {
                t.text(o + ' s 后重新获取'),
                    setTimeout(function () {
                        o -= 1,
                            o >= 0 ? n(t, o)  : t.button('reset')
                    }, 1000)
            }
            t(function () {
                var o = t('.js-getReg');
                o.on('click', function (o) {
                    o.stopPropagation(),
                        o.preventDefault();
                    var e = t(this),
                        c = e.data('src');
                    var d = $('#regmobile').val();
                    e.button('loading'),
                        // console.log(c),
                        t.ajax({
                            url: c,
                            data:{mobile:d},
                            method: 'GET',
                            success: function (data) {
                                 console.log(data);
                                // alert(data.message);
                            }
                        })/*.then(function (t) {
                            console.log(t),
                                0 === t.error ? n(e, 60)  : e.button('reset')
                            if(t.error == 2){
                                alert(t.message);
                            }
                        })*/
                })
            })
        }).call(n, o(1))
    }
]);
