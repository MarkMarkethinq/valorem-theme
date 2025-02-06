jQuery(document).ready(function ($) {
  // Toggle widget content
  const header = $("header");
  const nav = header.find('nav');
  const hero = $('#hero');
  const announcementBar = $('.mq-aanbieding');
  let headerHeight = header.outerHeight();
  let isFixed = false;

  // Alleen sticky header op homepage template
  if (document.body.classList.contains('page-template-geregeld-online-homepage')) {
    function updateHeader() {
      const scrollY = $(window).scrollTop();
      const announcementBarHeight = announcementBar.outerHeight() || 0;

      if (scrollY > announcementBarHeight && !isFixed) {
        header.addClass('fixed top-0 left-0 right-0 z-[9999]').removeClass('relative');
        announcementBar.hide();
        hero.css('padding-top', headerHeight + 'px');
        isFixed = true;
      } else if (scrollY <= announcementBarHeight && isFixed) {
        header.removeClass('fixed top-0 left-0 right-0 z-[9999]').addClass('relative');
        announcementBar.show();
        hero.css('padding-top', '');
        isFixed = false;
      }
    }

    $(window).on('scroll', updateHeader);

    // Update header height on window resize
    $(window).on('resize', function() {
      if (!isFixed) {
        headerHeight = header.outerHeight();
      }
      if (isFixed) {
        hero.css('padding-top', headerHeight + 'px');
      }
      updateHeader();
    });
  }

  // Rest van de bestaande widget code
  const widgetHeader = $(".widget-header");
  const content = $(".widget-content");

  widgetHeader.on("click", function () {
    content.toggle();
  });

  // Show and hide feedback and support forms
  const feedbackButton = $("#feedback-button");
  const supportButton = $("#support-button");
  const feedbackForm = $("#feedback-form");
  const supportForm = $("#support-form");

  feedbackButton.on("click", function () {
    feedbackForm.show(); // Show the feedback form
    supportForm.hide(); // Hide the support form
  });

  supportButton.on("click", function () {
    supportForm.show(); // Show the support form
    feedbackForm.hide(); // Hide the feedback form
  });
});

jQuery(document).ready(function ($) {
  console.log("Slick initialization starting");
  if ($(".carousel").length > 0) {
    $(".carousel").slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      speed: 0,
      autoplay: false,
      cssEase: "linear",
      autoplaySpeed: 0,
      infinite: true,
      arrows: true,
      responsive: [
        {
          breakpoint: 1023,
          settings: {
            slidesToShow: 3,
            dots: true,
          },
        },
        {
          breakpoint: 648,
          settings: {
            slidesToShow: 1,
            dots: true,
            centerMode: true,
            centerPadding: '60px',
          },
        },
        {
          breakpoint: 420,
          settings: {
            slidesToShow: 1,
            centerMode: true,
            centerPadding: '15px',
            dots: true,
          },
        },
        {
          breakpoint: 380,
          settings: {
            slidesToShow: 1,
            centerMode: true,
            centerPadding: '0px',
            dots: true,
          },
        },
      ],
    });
  }
});

jQuery(document).ready(function ($) {
    console.log("Slick initialization starting");
    if ($(".afbeelding-carousel").length > 0) {
      $(".afbeelding-carousel").slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        speed: 0,
        autoplay: false,
        cssEase: "linear",
        autoplaySpeed: 0,
        infinite: true,
        arrows: true,
        responsive: [
          {
            breakpoint: 1023,
            settings: {
              slidesToShow: 3,
              dots: false,
            },
          },
          {
            breakpoint: 648,
            settings: {
              slidesToShow: 1,
              dots: false,
              centerMode: true,
              centerPadding: '60px',
            },
          },
        ],
      });
    }
  });

jQuery(document).ready(function ($) {
    function showContentForStep() {
        // Get the current step from the hidden input
        var currentStep = $('#gform_source_page_number_1').val();

        console.log(currentStep);
    }

    // Run the function on form render (when the page changes)
    jQuery(document).on('gform_post_render', function () {
        showContentForStep();
    });

    // Ensure it runs on initial load as well
    showContentForStep();
});

//FAQ JS
if (document.body.classList.contains('home')) {
    const faqs = document.querySelectorAll(".faq");

    for (let i = 0; i < faqs.length; i++) {
        const answer = faqs[i].querySelector(".faq-answer");
        const icon = faqs[i].querySelector(".arrow-down");

        faqs[i].addEventListener("click", () => {
            for (let j = 0; j < faqs.length; j++) {
                const answer2 = faqs[j].querySelector(".faq-answer");
                const icon2 = faqs[j].querySelector(".arrow-down");

                if (faqs[i] != faqs[j]) {
                    answer2.style.maxHeight = "0px";
                    icon2.classList.replace("rotate-180", "rotate-0");
                }
            }

            if (icon.classList.contains("rotate-180")) {
                answer.style.maxHeight = 0 + "px";
                icon.classList.replace("rotate-180", "rotate-0");
            } else {
                answer.style.maxHeight = answer.scrollHeight + "px";
                icon.classList.replace("rotate-0", "rotate-180");
            }
        });
    }
}

//LIBER JS
jQuery(document).ready(function ($) {
    // Check URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    
    // Als liber parameter bestaat in URL, opslaan in localStorage voor 1 uur
    if (urlParams.has('liber')) {
        const expiryTime = new Date().getTime() + (60 * 60 * 1000); // 1 uur vanaf nu
        localStorage.setItem('liberExpiry', expiryTime);
        localStorage.setItem('liberParam', 'true');
        $('.mq-hidden-text').removeClass('hidden');
    }
    
    // Check of liberExpiry bestaat en niet verlopen is
    const liberExpiry = localStorage.getItem('liberExpiry');
    const now = new Date().getTime();
    
    if (liberExpiry && now > liberExpiry) {
        // Verwijder verlopen items
        localStorage.removeItem('liberExpiry');
        localStorage.removeItem('liberParam');
    }

    // Voeg liber parameter toe aan alle interne links als deze actief is
    if (localStorage.getItem('liberParam')) {
        $('a').each(function() {
            const href = $(this).attr('href');
            // Check of het een interne link is (begint met / of # of relatief pad)
            if (href && !href.startsWith('http') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
                const url = new URL(href, window.location.origin);
                url.searchParams.set('liber', '');
                $(this).attr('href', url.pathname + url.search + url.hash);
            }
        });
    }

    // Add hidden input to form with current URL including parameters
    // $('form').each(function() {
    //     var currentUrl = window.location.href;
    //     $(this).append('<input type="hidden" name="current_url" value="' + currentUrl + '">');
    // });
});

// Sticky header
// document.addEventListener('DOMContentLoaded', function () {
//   const nav = document.querySelector('nav');
//   const announcementBarHeight = document.querySelector('.mq-aanbieding').offsetHeight;

//   window.addEventListener('scroll', function () {
//       if (window.scrollY > announcementBarHeight) {
//           nav.classList.add('fixed', 'top-0');
//       } else {
//           nav.classList.remove('fixed', 'top-0');
//       }
//   });
// });

// Sluit mobiel menu bij klikken op anker links
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.getElementById('mobile-menu-2');
    const menuToggle = document.querySelector('[data-collapse-toggle="mobile-menu-2"]');
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024 && !mobileMenu.classList.contains('hidden')) {
                menuToggle.click();
            }
        });
    });
});

// GF done popup handler
jQuery(document).ready(function($) {
    const urlParams = new URLSearchParams(window.location.search);
    const popupEl = document.getElementById('info-popup');
    
    if (urlParams.get('popup') === 'done' && popupEl) {
        // Voeg een overlay toe voor betere styling
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-[9998]';
        document.body.appendChild(overlay);
        
        const donePopup = new Modal(popupEl, {
            placement: 'center',
            backdrop: 'dynamic',
            onShow: () => {
                document.body.style.overflow = 'hidden';
            },
            onHide: () => {
                document.body.style.overflow = '';
                overlay.remove();
            }
        });
        
        donePopup.show();
        
        const closeModalEl = document.getElementById('close-modal');
        if (closeModalEl) {
            closeModalEl.addEventListener('click', function() {
                donePopup.hide();
            });
        }

        // Sluit ook bij klikken op overlay
        overlay.addEventListener('click', function() {
            donePopup.hide();
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

// First-time popup handler
jQuery(document).ready(function($) {
    const urlParams = new URLSearchParams(window.location.search);
    const firstTimePopup = document.getElementById('first-time-popup');
    
    if (urlParams.get('welkom') !== null && firstTimePopup) {
        // Voeg een overlay toe
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-[9998]';
        document.body.appendChild(overlay);
        
        // Initialiseer Flowbite Modal
        const popup = new Modal(firstTimePopup, {
            placement: 'center',
            backdrop: 'dynamic',
            onShow: () => {
                document.body.style.overflow = 'hidden';
            },
            onHide: () => {
                document.body.style.overflow = '';
                overlay.remove();
            }
        });
        
        // Toon de popup
        popup.show();
        
        // Sluit popup functionaliteit
        const closeButton = document.getElementById('close-popup');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                popup.hide();
            });
        }

        // Sluit ook bij klikken op overlay
        overlay.addEventListener('click', function() {
            popup.hide();
        });
    }
});


jQuery(document).ready(function( $ ){	
	var formName = '.mq-aanmelden';
	var couponName = 'GRATIS';

	// Check URL parameter
	const urlParams = new URLSearchParams(window.location.search);
	if (urlParams.has('gratis')) {
		// Zoek het coupon invoerveld en de knop
		var couponInput = $(formName + ' .gf_coupon_code');
		var couponButton = $(formName + ' .ginput_container_coupon #gf_coupon_button');

		if (couponInput.length > 0 && couponButton.length > 0) {
			couponInput.val('gratis');
			couponButton.trigger('click');
		}
	}
});