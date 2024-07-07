let currentIndex = 0;

function changeSlide(direction) {
    const container = document.querySelector('.carousel-container');
    const slideWidth = document.querySelector('.carousel-item').clientWidth;
    
    currentIndex = (currentIndex + direction + totalSlides) % totalSlides;
    container.style.transform = `translateX(${-currentIndex * slideWidth}px)`;
}

// Obtém o total de slides uma vez para evitar cálculos repetidos
const totalSlides = document.querySelectorAll('.carousel-item').length;