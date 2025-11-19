/**
 * YOcook Recipe Application - Complete JS Implementation
 * Includes: Authentication, Recipe CRUD, Search/Filter, and UI Management
 */

// ==================== MAIN app CLASS
class YOcookApp {
  constructor() {
    this.currentUser = null;
    this.recipes = []; // Will be loaded from storage/API
    this.init();
  }

  // ==================== INITIALIZATION ====================
  init() {
    this.loadCurrentUser();
    this.setupEventListeners();
    this.loadRecipes();
    this.updateUI();
  }

  // ==================== AUTHENTICATION ====================
  loadCurrentUser() {
    const userData = localStorage.getItem('currentUser');
    this.currentUser = userData ? JSON.parse(userData) : null;
  }

  login(email, password) {
    // In a real app, validate against server
    const user = {
      id: 1,
      name: email.split('@')[0],
      email: email
    };
    
    this.currentUser = user;
    localStorage.setItem('currentUser', JSON.stringify(user));
    this.updateUI();
    this.redirect('recipes.html');
  }

  logout() {
    this.currentUser = null;
    localStorage.removeItem('currentUser');
    this.updateUI();
    this.redirect('index.html');
  }

  isLoggedIn() {
    return this.currentUser !== null;
  }

  // ==================== RECIPE MANAGEMENT ====================
  loadRecipes() {
    // Sample data - replace with actual API call
    this.recipes = [
      {
        id: 1,
        title: "Avocado Toast",
        image: "img/avocado-toast.jpg",
        description: "Creamy avocado on crispy sourdough",
        categories: ["vegetarian", "quick"],
        prepTime: 5,
        cookTime: 5,
        difficulty: "easy",
        ingredients: ["1 avocado", "2 slices bread", "Salt to taste"],
        instructions: ["Toast bread", "Mash avocado", "Spread on toast"],
        owner: 1
      },
      // More sample recipes...
    ];
    
    // Load from localStorage if available
    const savedRecipes = localStorage.getItem('recipes');
    if (savedRecipes) {
      this.recipes = JSON.parse(savedRecipes);
    }
  }

  saveRecipes() {
    localStorage.setItem('recipes', JSON.stringify(this.recipes));
  }

  getRecipeById(id) {
    return this.recipes.find(recipe => recipe.id === parseInt(id));
  }

  addRecipe(recipeData) {
    const newRecipe = {
      ...recipeData,
      id: this.recipes.length > 0 ? Math.max(...this.recipes.map(r => r.id)) + 1 : 1,
      owner: this.currentUser.id
    };
    this.recipes.push(newRecipe);
    this.saveRecipes();
    return newRecipe;
  }

  updateRecipe(id, recipeData) {
    const index = this.recipes.findIndex(r => r.id === parseInt(id));
    if (index !== -1) {
      this.recipes[index] = { ...this.recipes[index], ...recipeData };
      this.saveRecipes();
      return true;
    }
    return false;
  }

  deleteRecipe(id) {
    const index = this.recipes.findIndex(r => r.id === parseInt(id));
    if (index !== -1) {
      this.recipes.splice(index, 1);
      this.saveRecipes();
      return true;
    }
    return false;
  }

  // ==================== UI MANAGEMENT ====================
  updateUI() {
    // Update auth-related UI
    document.querySelectorAll('[data-auth]').forEach(el => {
      const shouldShow = el.dataset.auth === 'authenticated' ? this.isLoggedIn() : !this.isLoggedIn();
      el.style.display = shouldShow ? '' : 'none';
    });

    // Update user info
    if (this.isLoggedIn()) {
      const userElements = document.querySelectorAll('[data-user]');
      userElements.forEach(el => {
        if (el.dataset.user === 'name') el.textContent = this.currentUser.name;
      });
    }

    // Update copyright year
    document.getElementById('year')?.textContent = new Date().getFullYear();
  }

  // ==================== PAGE-SPECIFIC FUNCTIONS ====================
  setupRecipesPage() {
    if (!document.getElementById('recipe-grid')) return;

    const renderRecipes = (filter = 'all', searchTerm = '') => {
      const grid = document.getElementById('recipe-grid');
      grid.innerHTML = '';

      const filtered = this.recipes.filter(recipe => {
        const matchesFilter = filter === 'all' || recipe.categories.includes(filter);
        const matchesSearch = searchTerm === '' || 
          recipe.title.toLowerCase().includes(searchTerm) || 
          recipe.description.toLowerCase().includes(searchTerm);
        return matchesFilter && matchesSearch;
      });

      filtered.forEach(recipe => {
        const card = document.createElement('article');
        card.className = 'recipe-card';
        card.dataset.categories = recipe.categories.join(' ');
        card.innerHTML = `
          <img src="${recipe.image}" alt="${recipe.title}">
          <div class="recipe-info">
            <div class="recipe-category">${recipe.categories.join(' • ')}</div>
            <h3>${recipe.title}</h3>
            <p>${recipe.description}</p>
            <div class="recipe-meta">
              <span>${recipe.prepTime + recipe.cookTime} mins</span>
              <span>${recipe.difficulty}</span>
            </div>
            <a href="recipe-detail.html?id=${recipe.id}" class="btn btn-recipe">View Recipe</a>
            ${this.isLoggedIn() && this.currentUser.id === recipe.owner ? 
              `<a href="edit-recipe.html?id=${recipe.id}" class="btn btn-edit">Edit</a>` : ''}
          </div>
        `;
        grid.appendChild(card);
      });
    };

    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderRecipes(btn.dataset.filter);
      });
    });

    // Search input
    document.getElementById('search-input')?.addEventListener('input', (e) => {
      const activeFilter = document.querySelector('.filter-btn.active')?.dataset.filter || 'all';
      renderRecipes(activeFilter, e.target.value.toLowerCase());
    });

    // Initial render
    renderRecipes();
  }

  setupRecipeDetailPage() {
    if (!document.getElementById('recipe-title')) return;

    const urlParams = new URLSearchParams(window.location.search);
    const recipeId = urlParams.get('id');
    const recipe = this.getRecipeById(recipeId);

    if (!recipe) {
      this.redirect('recipes.html');
      return;
    }

    // Populate recipe data
    document.getElementById('recipe-title').textContent = recipe.title;
    document.getElementById('recipe-image').src = recipe.image;
    document.getElementById('recipe-description').textContent = recipe.description;
    document.getElementById('recipe-meta').innerHTML = `
      <span>Prep: ${recipe.prepTime} mins</span>
      <span>Cook: ${recipe.cookTime} mins</span>
      <span>Difficulty: ${recipe.difficulty}</span>
    `;

    // Ingredients
    const ingredientsList = document.getElementById('ingredients-list');
    recipe.ingredients.forEach(ing => {
      const li = document.createElement('li');
      li.textContent = ing;
      ingredientsList.appendChild(li);
    });

    // Instructions
    const instructionsList = document.getElementById('instructions-list');
    recipe.instructions.forEach((inst, i) => {
      const li = document.createElement('li');
      li.innerHTML = `<strong>Step ${i+1}:</strong> ${inst}`;
      instructionsList.appendChild(li);
    });
  }

  setupRecipeFormPage() {
    const form = document.getElementById('recipe-form');
    if (!form) return;

    const isEdit = form.id === 'edit-recipe-form';
    const urlParams = new URLSearchParams(window.location.search);
    const recipeId = urlParams.get('id');
    let currentRecipe = null;

    if (isEdit) {
      currentRecipe = this.getRecipeById(recipeId);
      if (!currentRecipe) {
        this.redirect('recipes.html');
        return;
      }

      // Populate form with existing recipe
      Object.keys(currentRecipe).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
          if (input.type === 'checkbox') {
            input.checked = currentRecipe.categories.includes(input.value);
          } else {
            input.value = currentRecipe[key];
          }
        }
      });

      // Setup delete button
      document.getElementById('delete-recipe')?.addEventListener('click', () => {
        if (confirm('Delete this recipe permanently?')) {
          this.deleteRecipe(recipeId);
          this.redirect('recipes.html');
        }
      });
    }

    // Dynamic fields
    this.setupDynamicFormFields();

    // Form submission
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      const recipeData = {
        title: formData.get('title'),
        image: formData.get('image'),
        description: formData.get('description'),
        categories: Array.from(formData.getAll('categories')),
        prepTime: parseInt(formData.get('prep-time')),
        cookTime: parseInt(formData.get('cook-time')),
        difficulty: formData.get('difficulty'),
        ingredients: Array.from(formData.getAll('ingredients[]')),
        instructions: Array.from(formData.getAll('instructions[]'))
      };

      if (isEdit) {
        this.updateRecipe(recipeId, recipeData);
      } else {
        this.addRecipe(recipeData);
      }

      alert(`Recipe ${isEdit ? 'updated' : 'created'} successfully!`);
      this.redirect('recipes.html');
    });
  }

  setupDynamicFormFields() {
    // Add ingredient
    document.getElementById('add-ingredient')?.addEventListener('click', () => {
      const container = document.getElementById('ingredients-container');
      const div = document.createElement('div');
      div.className = 'ingredient-input';
      div.innerHTML = `
        <input type="text" name="ingredients[]" required>
        <button type="button" class="remove-btn">×</button>
      `;
      container.appendChild(div);
    });

    // Add instruction
    document.getElementById('add-instruction')?.addEventListener('click', () => {
      const container = document.getElementById('instructions-container');
      const div = document.createElement('div');
      div.className = 'instruction-input';
      div.innerHTML = `
        <textarea name="instructions[]" required></textarea>
        <button type="button" class="remove-btn">×</button>
      `;
      container.appendChild(div);
    });

    // Remove buttons
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('remove-btn')) {
        const parentDiv = e.target.closest('.ingredient-input, .instruction-input');
        if (parentDiv && parentDiv.parentElement.children.length > 1) {
          parentDiv.remove();
        }
      }
    });
  }

  // ==================== UTILITIES ====================
  redirect(path) {
    window.location.href = path;
  }

  setupEventListeners() {
    // Login form
    document.getElementById('login-form')?.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = e.target.elements.email.value;
      const password = e.target.elements.password.value;
      this.login(email, password);
    });

    // Logout button
    document.getElementById('logout-btn')?.addEventListener('click', () => this.logout());

   // Register form
document.getElementById('register-form')?.addEventListener('submit', (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  // Validate form data
  const email = formData.get('email');
  const password = formData.get('password');

  if (!email || !password) {
      alert('Email and password are required!');
      return;
  }

  // In a real app, validate and create user
  login(email, password);
});

function login(email, password) {
  // Implement login logic here
  console.log(`Logging in with email: ${email} and password: ${password}`);
}


    // Protect authenticated routes
    const protectedPages = ['add-recipe.html', 'edit-recipe.html'];
    if (protectedPages.some(page => window.location.pathname.endsWith(page))) {
      if (!this.isLoggedIn()) {
        alert('Please login first');
        this.redirect('login.html');
      }
    }
  }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  const app = new YOcookApp();
  
  // Page-specific setups
  app.setupRecipesPage();
  app.setupRecipeDetailPage();
  app.setupRecipeFormPage();
});
function editProfile() {
  document.querySelector('.profile-details').style.display = 'none';
  document.querySelector('.edit-profile-form').style.display = 'block';
}

// Fetch user data and populate profile details
document.addEventListener('DOMContentLoaded', function() {
  fetch('getUserProfile.php')
      .then(response => response.json())
      .then(data => {
          document.getElementById('username').textContent = data.username;
          document.getElementById('email').textContent = data.email;
          document.getElementById('username').value = data.username;
          document.getElementById('email').value = data.email;
      });
});
