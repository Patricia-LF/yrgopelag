const slides = [{
                image: 'images/tropical-beach-paradise-stockcake.jpg',
                title: 'Paradise Found',
                subtitle: 'Escape to pristine beaches and palm trees'
            },
            {
                image: 'images/Overwater-Villa-with-Pool-Terrace-St.-Regis-Vommuli.jpg',
                title: 'Modern Serenity',
                subtitle: 'Infinity pool overlooking turquoise waters'
            },
            {
                image: 'images/Gemini_Generated_Image_s518nrs518nrs518(2).png',
                title: 'Zen Retreat',
                subtitle: 'Minimalist design with breathtaking ocean views'
            },
            {
                image: 'images/Gemini_Generated_Image_yn2h82yn2h82yn2h(1).png',
                title: 'Luxurious Comfort',
                subtitle: 'Spa-like bathrooms with ocean panoramas'
            },
            {
                image: 'images/mv_conrad_rangali_infinity-pool_web.jpg',
                title: 'Tropical Escape',
                subtitle: 'Unwind in paradise under swaying palms'
            },
            {
                image: 'images/photo-1507525428034-b723cf961d3e.jpeg',
                title: 'Evening Serenity',
                subtitle: 'Experience magical tropical sunsets'
            }
        ];

        class HeroCarousel {
            constructor(containerSelector, slidesData) {
                this.container = document.querySelector(containerSelector);
                this.slides = slidesData;
                this.currentSlide = 0;
                this.autoplayInterval = null;

                this.init();
            }

            init() {
                this.render();
                this.setupEventListeners();
                this.startAutoplay();
            }

            render() {
                // Skapa slides
                const slidesHTML = this.slides.map((slide, index) => `
                    <div class="slide ${index === 0 ? 'active' : ''}" data-index="${index}">
                        <img src="${slide.image}" alt="${slide.title}" class="slide-image">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <div class="content-wrapper">
                                <h1 class="slide-title">${slide.title}</h1>
                                <p class="slide-subtitle">${slide.subtitle}</p>
                                <a href="booking-page.php" class="cta-button">Book Your Stay</a>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Skapa navigation
                const navigationHTML = `
                    <button class="nav-button prev" aria-label="Previous slide">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button class="nav-button next" aria-label="Next slide">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                `;

                // Skapa dot indicators
                const dotsHTML = `
                    <div class="dot-indicators">
                        ${this.slides.map((_, index) => `
                            <button class="dot ${index === 0 ? 'active' : ''}" data-index="${index}" aria-label="Go to slide ${index + 1}"></button>
                        `).join('')}
                    </div>
                `;

                this.container.innerHTML = slidesHTML + navigationHTML + dotsHTML;
            }

            setupEventListeners() {
                // Navigation buttons
                this.container.querySelector('.nav-button.prev').addEventListener('click', () => this.prevSlide());
                this.container.querySelector('.nav-button.next').addEventListener('click', () => this.nextSlide());

                // Dot indicators
                this.container.querySelectorAll('.dot').forEach(dot => {
                    dot.addEventListener('click', (e) => {
                        const index = parseInt(e.target.dataset.index);
                        this.goToSlide(index);
                    });
                });
            }

            goToSlide(index) {
                // Ta bort active från nuvarande slide
                const currentSlideEl = this.container.querySelector(`.slide[data-index="${this.currentSlide}"]`);
                const currentDot = this.container.querySelector(`.dot[data-index="${this.currentSlide}"]`);

                currentSlideEl.classList.remove('active');
                currentDot.classList.remove('active');

                // Uppdatera index
                this.currentSlide = index;

                // Lägg till active på ny slide
                const newSlideEl = this.container.querySelector(`.slide[data-index="${this.currentSlide}"]`);
                const newDot = this.container.querySelector(`.dot[data-index="${this.currentSlide}"]`);

                newSlideEl.classList.add('active');
                newDot.classList.add('active');

                // Återställ autoplay
                this.resetAutoplay();
            }

            nextSlide() {
                const nextIndex = (this.currentSlide + 1) % this.slides.length;
                this.goToSlide(nextIndex);
            }

            prevSlide() {
                const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                this.goToSlide(prevIndex);
            }

            startAutoplay() {
                this.autoplayInterval = setInterval(() => {
                    this.nextSlide();
                }, 4000);
            }

            resetAutoplay() {
                clearInterval(this.autoplayInterval);
                this.startAutoplay();
            }
        }

        // Initiera carousel när sidan är laddad
        document.addEventListener('DOMContentLoaded', () => {
            new HeroCarousel('#carousel', slides);
        });