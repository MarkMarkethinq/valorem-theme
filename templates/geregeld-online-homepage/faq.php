<section id="veelgestelde-vragen" class="bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6 ">
        <h2 class="mb-6 lg:mb-8 text-3xl lg:text-4xl tracking-tight font-extrabold text-center !text-white"><?php echo get_field('faq_titel'); ?></h2>
        <div class="mx-auto max-w-screen-md">
            <div id="accordion-flush" data-accordion="collapse" data-active-classes="bg-gray-900 text-white" data-inactive-classes="text-gray-400"> 
                <?php if( have_rows('faq_vragen') ): ?>
                    <?php 
                    $count = 1;
                    while( have_rows('faq_vragen') ): the_row(); 
                    ?>
                        <h2 id="accordion-flush-heading-<?php echo $count; ?>">
                            <button type="button" class="flex justify-between items-center py-5 w-full text-xl font-medium text-left text-white bg-gray-900 border-b border-gray-700" data-accordion-target="#accordion-flush-body-<?php echo $count; ?>" aria-expanded="true" aria-controls="accordion-flush-body-<?php echo $count; ?>">
                                <span><?php echo get_sub_field('faq_vraag'); ?></span>
                                <svg data-accordion-icon="" class="w-6 h-6 rotate-180 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                        </h2>
                        <div id="accordion-flush-body-<?php echo $count; ?>" class="" aria-labelledby="accordion-flush-heading-<?php echo $count; ?>">
                            <div class="py-5 border-b border-gray-700">
                                <div class="mb-2 text-base font-normal text-gray-400"><?php echo wpautop(get_sub_field('faq_antwoord')); ?></div>
                            </div>
                        </div>
                    <?php 
                    $count++;
                    endwhile; 
                    ?>      
                <?php endif; ?>               
            </div>
        </div>
    </div>
</section>