<?php
// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = '/collection';
    $_SESSION['error'] = 'You need to log in to view your collections.';
    header('Location: /login');
    exit;
}

// Get user ID
$user_id = getCurrentUserId();
$username = getCurrentUsername();

// Pagination settings
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

// Get the user's collections
$sql = "SELECT * FROM user_collections WHERE user_id = ? ORDER BY created_at DESC";
$collections = $db->fetchAll($sql, [$user_id]);

// Count the user's collections
$sql = "SELECT COUNT(*) as total FROM user_collections WHERE user_id = ?";
$result = $db->fetchOne($sql, [$user_id]);
$total_collections = $result['total'] ?? 0;

// Calculate pagination
$total_pages = ceil($total_collections / $items_per_page);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">My Collections</h1>
            <p class="lead">Organize your favorite memes into custom collections.</p>
            
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
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCollectionModal">
                <i class="fas fa-plus-circle"></i> Create New Collection
            </button>
        </div>
    </div>
    
    <div class="row">
        <?php if (empty($collections)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <h4 class="alert-heading">No Collections Yet!</h4>
                    <p>You haven't created any collections yet. Collections help you organize your memes by theme, style, or any category you like.</p>
                    <hr>
                    <p class="mb-0">Click the "Create New Collection" button above to get started.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($collections as $collection): ?>
                <div class="col-md-4 mb-4">
                    <div class="card collection-card h-100">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0"><?= htmlspecialchars($collection['name']) ?></h5>
                        </div>
                        <?php
                        // Get preview of 4 memes from this collection
                        $sql = "SELECT m.image_url FROM collection_memes cm
                                JOIN memes m ON cm.meme_id = m.id
                                WHERE cm.collection_id = ? 
                                ORDER BY cm.added_at DESC LIMIT 4";
                        $meme_previews = $db->fetchAll($sql, [$collection['id']]);
                        
                        // Get count of memes in the collection
                        $sql = "SELECT COUNT(*) as total FROM collection_memes WHERE collection_id = ?";
                        $meme_count = $db->fetchOne($sql, [$collection['id']]);
                        $total_memes = $meme_count['total'] ?? 0;
                        ?>
                        
                        <div class="card-body">
                            <div class="collection-preview">
                                <?php if (!empty($meme_previews)): ?>
                                    <div class="preview-grid">
                                        <?php foreach ($meme_previews as $preview): ?>
                                            <div class="preview-item">
                                                <img src="<?= htmlspecialchars($preview['image_url']) ?>" alt="Meme preview" class="img-fluid">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-preview">
                                        <i class="fas fa-images fa-3x text-muted"></i>
                                        <p class="text-muted mt-2">No memes added yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <p class="card-text mt-3">
                                <?= htmlspecialchars($collection['description'] ?? 'No description provided.') ?>
                            </p>
                            
                            <p class="text-muted">
                                <i class="fas fa-image"></i> <?= $total_memes ?> meme<?= $total_memes != 1 ? 's' : '' ?>
                                <br>
                                <i class="fas fa-clock"></i> Created on <?= date('M j, Y', strtotime($collection['created_at'])) ?>
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-0">
                            <div class="d-flex justify-content-between">
                                <a href="/collection/view?id=<?= $collection['id'] ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary edit-collection" 
                                            data-id="<?= $collection['id'] ?>"
                                            data-name="<?= htmlspecialchars($collection['name']) ?>"
                                            data-description="<?= htmlspecialchars($collection['description'] ?? '') ?>"
                                            data-bs-toggle="modal" data-bs-target="#editCollectionModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-collection" 
                                            data-id="<?= $collection['id'] ?>"
                                            data-name="<?= htmlspecialchars($collection['name']) ?>"
                                            data-bs-toggle="modal" data-bs-target="#deleteCollectionModal">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if ($total_pages > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Collection Modal -->
<div class="modal fade" id="createCollectionModal" tabindex="-1" aria-labelledby="createCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCollectionModalLabel">Create New Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/api/user/create_collection.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="collection_name" class="form-label">Collection Name*</label>
                        <input type="text" class="form-control" id="collection_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="collection_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="collection_description" name="description" rows="3"></textarea>
                        <div class="form-text">A brief description of what this collection is about.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Collection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Collection Modal -->
<div class="modal fade" id="editCollectionModal" tabindex="-1" aria-labelledby="editCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCollectionModalLabel">Edit Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/api/user/update_collection.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="collection_id" id="edit_collection_id">
                    
                    <div class="mb-3">
                        <label for="edit_collection_name" class="form-label">Collection Name*</label>
                        <input type="text" class="form-control" id="edit_collection_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_collection_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="edit_collection_description" name="description" rows="3"></textarea>
                        <div class="form-text">A brief description of what this collection is about.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Collection Modal -->
<div class="modal fade" id="deleteCollectionModal" tabindex="-1" aria-labelledby="deleteCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCollectionModalLabel">Delete Collection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the collection "<span id="delete_collection_name"></span>"?</p>
                <p class="text-danger">This action cannot be undone. All memes will be removed from this collection.</p>
            </div>
            <form action="/api/user/delete_collection.php" method="post">
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="collection_id" id="delete_collection_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Collection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .collection-card {
        transition: transform 0.2s;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .collection-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .preview-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 5px;
        height: 150px;
    }
    
    .preview-item {
        overflow: hidden;
        border-radius: 5px;
    }
    
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .empty-preview {
        height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    
    .page-title {
        color: #333;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit collection modal
    const editButtons = document.querySelectorAll('.edit-collection');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            
            document.getElementById('edit_collection_id').value = id;
            document.getElementById('edit_collection_name').value = name;
            document.getElementById('edit_collection_description').value = description;
        });
    });
    
    // Delete collection modal
    const deleteButtons = document.querySelectorAll('.delete-collection');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete_collection_id').value = id;
            document.getElementById('delete_collection_name').innerText = name;
        });
    });
});
</script> 