var date = new Date();
var containerClass = document.querySelector('.container');
document.getElementById('date').textContent = ""+date.getFullYear();

window.addEventListener('load', function () {
    if(window.matchMedia("(max-width:400px)").matches) {
        containerClass.style.maxWidth = '380px';
    }
});