window.onload = ()=>{
    let imgGaleria =document.querySelector("#main-product-img")
    let imgs=document.querySelectorAll(".thumb")
    let btn=document.querySelector(".active")
    let btns=document.querySelector("")
    for(let i=0;i<imgs.length;i++){
        imgs[i].addEventListener('click',(evt)=>{
            console.log(evt.target)
            imgGaleria.src=evt.target.src.replace("thumbs/","")
            
            imgs.forEach(item=>{
                item.classList.remove('active')
            })
            evt.target.classList.add('active')
        })
    }
}