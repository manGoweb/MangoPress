window.addEventListener('load', function(e){
  var noscripts = document.querySelectorAll('noscript[defer]')
  var headEl = document.getElementsByTagName('head')[0]
  for(var k = 0; k < noscripts.length; k++) {
    headEl.insertAdjacentHTML( 'beforeend', noscripts[k].innerHTML)
  }
})
