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

    // Projecten carousel initialisatie
    if ($(".projecten-carousel").length > 0) {
        $(".projecten-carousel").slick({
            dots: true,
            arrows: true,
            infinite: true,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 1,
            adaptiveHeight: false,
            prevArrow: '<button type="button" class="slick-prev absolute top-1/2 -translate-y-1/2" style="left: -4rem;"><svg class="w-8 h-8" fill="none" stroke="black" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>',
            nextArrow: '<button type="button" class="slick-next absolute top-1/2 -translate-y-1/2" style="right: -4rem;"><svg class="w-8 h-8" fill="none" stroke="black" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>',
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        arrows: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        dots: true,
                        centerMode: true,
                        centerPadding: '40px'
                    }
                },
                {
                    breakpoint: 640,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        dots: true,
                        centerMode: false,
                        centerPadding: '0'
                    }
                }
            ]
        });

        $('.projecten-carousel').on('setPosition', function() {
            $(this).find('.slick-slide').height('auto');
            var slickTrack = $(this).find('.slick-track');
            var slickTrackHeight = $(slickTrack).height();
            $(this).find('.slick-slide').css('height', slickTrackHeight + 'px');
        });
    }
});
// Menu toggle functionaliteit
document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.querySelector('[data-collapse-toggle="mobile-menu-2"]');
    const mobileMenu = document.getElementById('mobile-menu-2');
    
    // Voeg click event toe
    menuButton.addEventListener('click', function() {
        // Wacht even tot Flowbite het menu heeft getoggeld
        setTimeout(() => {
            const isMenuOpen = !mobileMenu.classList.contains('hidden');
            
            // Selecteer beide SVG's
            const hamburgerIcon = menuButton.querySelector('svg:first-of-type');
            const closeIcon = menuButton.querySelector('svg:last-of-type');
            
            if (isMenuOpen) {
                // Menu is open, toon het kruisje
                hamburgerIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            } else {
                // Menu is dicht, toon het hamburger icoon
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            }
            
            // Update aria-expanded
            menuButton.setAttribute('aria-expanded', isMenuOpen);
        }, 10);
    });
});