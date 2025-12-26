// Bilder för overlay-karusellen
const overlayImages = [
    {
        image: "images/unnamed (3).jpg",
        title: 'Spa',
    },
    {
        image: 'images/Gemini_Generated_Image_chpf31chpf31chpf(3).png',
        title: 'Spa',
    },
    {
        image: 'images/unnamed (4).jpg',
        title: 'Private beach',
    },
    {
        image: 'images/Beach22.jpg',
        title: 'Private beach',
    },
    {
        image: 'images/pool.jpg',
        title: 'Pool',
    },
    {
        image: 'images/unnamed (1).jpg',
        title: 'Breakfast buffet',
    },
    // Lägg till fler bilder här
];

class ImageOverlayCarousel {
    constructor(containerSelector, slidesData) {
        this.container = document.querySelector(containerSelector);
        this.slides = slidesData;
        this.currentSlide = 0;

        this.init();
    }

    init() {
        this.render();
        this.setupEventListeners();
    }

    render() {
        const slidesHTML = this.slides.map((slide, index) => `
            <div class="overlay-slide ${index === 0 ? 'active' : ''}" data-index="${index}">
                <img src="${slide.image}" alt="${slide.title}" class="overlay-slide-image">
                <div class="overlay-slide-content">
                    <div class="content-wrapper">
                        <h1 class="overlay-slide-title">${slide.title}</h1>
                    </div>
                </div>    
            </div>
        `).join('');

        const navigationHTML = `
            <button class="overlay-nav-button prev" aria-label="Previous image">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button class="overlay-nav-button next" aria-label="Next image">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        `;

        const dotsHTML = `
            <div class="overlay-dot-indicators">
                ${this.slides.map((_, index) => `
                    <button class="overlay-dot ${index === 0 ? 'active' : ''}" data-index="${index}" aria-label="Go to image ${index + 1}"></button>
                `).join('')}
            </div>
        `;

        this.container.innerHTML = slidesHTML + navigationHTML + dotsHTML;
    }

    setupEventListeners() {
        this.container.querySelector('.overlay-nav-button.prev').addEventListener('click', () => this.prevSlide());
        this.container.querySelector('.overlay-nav-button.next').addEventListener('click', () => this.nextSlide());

        this.container.querySelectorAll('.overlay-dot').forEach(dot => {
            dot.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                this.goToSlide(index);
            });
        });
    }

    goToSlide(index) {
        const currentSlideEl = this.container.querySelector(`.overlay-slide[data-index="${this.currentSlide}"]`);
        const currentDot = this.container.querySelector(`.overlay-dot[data-index="${this.currentSlide}"]`);

        currentSlideEl.classList.remove('active');
        currentDot.classList.remove('active');

        this.currentSlide = index;

        const newSlideEl = this.container.querySelector(`.overlay-slide[data-index="${this.currentSlide}"]`);
        const newDot = this.container.querySelector(`.overlay-dot[data-index="${this.currentSlide}"]`);

        newSlideEl.classList.add('active');
        newDot.classList.add('active');
    }

    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        this.goToSlide(nextIndex);
    }

    prevSlide() {
        const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.goToSlide(prevIndex);
    }
}

// Funktioner för att öppna/stänga overlay
let overlayCarousel = null;

function openImageOverlay() {
    const overlay = document.getElementById('image-overlay');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    if (!overlayCarousel) {
        overlayCarousel = new ImageOverlayCarousel('#image-carousel', overlayImages);
    }
}

function closeImageOverlay() {
    document.getElementById('image-overlay').classList.remove('active');
    document.body.style.overflow = 'auto';
}