const wrapper = document.querySelector(".wrapper");
const fileName = document.querySelector(".file-name");
const defaultBtn = document.querySelector("#default-btn");
const customBtn = document.querySelector("#custom-btn");
const cancelBtn = document.querySelector("#cancel-btn i");
const img = document.getElementById("img-form");
let regExp = /[0-9a-zA-Z\^\&\'\@\{\}\[\]\,\$\=\!\-\#\(\)\.\%\+\~\_ ]+$/;
function defaultBtnActive(){
    defaultBtn.click();
}
defaultBtn.addEventListener("change", function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(){
            img.src = reader.result;
            wrapper.classList.add("active");
        };
        cancelBtn.addEventListener("click", function(){
            img.src = "";
            wrapper.classList.remove("active");
        });
        reader.readAsDataURL(file);
    }
});
/**
document.addEventListener('ready', function () {

    const cancelBtn = document.querySelector("#cancel-btn i");
    cancelBtn.addEventListener("click", function(){
        img.src = "default.jpg";
    });

});
 */

