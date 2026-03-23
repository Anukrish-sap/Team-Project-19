function updateHeadings(category, search, tag) {
    const heading = document.getElementById('headingBakes');
    const subheading = document.getElementById('subheadingBakes');
    if (!subheading || !heading) return;

    if (search) {
        subheading.textContent = `Search results for "${search}"`;
    } else if (tag === 'gluten-free') {
        subheading.textContent = `Gluten Free Bakes`;
    } else if (category) {
        subheading.textContent = `Our Products in "${category}"`;
    } else {
        subheading.textContent = 'Browse our freshly baked goods!';
    }

    if (search) {
        heading.textContent = `Search`;
    } else if (category) {
        heading.textContent = `Our Freshly Baked ${category}`;
    } else if (tag === 'gluten-free') {
        heading.textContent = `Our Gluten Free Bakes`;
    } else {
        heading.textContent = 'All Bakes';
    }
}

function loadProducts(category = null, search = '', tag = '') {
    const baseUrl = '/public_/bake/api/get_bakes.php';
    const params = new URLSearchParams();

    if (category) params.append('category', category);
    if (search)   params.append('search', search);
    if (tag)      params.append('tag', tag);

    updateHeadings(category, search, tag);

    const url = `${baseUrl}?${params.toString()}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("bakes-container");
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<p>No products found.</p>';
                return;
            }

            data.forEach((item, index) => {
                const price = parseFloat(item.price).toFixed(2);

                const div = document.createElement('div');
                div.dataset.price = parseFloat(item.price);

                div.innerHTML = `
                    <a class="card product-card product-link" href="/public_/bake/bake_details.php?bakeID=${item.bakeID}">
                        <img
                            src="/public_/bake/img/uploads/${item.imageFileName}"
                            class="product-image"
                            alt="${item.bakeName}"
                            style="height:140px;width:100%;object-fit:cover;border-radius:0.7rem;"
                        >
                        <h4>${item.bakeName}</h4>
                        <p>${item.description}</p>

                        ${parseInt(item.isGlutenFree) === 1
                            ? `<span class="badge gluten-free">Gluten Free</span>`
                            : ``
                        }

                        <p class="price">£${price}</p>

                        ${parseInt(item.stockAmount) > 0
                            ? `<div class="stock-line">In stock: <strong>${item.stockAmount}</strong></div>`
                            : `<div class="out-stock">Out of stock</div>`
                        }

                        <span class="view-desc">View details</span>
                    </a>
                `;

                container.appendChild(div);

                setTimeout(() => {
                    div.classList.add("show");
                }, index * 200);
            });

            applyPriceFilter();
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById("bakes-container").innerHTML = '<p>Could not load products.</p>';
        });
}

document.getElementById('searchBarForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const searchTerm = document.getElementById('searchInput').value;
    const params = new URLSearchParams(window.location.search);
    const category = params.get('category');
    const tag = params.get('tag') || '';
    loadProducts(category, searchTerm, tag);
});

document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const category = params.get('category');
    const search   = params.get('search') || '';
    const tag      = params.get('tag') || '';
    loadProducts(category, search, tag);
});

let priceMin = 0;
let priceMax = 100;

function togglePriceFilter() {
    const bar = document.getElementById('priceFilterBar');
    const btn = document.getElementById('filterToggleBtn');
    const isOpen = bar.style.display !== 'none';

    if (isOpen) {
        bar.style.animation = 'filterSlideUp 0.25s ease forwards';
        setTimeout(() => {
            bar.style.display = 'none';
            bar.style.animation = '';
        }, 220);
        btn.classList.remove('active');
    } else {
        bar.style.display = 'flex';
        bar.style.animation = 'filterSlideDown 0.25s ease forwards';
        btn.classList.add('active');
    }
}

function onPriceChange() {
    const minSlider = document.getElementById('priceMin');
    const maxSlider = document.getElementById('priceMax');

    if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
        minSlider.value = maxSlider.value;
    }
    if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
        maxSlider.value = minSlider.value;
    }

    priceMin = parseInt(minSlider.value);
    priceMax = parseInt(maxSlider.value);

    document.getElementById('priceMinDisplay').textContent = priceMin;
    document.getElementById('priceMaxDisplay').textContent = priceMax;

    const badge = document.getElementById('priceActiveBadge');
    if (badge) {
        if (priceMin > 0 || priceMax < 100) {
            badge.classList.add('visible');
        } else {
            badge.classList.remove('visible');
        }
    }

    // Show red dot on toggle button when filter is active
    const btn = document.getElementById('filterToggleBtn');
    if (btn) {
        if (priceMin > 0 || priceMax < 100) {
            btn.classList.add('filter-dirty');
        } else {
            btn.classList.remove('filter-dirty');
        }
    }

    applyPriceFilter();
}

function applyPriceFilter() {
    const cards = document.querySelectorAll('#bakes-container > div[data-price]');
    cards.forEach(card => {
        const price = parseFloat(card.dataset.price);
        card.style.display = (price >= priceMin && price <= priceMax) ? '' : 'none';
    });
}

function resetPriceFilter() {
    document.getElementById('priceMin').value = 0;
    document.getElementById('priceMax').value = 100;
    priceMin = 0;
    priceMax = 100;
    document.getElementById('priceMinDisplay').textContent = 0;
    document.getElementById('priceMaxDisplay').textContent = 100;

    const badge = document.getElementById('priceActiveBadge');
    if (badge) badge.classList.remove('visible');

    const btn = document.getElementById('filterToggleBtn');
    if (btn) btn.classList.remove('filter-dirty');

    applyPriceFilter();
}