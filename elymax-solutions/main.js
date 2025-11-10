// main.js - Elymax Network Solutions (Multi-page version)

// Global variables
let cart = [];

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeWebsite();
    setupEventListeners();
    loadCartFromStorage();
    updateCartUI();
});

// Initialize website functionality
function initializeWebsite() {
    // Initialize animations
    initializeAnimations();
    
    // Initialize product stock management if on products page
    if (window.location.pathname.includes('products.html')) {
        initializeProductStock();
    }
    
    console.log('Elymax Network Solutions initialized');
}

// Setup all event listeners
function setupEventListeners() {
    // Cart functionality
    setupCartFunctionality();
    
    // Form submissions
    setupFormHandlers();
    
    // Product interactions
    setupProductInteractions();
    
    // Mobile menu
    setupMobileMenu();
    
    // Smooth scrolling for anchor links
    setupSmoothScrolling();
}

// Mobile menu functionality
function setupMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

// Cart functionality
function setupCartFunctionality() {
    const cartButton = document.getElementById('cart-button');
    const cartSidebar = document.getElementById('cart-sidebar');
    const closeCart = document.getElementById('close-cart');
    
    if (cartButton && cartSidebar) {
        cartButton.addEventListener('click', function() {
            cartSidebar.classList.toggle('translate-x-full');
        });
    }
    
    if (closeCart && cartSidebar) {
        closeCart.addEventListener('click', function() {
            cartSidebar.classList.add('translate-x-full');
        });
    }
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productElement = this.closest('.bg-white');
            const productName = this.getAttribute('data-product') || 
                              productElement.querySelector('h3')?.textContent || 
                              'Unknown Product';
            const productPrice = this.getAttribute('data-price') || 
                               productElement.querySelector('.text-2xl')?.textContent || 
                               '$0.00';
            
            addToCart({
                id: generateProductId(productName),
                name: productName,
                price: productPrice,
                quantity: 1
            });
        });
    });
}

// Add item to cart
function addToCart(product) {
    // Check if product is already in cart
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push(product);
    }
    
    // Update UI and storage
    updateCartUI();
    saveCartToStorage();
    
    // Show confirmation
    showNotification(`${product.name} added to cart!`, 'success');
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartUI();
    saveCartToStorage();
    showNotification('Item removed from cart', 'info');
}

// Update cart quantity
function updateCartQuantity(productId, quantity) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        if (quantity <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = quantity;
            updateCartUI();
            saveCartToStorage();
        }
    }
}

// Update cart UI
function updateCartUI() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const cartCount = document.getElementById('cart-count');
    
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }
    
    if (cartItems && cartTotal) {
        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Your cart is empty</p>';
            cartTotal.textContent = '$0.00';
        } else {
            cartItems.innerHTML = '';
            let total = 0;
            
            cart.forEach(item => {
                const priceValue = parseFloat(item.price.replace('$', '').replace('/yr', ''));
                const itemTotal = priceValue * item.quantity;
                total += itemTotal;
                
                const cartItem = document.createElement('div');
                cartItem.className = 'flex justify-between items-center mb-4 pb-4 border-b';
                cartItem.innerHTML = `
                    <div class="flex items-center">
                        <div>
                            <h4 class="font-medium text-sm">${item.name}</h4>
                            <p class="text-gray-500 text-xs">${item.price}</p>
                            <div class="flex items-center mt-1">
                                <button class="quantity-decrease text-gray-500 hover:text-gray-700" data-id="${item.id}">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="mx-2 text-sm">${item.quantity}</span>
                                <button class="quantity-increase text-gray-500 hover:text-gray-700" data-id="${item.id}">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">$${itemTotal.toFixed(2)}</p>
                        <button class="text-red-500 hover:text-red-700 remove-item mt-1" data-id="${item.id}">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                `;
                cartItems.appendChild(cartItem);
            });
            
            // Add event listeners for quantity controls and remove buttons
            document.querySelectorAll('.quantity-decrease').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    const item = cart.find(item => item.id === productId);
                    if (item) {
                        updateCartQuantity(productId, item.quantity - 1);
                    }
                });
            });
            
            document.querySelectorAll('.quantity-increase').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    const item = cart.find(item => item.id === productId);
                    if (item) {
                        updateCartQuantity(productId, item.quantity + 1);
                    }
                });
            });
            
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    removeFromCart(productId);
                });
            });
            
            cartTotal.textContent = `$${total.toFixed(2)}`;
        }
    }
}

// Generate product ID
function generateProductId(productName) {
    return productName.toLowerCase().replace(/\s+/g, '-') + '-' + Date.now();
}

// Cart storage
function saveCartToStorage() {
    localStorage.setItem('elymax-cart', JSON.stringify(cart));
}

function loadCartFromStorage() {
    const savedCart = localStorage.getItem('elymax-cart');
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
        } catch (e) {
            console.error('Error loading cart from storage:', e);
            cart = [];
        }
    }
}

// Form handlers
function setupFormHandlers() {
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactForm);
    }
}

// Contact form handler
function handleContactForm(e) {
    e.preventDefault();
    
    const formData = {
        firstName: document.getElementById('first-name')?.value,
        lastName: document.getElementById('last-name')?.value,
        email: document.getElementById('email')?.value,
        company: document.getElementById('company')?.value,
        subject: document.getElementById('subject')?.value,
        message: document.getElementById('message')?.value
    };
    
    // Basic validation
    if (!formData.firstName || !formData.email || !formData.message) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    // Simulate form submission
    showNotification(`Thank you ${formData.firstName}! Your message has been sent. We'll respond within 24 hours.`, 'success');
    
    // Reset form
    e.target.reset();
}

// Product interactions
function setupProductInteractions() {
    // Product filter buttons
    const filterButtons = document.querySelectorAll('.bg-gray-200');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            // Add active class to clicked button
            this.classList.remove('bg-gray-200', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white');
            
            const filter = this.textContent.toLowerCase();
            filterProducts(filter);
        });
    });
}

// Filter products (simulated)
function filterProducts(filter) {
    const products = document.querySelectorAll('.bg-white.rounded-xl');
    
    products.forEach(product => {
        const productText = product.textContent.toLowerCase();
        
        if (filter === 'all products' || productText.includes(filter)) {
            product.style.display = 'block';
            setTimeout(() => {
                product.classList.add('fade-in');
            }, 10);
        } else {
            product.classList.remove('fade-in');
            setTimeout(() => {
                product.style.display = 'none';
            }, 300);
        }
    });
    
    showNotification(`Filtering by: ${filter}`, 'info');
}

// Product stock management
function initializeProductStock() {
    // In a real application, this would fetch stock data from an API
    const stockData = {
        'enterprise-wireless-access-points': 24,
        'network-switches': 15,
        'network-security-firewall': 3,
        'network-routers': 18,
        'network-management-software': 999, // Digital product
        'network-cabling-solutions': 0
    };
    
    // Update stock displays
    Object.keys(stockData).forEach(productId => {
        const stockElement = document.querySelector(`[data-product-id="${productId}"]`)?.closest('.bg-white')?.querySelector('.stock-indicator');
        if (stockElement) {
            const stock = stockData[productId];
            if (stock === 0) {
                stockElement.className = 'stock-out text-sm font-medium px-3 py-1 rounded-full';
                stockElement.textContent = 'Out of Stock';
                const button = stockElement.closest('.bg-white').querySelector('button');
                if (button) {
                    button.disabled = true;
                    button.className = 'w-full bg-gray-400 text-white py-2 rounded-lg font-medium cursor-not-allowed';
                    button.textContent = 'Out of Stock';
                }
            } else if (stock < 5) {
                stockElement.className = 'stock-low text-sm font-medium px-3 py-1 rounded-full';
                stockElement.textContent = 'Low Stock';
            } else {
                stockElement.className = 'stock-ok text-sm font-medium px-3 py-1 rounded-full';
                stockElement.textContent = 'In Stock';
            }
        }
    });
}

// Smooth scrolling for anchor links
function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Animations
function initializeAnimations() {
    // Fade in elements on scroll
    const fadeElements = document.querySelectorAll('.fade-in');
    
    const fadeInOnScroll = function() {
        fadeElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('fade-in');
            }
        });
    };
    
    // Initial check
    fadeInOnScroll();
    
    // Check on scroll
    window.addEventListener('scroll', fadeInOnScroll);
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.fixed-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full`;
    
    // Set styles based on type
    const styles = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-white'
    };
    
    notification.className += ` ${styles[type] || styles.info}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${getNotificationIcon(type)} mr-2"></i>
            <span>${message}</span>
            <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Get notification icon
function getNotificationIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle',
        warning: 'fa-exclamation-triangle'
    };
    return icons[type] || 'fa-info-circle';
}

console.log('Elymax Network Solutions main.js loaded');