(function ($) {
    'use strict';

    function debounce(fn, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function fetchSuggestions(query, $field) {
        console.log('Fetching suggestions for query:', query);

        $.ajax({
            url: gfOpenKvkAutocomplete.ajaxUrl,
            method: 'GET',
            data: {
                action: 'my_openkvk_autocomplete',
                nonce: gfOpenKvkAutocomplete.nonce,
                q: query
            },
            success: function (response) {
                console.log('Server response:', response);

                // Clear old suggestions
                $field.next('.gf-openkvk-suggestions').remove();

                if (response && response.success && response.data) {
                    const suggestions = response.data;
                    if (suggestions.length > 0) {
                        const $list = $('<ul class="gf-openkvk-suggestions"></ul>');
                        suggestions.forEach(function (item) {
                            const $li = $(
                                `<li>
                                    <strong>${item.handelsnaam}</strong><br>
                                    <small style="color: gray;">${item.plaats}</small>
                                </li>`
                            );
                            $li.on('click', function () {
                                $field.val(item.handelsnaam);
                                $list.remove();
                            });
                            $list.append($li);
                        });

                        $field.after($list);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error('Autocomplete error:', status, error);
            }
        });
    }

    $(document).on('input', '.gf-openkvk-autocomplete', debounce(function () {
        const $field = $(this);
        const query = $field.val();

        if (query.length >= 3) {
            fetchSuggestions(query, $field);
        } else {
            $field.next('.gf-openkvk-suggestions').remove();
        }
    }, 300));

    // Keyboard navigation for suggestions
    $(document).on('keydown', '.gf-openkvk-autocomplete', function (e) {
        const $list = $(this).next('.gf-openkvk-suggestions');
        const $items = $list.find('li');
        if (!$items.length) return;

        let index = $items.filter('.active').index();

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            index = (index + 1) % $items.length;
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            index = (index - 1 + $items.length) % $items.length;
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (index >= 0) {
                $items.eq(index).trigger('click');
            }
        }

        $items.removeClass('active');
        if (index >= 0) {
            $items.eq(index).addClass('active');
        }
    });
})(jQuery);
