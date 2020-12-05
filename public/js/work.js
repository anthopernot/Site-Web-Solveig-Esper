const container = document.getElementById('container');
const wrapper = document.querySelector(".wrapper");
const fileName = document.querySelector(".file-name");
const text = document.querySelector(".text");
const defaultBtn = document.querySelector("#default-btn");
const cancelBtn = document.querySelector("#cancel-btn i");
const image = document.getElementById('image');
const content = document.getElementById('content');
const img = document.querySelector("img");
const video = document.querySelector("source");
const lecteur = document.querySelector("video");
const regExp = /[0-9a-zA-Z\^\&\'\@\{\}\[\]\,\$\=\!\-\#\(\)\.\%\+\~\_ ]+$/;
const imgColonne = document.querySelectorAll(".hover-shadow");
const mediaWork = document.querySelectorAll('#mediaWork');
const mediaWork2 = document.querySelectorAll('#mediaWork2');
let btnUpdate = document.getElementById('btnUpdate');
let btnDelete = document.getElementById('btnDelete');
var xhr = null;
var ajax = getXHR();
var method = "GET";
var url;
var asynchronous = true;
var data;

function defaultBtnActive(){
    defaultBtn.click();
}

function get_extension(filename) {
    return filename.slice((filename.lastIndexOf('.') - 1 >>> 0) + 2);
}

function getXHR() {
    if(window.XMLHttpRequest) { // Firefox et autres
        xhr = new XMLHttpRequest();
    }else if(window.ActiveXObject){ // Internet Explorer
        try {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    else { // XMLHttpRequest non supporté par le navigateur
        console.log("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
        xhr = false;
    }
    return xhr;
}

window.addEventListener('load', function () {

    container.className = "animate__animated animate__fadeIn container about-custom";
    if(window.matchMedia("(max-width:450px)").matches) {
        container.style.maxWidth = '200px';
        let colonne = document.querySelectorAll('#col-imgAlea');

        for(let i = 0; i < imgColonne.length; i++){
            imgColonne[i].className = 'hover-shadow mb-4 mt-4';
        }
        for(let j = 0; j < colonne.length; j++){
            colonne[j].style.marginLeft = '30%';
        }
    }else if(window.matchMedia("(min-width:450px)").matches && window.matchMedia("(max-width:850px)").matches) {
        for(i = 0; i < imgColonne.length; i++){
            imgColonne[i].style.marginLeft = ""+Math.floor(Math.random()*12) + 1+"px";
            imgColonne[i].style.marginTop = ""+Math.floor(Math.random()*5) + 1+"px";
            imgColonne[i].style.marginBottom = ""+Math.floor(Math.random()*8) + 1+"px";
        }
    }else if(window.matchMedia("(min-width:850px)").matches) {
        for(i = 0; i < imgColonne.length; i++){
            imgColonne[i].style.marginLeft = ""+Math.floor(Math.random()*21) + 1+"px";
            imgColonne[i].style.marginTop = ""+Math.floor(Math.random()*12) + 1+"px";
            imgColonne[i].style.marginBottom = ""+Math.floor(Math.random()*12) + 1+"px";
        }
    }

    url = getFileUrl;
    ajax.open(method,url,asynchronous);
    ajax.send();

    ajax.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            data = JSON.parse(this.responseText);

            for(let h = 0;h < data.length; h++ ){

                let id = JSON.stringify(data[h].id);
                let nom = JSON.stringify(data[h].nom);
                let description = JSON.stringify(data[h].description);
                let src = JSON.stringify(data[h].path);
                let divItem = document.createElement('div');
                let divCaption = document.createElement('div');
                let title = document.createElement('h6');
                let p = document.createElement('p');
                let pButton = document.createElement('p');
                let btnUpdate = document.createElement('button');
                let btnDelete = document.createElement('button');
                let iconEdit = document.createElement('i');
                let iconDelete = document.createElement('i');
                let img = document.createElement('img');
                let source = document.createElement('source');
                let videoApercu = document.createElement('video');

                nom = nom.substring(1,nom.length); nom = nom.substr(0,nom.length-1);
                description = description.substring(1,description.length); description = description.substr(0,description.length-1);
                src = src.substring(1,src.length); src = src.substr(0,src.length-1);

                document.getElementById("modalContent").className = 'animate__animated animate__fadeIn modal-content';

                if(h === 0){ divItem.className = "carousel-item active"; }else{ divItem.className = "carousel-item"; }

                divCaption.className = "carousel-caption d-none d-md-block";
                divItem.id = "bodyCarousel";
                title.textContent = nom;
                title.style.fontSize = '70%';
                p.textContent = description;

                btnUpdate.className = "btn btn-light border-secondary mr-2";
                btnUpdate.id = "btnUpdate";
                btnUpdate.setAttribute('data-dismiss', 'modal');
                btnUpdate.setAttribute('data-toggle', 'modal');
                btnUpdate.setAttribute('data-target', '#modalUpdate');
                btnUpdate.setAttribute('data-id', id);
                btnUpdate.setAttribute('data-nom', nom);
                btnUpdate.setAttribute('data-description', description);
                btnUpdate.setAttribute('data-src', src);

                btnDelete.className = "btn btn-dark border-secondary";
                btnDelete.id = "btnDelete";
                btnDelete.setAttribute('data-dismiss', 'modal');
                btnDelete.setAttribute('data-toggle', 'modal');
                btnDelete.setAttribute('data-target', '#modalDelete');
                btnDelete.setAttribute('data-id', id);
                btnDelete.setAttribute('data-nom', nom);

                iconEdit.className = "fas fa-edit";
                iconDelete.className = "fas fa-times";

                img.src = ""+baseUrl+'/uploads/'+src;
                img.className = "d-block w-100 mx-auto mb-2";
                img.alt = "";

                videoApercu.id = 'videoApercu';
                videoApercu.className = 'd-block w-100 mx-auto mb-2';
                videoApercu.controls = 'controls';
                videoApercu.style.height = '100%';
                videoApercu.style.width = '100%';

                source.id = 'videoApc';
                source.src = ""+baseUrl+'/uploads/'+src;
                source.type = "video/"+get_extension(src)+"";

                document.getElementById('bodyCrsl').appendChild(divItem);

                if(session){
                    btnDelete.appendChild(iconDelete);
                    btnUpdate.appendChild(iconEdit);
                    pButton.appendChild(btnUpdate);
                    pButton.appendChild(btnDelete);
                    divCaption.appendChild(pButton);
                }

                divCaption.appendChild(title);
                divCaption.appendChild(p);
                divItem.appendChild(divCaption);

                if(get_extension(src) === "mp4" || get_extension(src) === "avi" || get_extension(src) === "wave"
                    || get_extension(src) === "MP4" || get_extension(src) === "AVI" || get_extension(src) === "WAVE" ){

                    divItem.appendChild(videoApercu);
                    videoApercu.appendChild(source);

                }else if(get_extension(src) === "jpg" || get_extension(src) === "png" || get_extension(src) === "jpeg" || get_extension(src) === "gif"
                    || get_extension(src) === "JPG" || get_extension(src) === "PNG" || get_extension(src) === "JPEG" || get_extension(src) === "GIF"){

                    divItem.appendChild(img);

                }

            }

        }
    };

    /**
     * Tableaux pour fixer les indicateurs du carousel
     * entre chaque média des 2 colonnes d'affichage
     */
    let tab1 = new Array(mediaWork.length + mediaWork2.length);
    let tab2 = new Array(mediaWork.length - mediaWork.length);
    let tab3 = new Array(mediaWork2.length - mediaWork2.length );

    /**
     * boucle pour la colonne de gauche
     */
    for(let w = 0; w < tab1.length; w++){ tab2.push(w); w=w+1; }

    /**
     * boucle pour la colonne de droite
     */
    for(let w = 1; w < tab1.length; w++){ tab3.push(w); w=w+1; }

    /**
     * attribution des données pour les médias
     * de la colonne de gauche
     */
    for (let z = 0; z < mediaWork2.length; z++){
        mediaWork2[z].setAttribute('data-target' , '#carouselApercu');
        mediaWork2[z].setAttribute('data-slide-to' , tab3[z]);
    }

    /**
     * attribution des données pour les médias
     * de la colonne de droite
     */
    for (let z = 0; z < mediaWork.length; z++){
        mediaWork[z].setAttribute('data-target' , '#carouselApercu');
        mediaWork[z].setAttribute('data-slide-to' , tab2[z]);
    }

});


$('#modalUpdate').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget);
    let id = button.data('id');
    let nom = button.data('nom');
    let description = button.data('description');

    document.getElementById('titleCarouselUpdate').textContent = nom;
    document.getElementById('idUpdate').value = id;
    document.getElementById('nameUpdate').value = nom;
    document.getElementById('textareaUpdate').value = description;
});

$('#modalDelete').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget);
    let id = button.data('id');

    document.getElementById('titleCarouselDelete').textContent = button.data('nom');
    document.getElementById('idDelete').value = id;
});

defaultBtn.addEventListener("change", function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(){
            const result = reader.result;
            img.src = result;
            wrapper.classList.add("active");

            let name = fileName.textContent;
            if(get_extension(name) === "mp4" || get_extension(name) === "avi" || get_extension(name) === "wave"
                || get_extension(name) === "MP4" || get_extension(name) === "AVI" || get_extension(name) === "WAVE" ){
                lecteur.hidden = false;
                video.src = result;
                video.type ="video/"+get_extension(name)+"";
                image.hidden = true;
                content.hidden = true;
		        console.log("video supported !");
            }else if(get_extension(name) === "jpg" || get_extension(name) === "png" || get_extension(name) === "jpeg" || get_extension(name) === "gif"
                || get_extension(name) === "JPG" || get_extension(name) === "PNG" || get_extension(name) === "JPEG" || get_extension(name) === "GIF"){
                console.log("image supported !");
                lecteur.hidden = true;
                image.hidden = false;
                content.hidden = false;
		        img.src = result;
            }else{
	    	    console.error('file not supported');
	    	    text.textContent = 'file not supported';
            }

            wrapper.classList.add("active");

        };
        reader.readAsDataURL(file);
        cancelBtn.addEventListener("click", function(){
            img.src = "";
            video.src = "";
            lecteur.hidden = true;
            image.hidden = false;
            content.hidden = false;
            wrapper.classList.remove("active");
            text.textContent = 'No file chosen, yet!';
        });
    }
    if(this.value){
        let valueStore = this.value.match(regExp);
        fileName.textContent = valueStore;
    }
});


