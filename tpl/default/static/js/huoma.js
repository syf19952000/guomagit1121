
$('#passwordeye').click(function(){
    switchPwd();
});
function switchPwd() {
    var passwordeye = $('#passwordeye');
    var showPwd = $("#password");
    passwordeye.off('click').on('click',function(){
        if(passwordeye.hasClass('bukejian')){
            passwordeye.removeClass('bukejian').addClass('kejian');//密码可见
            showPwd.prop('type','text');
        }else{
            passwordeye.removeClass('kejian').addClass('bukejian');//密码不可见
            showPwd.prop('type','password');
        };
    });
}

// 上传图片并展示
document.getElementById('biaoqianma_file').onchange = function() {
    // alert(点击了);
    var imgFile = this.files[0];
    // alert(a);
    var fr = new FileReader();
    fr.onload = function() {
        // alert(fr.result);
        $('.huoma_photo_box').append('<img src="'+fr.result+'" alt="">');
        $('.huoma_photo_box').width($('.huoma_photo_box img').length * 90);
        // document.getElementById('image').getElementsByTagName('img')[0].src = fr.result;
    };
    fr.readAsDataURL(imgFile);
};



