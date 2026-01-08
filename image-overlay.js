// Bilder för overlay-karusellen
const overlayImages = [
    {
        image: 'images/rooms/luxury-room.png',
        title: 'Luxury suite',
    },
    {
        image: 'images/rooms/luxury8.png',
        title: 'Luxury suite',
    },
    {
        image: 'images/rooms/standard8.png',
        title: 'Standard room',
    },
    {
        image: 'images/rooms/standard5.png',
        title: 'Standard room',
    },
    {
        image: 'images/rooms/economy8.png',
        title: 'Economy room',
    },
    {
        image: 'images/rooms/economy9.jpg',
        title: 'Economy room',
    },
    {
        image: 'images/rooms/bathroom.jpg',
        title: 'Bathroom',
    },
    {
        image: 'images/rooms/bathroom3.png',
        title: 'Bathroom',
    },
    {
        image: 'images/features/infinity-pool.png',
        title: 'Pool',
    },
    {
        image: 'images/features/pool2.jpg',
        title: 'Pool',
    },
    {
        image: 'images/features/private-beach.jpg',
        title: 'Private beach',
    },
    {
        image: 'images/features/Beach2.jpg',
        title: 'Private beach',
    },
    {
        image: 'images/features/private-beach3.png',
        title: 'Private beach',
    },
    {
        image: 'images/restaurant5.png',
        title: 'Restaurant',
    },
    {
        image: 'images/restaurant.png',
        title: 'Restaurant',
    },
    {
        image: 'images/reataurang.png',
        title: 'Restaurant',
    },
    {
        image: 'images/beach-bar2.png',
        title: 'Beach bar',
    },
    {
        image: 'images/beach-bar.png',
        title: 'Beach bar',
    },
    {
        image: 'images/ocean.png',
        title: 'Beach bar',
    },
    {
        image: "images/features/spa2.png",
        title: 'Spa',
    },
    {
        image: "images/features/spa.jpg",
        title: 'Spa',
    },
    {
        image: 'images/features/spa-evening.png',
        title: 'Spa',
    },
    {
        image: 'images/features/bowling.png',
        title: 'Bowling alley',
    },
    {
        image: 'images/features/breakfast2.png',
        title: 'Breakfast buffet',
    },
    {
        image: 'images/features/breakfast.jpg',
        title: 'Breakfast buffet',
    },
    {
        image: 'images/gym.jpg',
        title: 'Gym',
    },
    {
        image: 'images/gym1.png',
        title: 'Gym',
    },
    
    /*
    //Games
    {
        image: 'images/features/yahtzee.jpg',
        title: 'Yahtzee',
    },
    {
        image: 'images/features/pingpong.jpg',
        title: 'Ping pong table',
    },
    {
        image: 'images/features/ps5.png',
        title: 'PS5',
    },*/
    {
        image: 'images/features/casino.jpg',
        title: 'Casino',
    },
    /*
    //Water
    {
        image: 'images/features/scuba-diving.jpg',
        title: 'Scuba diving',
    },
    {
        image: 'images/features/olympic-pool.jpg',
        title: 'Olympic pool',
    },
    {
        image: 'images/features/waterpark1.jpg',
        title: 'Waterpark with fire and minibar',
    },*/
    {
        image: 'images/features/waterpark.jpg',
        title: 'Waterpark with fire and minibar',
    },
    /*
    //wheels
    {
        image: 'images/features/unicycle.jpg',
        title: 'Unicycle',
    },
    {
        image: 'images/features/bicycle.jpg',
        title: 'Bicycle',
    },
    {
        image: 'images/features/trike.jpg',
        title: 'Trike',
    },*/
    {
        image: 'images/features/four-wheeled-beast.jpg',
        title: 'Four-wheeled motorized beast',
    },
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