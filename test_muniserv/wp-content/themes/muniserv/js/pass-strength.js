(function(a){
  function b(){
    var e=a("#consultant_password").val(),d=a("#consultant_username").val(),c=a("#consultant_password2").val(),f;
    a("#pass-strength-result").removeClass("short bad good strong");
    if(!e){a("#pass-strength-result").html("Strength indicator");return}
    f=passwordStrength(e,d,c);
    switch(f){
      case 2:a("#pass-strength-result").addClass("bad").html("Weak");
      break;
      case 3:a("#pass-strength-result").addClass("good").html("Medium");
      break;
      case 4:a("#pass-strength-result").addClass("strong").html("Strong");
      break;
      case 5:a("#pass-strength-result").addClass("short").html("Mismatch");
      break;
      default:a("#pass-strength-result").addClass("short").html("Very weak")
    }
  }
  a(document).ready(function(){
    a("#consultant_password").val("").keyup(b);
    a("#consultant_password2").val("").keyup(b);
  })
})(jQuery);