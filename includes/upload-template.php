<?php
// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = '/upload-template';
    $_SESSION['error'] = 'You need to log in to upload templates.';
    header('Location: /login');
    exit;
}

// Get user ID and username
$user_id = getCurrentUserId();
$username = getCurrentUsername();

// Get categories for dropdown
$sql = "SELECT id, name FROM template_categories ORDER BY name ASC";
$categories = $db->fetchAll($sql, []);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <h1 class="page-title">Upload Meme Template</h1>
            <p class="lead">Share your creative template with the MemeNews community.</p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form action="/api/user/upload_template.php" method="post" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        
                        <div class="mb-4">
                            <label for="template_name" class="form-label">Template Name*</label>
                            <input type="text" class="form-control" id="template_name" name="name" required>
                            <div class="form-text">Choose a descriptive name for your template (max 100 characters).</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="template_description" class="form-label">Description*</label>
                            <textarea class="form-control" id="template_description" name="description" rows="3" required></textarea>
                            <div class="form-text">Describe your template and suggest how it could be used (max 500 characters).</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="template_category" class="form-label">Category*</label>
                            <select class="form-select" id="template_category" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Choose the category that best fits your template.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="template_tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="template_tags" name="tags">
                            <div class="form-text">Enter comma-separated tags to help users find your template (e.g., funny, reaction, politics).</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="template_image" class="form-label">Template Image*</label>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" class="form-control visually-hidden" id="template_image" name="image" required accept="image/jpeg,image/png,image/gif">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p>Drag and drop your image here, or click to browse</p>
                                    <small class="text-muted">Accepted formats: JPG, PNG, GIF (max 5MB)</small>
                                </div>
                                <div class="preview-container d-none" id="previewContainer">
                                    <img src="" alt="Template preview" id="imagePreview" class="img-fluid mb-2">
                                    <p class="mb-0 text-muted" id="fileInfo"></p>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeImage">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                            <div class="form-text">Upload a clean image without text for the best meme template.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Text Positions</label>
                            <div class="text-positions">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="top" id="position_top" name="text_positions[]">
                                    <label class="form-check-label" for="position_top">Top Text</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="middle" id="position_middle" name="text_positions[]">
                                    <label class="form-check-label" for="position_middle">Middle Text</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="bottom" id="position_bottom" name="text_positions[]">
                                    <label class="form-check-label" for="position_bottom">Bottom Text</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="custom" id="position_custom" name="text_positions[]">
                                    <label class="form-check-label" for="position_custom">Custom Positions</label>
                                </div>
                            </div>
                            <div class="form-text">Indicate where text can be placed on your template.</div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="1" id="terms_agree" name="terms_agree" required>
                            <label class="form-check-label" for="terms_agree">
                                I confirm that I have the right to share this image and it doesn't violate any copyright laws or community guidelines.*
                            </label>
                        </div>
                        
                        <div class="upload-guidelines mb-4">
                            <h5>Upload Guidelines:</h5>
                            <ul>
                                <li>Only upload images that you own or have the right to use</li>
                                <li>No explicit, offensive, or copyrighted content</li>
                                <li>Ideal template size is 800-1200px width</li>
                                <li>Templates must be appropriate for all audiences</li>
                                <li>All uploads are reviewed before being published</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Upload Template</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .upload-area {
        border: 2px dashed #ced4da;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .upload-area.dragover {
        background-color: #e9ecef;
        border-color: #6c757d;
    }
    
    .upload-placeholder {
        color: #6c757d;
    }
    
    .upload-area:hover {
        background-color: #e9ecef;
    }
    
    .preview-container {
        max-width: 300px;
        margin: 0 auto;
    }
    
    .text-positions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .upload-guidelines {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        border-left: 4px solid #0d6efd;
    }
    
    .upload-guidelines ul {
        padding-left: 1.25rem;
        margin-bottom: 0;
    }
    
    .page-title {
        color: #333;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const uploadInput = document.getElementById('template_image');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const fileInfo = document.getElementById('fileInfo');
    const removeButton = document.getElementById('removeImage');
    
    // Click to select file
    uploadArea.addEventListener('click', function() {
        if (previewContainer.classList.contains('d-none')) {
            uploadInput.click();
        }
    });
    
    // Handle file selection
    uploadInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            handleFileUpload(file);
        }
    });
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files && e.dataTransfer.files.length) {
            const file = e.dataTransfer.files[0];
            uploadInput.files = e.dataTransfer.files;
            handleFileUpload(file);
        }
    });
    
    // Remove image button
    removeButton.addEventListener('click', function(e) {
        e.stopPropagation();
        uploadInput.value = '';
        previewContainer.classList.add('d-none');
        uploadPlaceholder.classList.remove('d-none');
    });
    
    // Handle file upload and preview
    function handleFileUpload(file) {
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Invalid file type. Please upload a JPG, PNG, or GIF file.');
            return;
        }
        
        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum file size is 5MB.');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            
            // Format file size
            let fileSize = '';
            if (file.size < 1024) {
                fileSize = file.size + ' bytes';
            } else if (file.size < 1024 * 1024) {
                fileSize = (file.size / 1024).toFixed(1) + ' KB';
            } else {
                fileSize = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
            }
            
            fileInfo.textContent = file.name + ' (' + fileSize + ')';
            
            uploadPlaceholder.classList.add('d-none');
            previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script> 