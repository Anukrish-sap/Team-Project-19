function loadProducts(category = null) {
    
    let url = '../api/get_bakes.php';
    if(category) {
        url += `?category=` + category;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('bakes-container');
            container.innerHTML = '';
            if(data.length === 0) {
                container.innerHTML = '<p>No products found.</p>';
                return;
            }
            data.forEach((item, index)=> {
                const div = document.createElement('div');
                div.innerHTML = `
                    <img src="/public_/bake/images/${item.imageFileName}" class="product-image" alt="${item.bakeName}">
                    <h4>${item.bakeName}</h4>
                    <p>${item.description}</p>
                    <p class="price">£${parseFloat(item.price).toFixed(2)}</p>
                `;
                container.appendChild(div);
                setTimeout(() => {
                   div.classList.add('visible'); 
                }, index * 150);
            });
        });
}

loadProducts();