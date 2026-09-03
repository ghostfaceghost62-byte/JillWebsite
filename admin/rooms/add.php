<?php
declare(strict_types=1);
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();
$types     = $pdo->query('SELECT id, name FROM room_types ORDER BY name')->fetchAll();
$amenities = $pdo->query('SELECT id, name FROM amenities ORDER BY name')->fetchAll();

$pageTitle = 'Add New Room';
require __DIR__ . '/../../includes/header.php';
?>

<div class="admin-page-heading">
    <div>
        <span class="kicker">INVENTORY MANAGEMENT</span>
        <h1>Add New Room</h1>
        <p>Fill in all details below. Upload at least one room image to publish.</p>
    </div>
    <a class="btn small" href="<?=url('admin/rooms/index.php')?>">← Back to Rooms</a>
</div>

<div class="admin-panel" style="max-width:860px;">
    <form method="post" action="<?=url('admin/rooms/save.php')?>" enctype="multipart/form-data" id="addRoomForm">
        <input type="hidden" name="csrf" value="<?=csrf()?>">

        <div class="form-grid-2">
            <!-- Room Number -->
            <div class="form-group">
                <label for="room_number">Room Number <span class="req">*</span></label>
                <input type="text" id="room_number" name="room_number" required maxlength="20"
                       placeholder="e.g. 601" class="form-control">
            </div>
            <!-- Room Type -->
            <div class="form-group">
                <label for="room_type_id">Room Category <span class="req">*</span></label>
                <select id="room_type_id" name="room_type_id" required class="admin-select">
                    <option value="">— Select type —</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?=$t['id']?>"><?=e($t['name'])?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Title -->
            <div class="form-group form-col-2">
                <label for="title">Room Title <span class="req">*</span></label>
                <input type="text" id="title" name="title" required maxlength="160"
                       placeholder="e.g. Deluxe King — City View" class="form-control">
            </div>
            <!-- Description -->
            <div class="form-group form-col-2">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="2000"
                          placeholder="Brief room description for guests…" class="form-control"></textarea>
            </div>
            <!-- Price -->
            <div class="form-group">
                <label for="price_per_night">Nightly Rate (₱) <span class="req">*</span></label>
                <input type="number" id="price_per_night" name="price_per_night" required
                       min="100" max="999999" step="0.01" placeholder="3500.00" class="form-control">
            </div>
            <!-- Max Guests -->
            <div class="form-group">
                <label for="max_guests">Max Guests <span class="req">*</span></label>
                <input type="number" id="max_guests" name="max_guests" required
                       min="1" max="20" value="2" class="form-control">
            </div>
            <!-- Bed Type -->
            <div class="form-group">
                <label for="bed_type">Bed Type</label>
                <input type="text" id="bed_type" name="bed_type" maxlength="80"
                       placeholder="King Bed / Two Queen Beds…" class="form-control">
            </div>
            <!-- Size -->
            <div class="form-group">
                <label for="size">Room Size</label>
                <input type="text" id="size" name="size" maxlength="50"
                       placeholder="e.g. 42 sqm" class="form-control">
            </div>
            <!-- Floor -->
            <div class="form-group">
                <label for="floor">Floor</label>
                <input type="text" id="floor" name="floor" maxlength="20"
                       placeholder="e.g. 6" class="form-control">
            </div>
            <!-- Featured -->
            <div class="form-group" style="display:flex;align-items:center;gap:0.65rem;padding-top:1.6rem;">
                <input type="checkbox" id="featured" name="featured" value="1"
                       style="width:1.1rem;height:1.1rem;">
                <label for="featured" style="margin:0;">Mark as Featured Room</label>
            </div>
        </div>

        <!-- Amenities -->
        <fieldset class="form-group" style="border:1px solid var(--border);border-radius:8px;padding:1rem 1.25rem;margin-top:1rem;">
            <legend style="font-weight:600;font-size:0.9rem;padding:0 0.5rem;">Amenities</legend>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.45rem 1rem;">
                <?php foreach ($amenities as $am): ?>
                    <label style="display:flex;align-items:center;gap:0.45rem;font-size:0.9rem;">
                        <input type="checkbox" name="amenities[]" value="<?=$am['id']?>">
                        <?=e($am['name'])?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>

        <!-- Image Upload -->
        <div class="form-group" style="margin-top:1.25rem;">
            <label>Room Images <span style="font-size:0.8rem;color:var(--text-secondary);">(JPG/PNG/WebP, max 5 MB each, up to 8 files)</span></label>
            <div id="dropzone" class="image-dropzone">
                <input type="file" id="room_images" name="room_images[]"
                       accept="image/jpeg,image/png,image/webp"
                       multiple style="display:none;">
                <div class="dropzone-inner" id="dropzoneTrigger">
                    <svg width="32" height="32" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <p>Drag &amp; drop images here, or <strong>click to browse</strong></p>
                    <small>First image will be the primary photo</small>
                </div>
                <div id="imagePreviewGrid" class="image-preview-grid"></div>
            </div>
        </div>

        <div style="display:flex;gap:1rem;margin-top:2rem;">
            <button type="submit" class="btn btn-gold">Create Room</button>
            <a href="<?=url('admin/rooms/index.php')?>" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<style>
.form-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:1rem; }
.form-col-2  { grid-column:1/-1; }
.form-group label { display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem; }
.form-control { width:100%;padding:0.55rem 0.75rem;border:1px solid var(--border,#D4C5A9);border-radius:6px;font-size:0.9rem;background:var(--surface,#fff);color:var(--text,#1C3328); }
.form-control:focus { outline:none;border-color:var(--brand,#1C3328); }
.req { color:#c0392b; }
.image-dropzone { border:2px dashed var(--border,#D4C5A9);border-radius:10px;padding:1rem;cursor:pointer;transition:border-color .2s; }
.image-dropzone.drag-over { border-color:var(--brand,#1C3328);background:rgba(28,51,40,.04); }
.dropzone-inner { display:flex;flex-direction:column;align-items:center;gap:0.4rem;padding:1.5rem;color:var(--text-secondary,#5C625D);text-align:center; }
.image-preview-grid { display:flex;flex-wrap:wrap;gap:0.75rem;margin-top:0.75rem; }
.image-preview-grid figure { position:relative;width:96px;height:96px;margin:0; }
.image-preview-grid img  { width:96px;height:96px;object-fit:cover;border-radius:6px;border:2px solid var(--border); }
.image-preview-grid .rm-btn { position:absolute;top:-6px;right:-6px;background:#c0392b;color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:11px;line-height:20px;text-align:center; }
.image-preview-grid figure.is-primary img { border-color:var(--brand,#1C3328); }
@media(max-width:600px){ .form-grid-2{ grid-template-columns:1fr; } }
</style>

<script>
(function(){
    const input  = document.getElementById('room_images');
    const grid   = document.getElementById('imagePreviewGrid');
    const zone   = document.getElementById('dropzone');
    const trigger= document.getElementById('dropzoneTrigger');
    let allFiles = [];

    trigger.addEventListener('click', () => input.click());
    zone.addEventListener('dragover', e=>{ e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave',()=> zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e=>{ e.preventDefault(); zone.classList.remove('drag-over'); addFiles(e.dataTransfer.files); });
    input.addEventListener('change', ()=> addFiles(input.files));

    function addFiles(files){
        [...files].forEach(f=>{
            if(allFiles.length >= 8) return;
            if(!f.type.match(/^image\/(jpeg|png|webp)$/)) return;
            if(f.size > 5*1024*1024) { alert(f.name + ' exceeds 5 MB limit.'); return; }
            allFiles.push(f);
        });
        renderPreviews();
        syncInput();
    }

    function renderPreviews(){
        grid.innerHTML='';
        allFiles.forEach((f,i)=>{
            const fig=document.createElement('figure');
            if(i===0) fig.classList.add('is-primary');
            const img=document.createElement('img');
            img.alt=f.name;
            const url=URL.createObjectURL(f);
            img.src=url;
            const rm=document.createElement('button');
            rm.type='button'; rm.textContent='✕'; rm.className='rm-btn';
            rm.addEventListener('click',()=>{ allFiles.splice(i,1); renderPreviews(); syncInput(); });
            fig.appendChild(img); fig.appendChild(rm); grid.appendChild(fig);
        });
    }

    function syncInput(){
        const dt=new DataTransfer();
        allFiles.forEach(f=>dt.items.add(f));
        input.files=dt.files;
    }
})();
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
