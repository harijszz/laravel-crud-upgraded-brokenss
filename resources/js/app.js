import './bootstrap';

const tagForm = document.querySelector('.tag-form[data-search-url]');

if (tagForm) {
    const searchInput = tagForm.querySelector('#tag-search');
    const suggestionsList = tagForm.querySelector('#tag-suggestions');
    const selectedTags = tagForm.querySelector('#selected-tags');
    const searchUrl = tagForm.dataset.searchUrl;

    let suggestions = [];
    let visibleSuggestions = [];
    let activeIndex = 0;
    let requestId = 0;
    let debounceTimer;

    const selectedTitles = () => Array.from(selectedTags.querySelectorAll('input[name="tags[]"]'))
        .map((input) => input.value.toLowerCase());

    const addTag = (title) => {
        const trimmed = title.trim();

        if (!trimmed || selectedTitles().includes(trimmed.toLowerCase())) {
            return;
        }

        const item = document.createElement('li');
        item.className = 'tag-item';

        const label = document.createElement('span');
        label.textContent = trimmed;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tags[]';
        input.value = trimmed;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'tag-remove';
        remove.textContent = '\u00d7';
        remove.setAttribute('aria-label', `Remove ${trimmed}`);
        remove.addEventListener('click', () => item.remove());

        item.append(label, input, remove);
        selectedTags.append(item);
    };

    const clearSuggestions = () => {
        suggestions = [];
        visibleSuggestions = [];
        activeIndex = 0;
        suggestionsList.replaceChildren();
        suggestionsList.hidden = true;
    };

    const renderSuggestions = () => {
        const selected = selectedTitles();
        visibleSuggestions = suggestions.filter((tag) => !selected.includes(tag.title.toLowerCase()));
        activeIndex = Math.min(activeIndex, Math.max(visibleSuggestions.length - 1, 0));
        suggestionsList.replaceChildren();

        visibleSuggestions.forEach((tag, index) => {
            const item = document.createElement('li');
            item.textContent = tag.title;
            item.className = index === activeIndex ? 'tag-suggestion is-active' : 'tag-suggestion';
            item.addEventListener('mousedown', (event) => event.preventDefault());
            item.addEventListener('click', () => {
                addTag(tag.title);
                searchInput.value = '';
                clearSuggestions();
                searchInput.focus();
            });
            suggestionsList.append(item);
        });

        suggestionsList.hidden = visibleSuggestions.length === 0;
    };

    const search = async () => {
        const query = searchInput.value.trim();
        const currentRequest = ++requestId;

        try {
            const response = await window.axios.get(searchUrl, { params: { q: query } });

            if (currentRequest !== requestId || query !== searchInput.value.trim()) {
                return;
            }

            suggestions = response.data.tags ?? [];
            activeIndex = 0;
            renderSuggestions();
        } catch (error) {
            clearSuggestions();
        }
    };

    selectedTags.addEventListener('click', (event) => {
        if (event.target.closest('.tag-remove')) {
            event.target.closest('.tag-item').remove();
        }
    });

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        clearSuggestions();
        debounceTimer = setTimeout(search, 150);
    });

    searchInput.addEventListener('focus', () => {
        clearTimeout(debounceTimer);
        search();
    });

    searchInput.addEventListener('keydown', async (event) => {
        if (event.key === 'ArrowDown' && visibleSuggestions.length) {
            event.preventDefault();
            activeIndex = (activeIndex + 1) % visibleSuggestions.length;
            renderSuggestions();
        } else if (event.key === 'ArrowUp' && visibleSuggestions.length) {
            event.preventDefault();
            activeIndex = (activeIndex - 1 + visibleSuggestions.length) % visibleSuggestions.length;
            renderSuggestions();
        } else if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(debounceTimer);

            if (!visibleSuggestions.length) {
                await search();
            }

            addTag(visibleSuggestions.length ? visibleSuggestions[activeIndex].title : searchInput.value);
            searchInput.value = '';
            clearSuggestions();
            searchInput.focus();
        } else if (event.key === 'Escape') {
            clearSuggestions();
        }
    });

    document.addEventListener('click', (event) => {
        if (!tagForm.contains(event.target)) {
            clearSuggestions();
        }
    });
}
