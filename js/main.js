/**
 * MemeNews - Main JavaScript File
 * 
 * This file contains all client-side functionality for the MemeNews application,
 * including theme toggling, animations, mobile navigation, and meme generation.
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initThemeToggle();
    initMobileNavigation();
    initMemeGenerator();
    initAnimations();
});

/**
 * Initializes theme toggle functionality
 */
function initThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle-btn');
    const htmlElement = document.documentElement;
    
    if (!themeToggle) return;
    
    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        htmlElement.classList.toggle('dark-theme', savedTheme === 'dark');
    } else {
        // Check for system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        htmlElement.classList.toggle('dark-theme', prefersDark);
    }
    
    // Toggle theme on button click
    themeToggle.addEventListener('click', function() {
        const isDark = htmlElement.classList.toggle('dark-theme');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
}

/**
 * Initializes mobile navigation menu
 */
function initMobileNavigation() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (!mobileMenuToggle || !mobileMenu) return;
    
    mobileMenuToggle.addEventListener('click', function() {
        const expanded = this.getAttribute('aria-expanded') === 'true' || false;
        this.setAttribute('aria-expanded', !expanded);
        mobileMenu.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mobileMenu.classList.contains('active') && 
            !mobileMenu.contains(event.target) && 
            !mobileMenuToggle.contains(event.target)) {
            mobileMenu.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('menu-open');
        }
    });
}

/**
 * Initializes meme generator functionality
 */
function initMemeGenerator() {
    const memeForm = document.getElementById('meme-form');
    const newsSelect = document.getElementById('news-select');
    const customHeadline = document.getElementById('custom-headline');
    const templateSelect = document.getElementById('template-select');
    const customImage = document.getElementById('custom-image');
    const topText = document.getElementById('top-text');
    const bottomText = document.getElementById('bottom-text');
    const fontSelect = document.getElementById('font-select');
    const textColor = document.getElementById('text-color');
    const outlineColor = document.getElementById('outline-color');
    const textSize = document.getElementById('text-size');
    const textSizeValue = document.getElementById('text-size-value');
    const imageFilter = document.getElementById('image-filter');
    const filterIntensity = document.getElementById('filter-intensity');
    const filterIntensityValue = document.getElementById('filter-intensity-value');
    const btnTextBold = document.getElementById('btn-text-bold');
    const btnTextItalic = document.getElementById('btn-text-italic');
    const btnTextUppercase = document.getElementById('btn-text-uppercase');
    const btnTextShadow = document.getElementById('btn-text-shadow');
    const generateCaptionBtn = document.getElementById('generate-caption-btn');
    const memePreview = document.getElementById('meme-preview');
    const previewTopText = document.getElementById('preview-top-text');
    const previewBottomText = document.getElementById('preview-bottom-text');
    const templateImage = document.getElementById('template-image');
    const downloadBtn = document.getElementById('download-meme-btn');
    const shareBtn = document.getElementById('share-meme-btn');
    
    // Exit if we're not on the meme generator page
    if (!memeForm) return;
    
    // Text style states
    let isBold = false;
    let isItalic = false;
    let isUppercase = true; // Default is uppercase for memes
    let hasShadow = true; // Default has shadow
    
    // Update preview text when user inputs change
    function updatePreviewText() {
        if (previewTopText) previewTopText.textContent = isUppercase ? topText.value.toUpperCase() : topText.value;
        if (previewBottomText) previewBottomText.textContent = isUppercase ? bottomText.value.toUpperCase() : bottomText.value;
        
        // Apply font, color, and styles
        const textElements = memePreview.querySelectorAll('.meme-text');
        textElements.forEach(el => {
            // Font family
            el.style.fontFamily = fontSelect.value;
            
            // Text color
            el.style.color = textColor.value;
            
            // Text size
            el.style.fontSize = textSize.value + 'px';
            
            // Text outline
            el.style.webkitTextStroke = `2px ${outlineColor.value}`;
            el.style.textStroke = `2px ${outlineColor.value}`;
            
            // Bold
            el.style.fontWeight = isBold ? '900' : 'bold';
            
            // Italic
            el.style.fontStyle = isItalic ? 'italic' : 'normal';
            
            // Shadow
            if (hasShadow) {
                el.style.textShadow = `3px 3px 6px rgba(0, 0, 0, 0.8), -1px -1px 3px rgba(0, 0, 0, 0.8)`;
            } else {
                el.style.textShadow = 'none';
            }
        });
    }
    
    // Update image filter
    function updateImageFilter() {
        if (!templateImage) return;
        
        // Remove all filter classes first
        templateImage.className = '';
        
        const filter = imageFilter.value;
        const intensity = filterIntensity.value;
        
        if (filter === 'none') {
            templateImage.classList.add('preview-filter-none');
            return;
        }
        
        // Add filter class
        templateImage.classList.add(`preview-filter-${filter}`);
        
        // Calculate intensity class
        let intensityClass;
        if (intensity <= 25) {
            intensityClass = 'intensity-25';
        } else if (intensity <= 50) {
            intensityClass = 'intensity-50';
        } else if (intensity <= 75) {
            intensityClass = 'intensity-75';
        } else {
            intensityClass = 'intensity-100';
        }
        
        templateImage.classList.add(intensityClass);
    }
    
    // Set up event listeners for form inputs
    if (topText) topText.addEventListener('input', updatePreviewText);
    if (bottomText) bottomText.addEventListener('input', updatePreviewText);
    if (fontSelect) fontSelect.addEventListener('change', updatePreviewText);
    if (textColor) textColor.addEventListener('input', updatePreviewText);
    
    // New event listeners
    if (outlineColor) outlineColor.addEventListener('input', updatePreviewText);
    
    if (textSize) {
        textSize.addEventListener('input', function() {
            if (textSizeValue) textSizeValue.textContent = this.value + 'px';
            updatePreviewText();
        });
    }
    
    if (imageFilter) {
        imageFilter.addEventListener('change', function() {
            // Enable/disable intensity slider based on filter selection
            if (this.value === 'none') {
                filterIntensity.disabled = true;
            } else {
                filterIntensity.disabled = false;
            }
            updateImageFilter();
        });
    }
    
    if (filterIntensity) {
        filterIntensity.addEventListener('input', function() {
            if (filterIntensityValue) filterIntensityValue.textContent = this.value + '%';
            updateImageFilter();
        });
    }
    
    // Text style buttons
    if (btnTextBold) {
        btnTextBold.addEventListener('click', function() {
            isBold = !isBold;
            this.classList.toggle('active', isBold);
            updatePreviewText();
        });
    }
    
    if (btnTextItalic) {
        btnTextItalic.addEventListener('click', function() {
            isItalic = !isItalic;
            this.classList.toggle('active', isItalic);
            updatePreviewText();
        });
    }
    
    if (btnTextUppercase) {
        btnTextUppercase.addEventListener('click', function() {
            isUppercase = !isUppercase;
            this.classList.toggle('active', isUppercase);
            updatePreviewText();
        });
        // Set initial state (uppercase is default for memes)
        btnTextUppercase.classList.add('active');
    }
    
    if (btnTextShadow) {
        btnTextShadow.addEventListener('click', function() {
            hasShadow = !hasShadow;
            this.classList.toggle('active', hasShadow);
            updatePreviewText();
        });
        // Set initial state (shadow is default for memes)
        btnTextShadow.classList.add('active');
    }
    
    // Custom headline / news selection logic
    if (newsSelect && customHeadline) {
        newsSelect.addEventListener('change', function() {
            if (this.value) {
                customHeadline.value = '';
            }
        });
        
        customHeadline.addEventListener('input', function() {
            if (this.value) {
                newsSelect.value = '';
            }
        });
    }
    
    // Custom image / template selection logic
    if (templateSelect && customImage) {
        templateSelect.addEventListener('change', function() {
            customImage.value = '';
        });
        
        customImage.addEventListener('change', function() {
            if (this.files.length > 0) {
                templateSelect.value = '';
                
                // Preview the custom image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = memePreview.querySelector('img');
                    if (imgElement) {
                        imgElement.src = e.target.result;
                        imgElement.alt = 'Custom template';
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // AI Caption generation
    if (generateCaptionBtn) {
        generateCaptionBtn.addEventListener('click', function() {
            const headline = customHeadline.value || newsSelect.value;
            
            if (!headline) {
                alert('Please select a news headline or enter a custom one');
                return;
            }
            
            generateCaptionBtn.disabled = true;
            generateCaptionBtn.textContent = 'Generating...';
            
            const formData = new FormData();
            formData.append('headline', headline);
            formData.append('csrf_token', csrfToken);
            
            fetch(`${siteUrl}/api/generate-caption.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Split the caption into top and bottom text
                    const caption = data.caption;
                    const splitIndex = Math.ceil(caption.length / 2);
                    const firstPart = caption.substring(0, splitIndex);
                    const secondPart = caption.substring(splitIndex);
                    
                    topText.value = firstPart;
                    bottomText.value = secondPart;
                    
                    // Update preview
                    updatePreviewText();
                } else {
                    alert('Failed to generate caption: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while generating the caption');
            })
            .finally(() => {
                generateCaptionBtn.disabled = false;
                generateCaptionBtn.textContent = 'Generate AI Caption';
            });
        });
    }
    
    // Template change
    if (templateSelect) {
        templateSelect.addEventListener('change', function() {
            if (!memePreview) return;
            
            const templateId = this.value;
            if (!templateId) return;
            
            fetch(`${siteUrl}/api/get-template.php?id=${templateId}&csrf_token=${csrfToken}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const imgElement = memePreview.querySelector('img');
                        if (imgElement) {
                            imgElement.src = data.template_url;
                            imgElement.alt = data.template_name;
                            
                            // Apply current filter after image change
                            updateImageFilter();
                        }
                    }
                })
                .catch(console.error);
        });
    }
    
    // Check URL for template parameter
    const urlParams = new URLSearchParams(window.location.search);
    const templateParam = urlParams.get('template');
    if (templateParam && templateSelect) {
        // Set the template select value and trigger change
        templateSelect.value = templateParam;
        
        // If template isn't in the list, add it as an option
        if (templateSelect.value !== templateParam) {
            const option = document.createElement('option');
            option.value = templateParam;
            option.textContent = 'Selected Template';
            option.selected = true;
            templateSelect.appendChild(option);
        }
        
        // Trigger the change event to load the template
        const event = new Event('change');
        templateSelect.dispatchEvent(event);
    }
    
    // Initialize preview with default values
    updatePreviewText();
    updateImageFilter();
    
    // Download meme
    if (downloadBtn && memePreview) {
        downloadBtn.addEventListener('click', function() {
            // Set button to loading state
            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            // Use html2canvas to convert the preview to an image
            // Additional options to ensure filters and text styles are captured correctly
            html2canvas(memePreview, {
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                scale: 2, // Better quality
                logging: false
            }).then(canvas => {
                const image = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = image;
                link.download = 'meme-news-' + Date.now() + '.png';
                link.click();
                
                // Reset button state
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = 'Download Meme';
            })
            .catch(error => {
                console.error('Error generating meme:', error);
                alert('There was an error generating your meme. Please try again.');
                
                // Reset button state
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = 'Download Meme';
            });
        });
    }
    
    // Share meme
    if (shareBtn && memePreview) {
        shareBtn.addEventListener('click', function() {
            // Set button to loading state
            shareBtn.disabled = true;
            shareBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            html2canvas(memePreview, {
                useCORS: true,
                allowTaint: true,
                backgroundColor: null,
                scale: 2, // Better quality
                logging: false
            }).then(canvas => {
                canvas.toBlob(blob => {
                    // If Web Share API is available
                    if (navigator.share) {
                        const file = new File([blob], 'meme.png', { type: 'image/png' });
                        
                        navigator.share({
                            title: 'MemeNews Generated Meme',
                            text: 'Check out this meme I created with MemeNews!',
                            files: [file]
                        })
                        .catch(error => {
                            console.error('Error sharing:', error);
                            // Fallback to opening in new tab
                            const imageURL = URL.createObjectURL(blob);
                            window.open(imageURL, '_blank');
                        })
                        .finally(() => {
                            // Reset button state
                            shareBtn.disabled = false;
                            shareBtn.innerHTML = 'Share Meme';
                        });
                    } else {
                        // Fallback to opening in new tab
                        const imageURL = URL.createObjectURL(blob);
                        window.open(imageURL, '_blank');
                        
                        // Reset button state
                        shareBtn.disabled = false;
                        shareBtn.innerHTML = 'Share Meme';
                    }
                });
            })
            .catch(error => {
                console.error('Error generating meme for sharing:', error);
                alert('There was an error preparing your meme for sharing. Please try again.');
                
                // Reset button state
                shareBtn.disabled = false;
                shareBtn.innerHTML = 'Share Meme';
            });
        });
    }
}

/**
 * Initializes scroll animations using Intersection Observer
 */
function initAnimations() {
    // Get all elements that should animate on scroll
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animatedElements.length === 0) return;
    
    // Set up the Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                // Unobserve after animation is triggered
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.2, // Trigger when 20% of the element is visible
        rootMargin: '0px 0px -50px 0px' // Adjust the trigger point
    });
    
    // Observe each element
    animatedElements.forEach(element => {
        observer.observe(element);
    });
} 