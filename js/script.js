
const openMenuBtn = document.getElementById('open-menu-btn'); 

const menuLateral = document.getElementById('menu-lateral');

const closeMenuBtn = document.getElementById('close-menu-btn');

openMenuBtn.addEventListener('click', () => {
    menuLateral.classList.toggle('abierto');
});
if (closeMenuBtn) {
    closeMenuBtn.addEventListener('click', () => {
        menuLateral.classList.remove('abierto');
    });
}

