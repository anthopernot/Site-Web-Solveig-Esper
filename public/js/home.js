var colonneSlide = document.querySelectorAll('#imgSlide');

window.addEventListener('load', function () {
    if(window.matchMedia("(max-width:450px)").matches) {
        for(let i = 0; i < colonneSlide.length ; i++ ){
            colonneSlide[i].style.height = '180px';
            colonneSlide[i].style.marginTop = '30%';
        }
        var row = document.createElement('div');
        var col = document.createElement('div');
        var title = document.createElement('div');

        row.className = 'row';
        col.className = 'col-sm-12 mx-auto mt-4 text-center font-weight-bold';
        title.className = 'title-custom';
        title.style.marginBottom = "100px";
        title.textContent = "I present my work to you.";

        document.querySelector('.home').appendChild(row);
        row.appendChild(col);
        col.appendChild(title);

    }else if(window.matchMedia("(min-width:450px)").matches && window.matchMedia("(max-width:850px)").matches) {
        for(let i = 0; i < colonneSlide.length ; i++ ){
            colonneSlide[i].style.height = '360px';
            colonneSlide[i].style.marginTop = '5%';
        }
    }
});