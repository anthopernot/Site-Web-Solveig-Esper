var container = document.getElementById('container');

window.addEventListener('load', function () {
    container.className = "animate__animated animate__zoomIn container about-custom";
    if(window.matchMedia("(max-width:400px)").matches) {
        container.style.width = '350px';
        document.getElementById('aboutTitle').className = 'row mt-4';
        document.getElementById('description').className = 'col-sm-4 mx-auto about-text-custom blocAbout2-custom mt-0';
    }
});
