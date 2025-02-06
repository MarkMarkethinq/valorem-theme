jQuery(document).ready(function ($) {
    // Initialiseer de Flowbite carousel
    const carousel = document.getElementById('testimonial-carousel');
    if (carousel) {
        const options = {
            defaultPosition: 0,
            interval: 3000,
            indicators: {
                activeClasses: 'bg-white dark:bg-gray-800',
                inactiveClasses: 'bg-white/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800',
                items: []
            }
        };
        
        const items = carousel.querySelectorAll('[data-carousel-item]');
        items.forEach((item, index) => {
            if (index === 0) {
                item.classList.remove('hidden');
            }
        });
    }
});

