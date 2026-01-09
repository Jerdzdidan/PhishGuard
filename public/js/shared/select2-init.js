/**
 * Simple Select2 Initializer with Badge Styling
 * Clean and reusable for Laravel projects
 */
function prefetchAndInitSelect2(selector, url, placeholder) {
    return new Promise((resolve, reject) => {
        $.get(url)
            .done(function(data) {
                const preloadedData = (Array.isArray(data) ? data : data.data || []).map(item => ({
                    id: item.id,
                    text: item.name,
                    code: item.code
                }));

                initSelect2(selector, { placeholder, preloadedData });
                resolve(preloadedData); 
            })
            .fail(function(error) {
                console.error('Failed to load Select2 data:', error);
                reject(error); 
            });
    });
}

function initSelect2(selector, options = {}) {
    const {
        url = null,
        badgeKey = 'code',
        badgeClass = 'bg-primary',
        placeholder = 'Select an option',
        preloadedData = null 
    } = options;

    const $element = $(selector);
    if (!$element.length) return;

    const config = {
        allowClear: true,
        placeholder: placeholder,
        templateResult: formatWithBadge,
        templateSelection: formatWithBadge,
        language: {
            searching: function() { return null; }
        }
    };

    if (preloadedData) {
        $element.empty().append('<option></option>');
        // Use local data instead of AJAX
        config.data = preloadedData;
    } else if (url) {
        // Original AJAX config
        config.ajax = {
            url: url,
            dataType: 'json',
            delay: 250,
            processResults: (response) => ({
                results: (Array.isArray(response) ? response : response.data || []).map(item => ({
                    id: item.id,
                    text: item.name || item.text,
                    code: item[badgeKey] || ''
                }))
            })
        };
    }

    $element.select2(config);

    function formatWithBadge(option) {
        if (!option.id) return option.text;
        const code = option.code || $(option.element).data('code') || '';
        if (!code) return $('<span>' + option.text + '</span>');
        return $('<span>' + code + ' - ' + option.text + '</span>');
    }
}

// Reset Select2
function resetSelect2(selector) {
    $(selector).val(null).trigger('change');
}

// Set Select2 value
function setSelect2Value(selector, value) {
    if (value) $(selector).val(value).trigger('change');
}