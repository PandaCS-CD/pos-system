<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mapTextarea = document.getElementById('map');

        // Initialize map preview on page load
        updateMapPreview();

        // Update preview when user types or pastes
        mapTextarea.addEventListener('input', updateMapPreview);
        mapTextarea.addEventListener('paste', updateMapPreview);
    });

    function updateMapPreview() {
        const mapEmbedCode = document.getElementById('map').value.trim();
        const mapPreview = document.getElementById('map-preview');
        const mapPlaceholder = document.getElementById('map-placeholder');

        if (mapEmbedCode === '') {
            // Show placeholder
            mapPlaceholder.style.display = 'flex';
            mapPreview.innerHTML = `
            <div id="map-placeholder" class="d-flex align-items-center justify-content-center h-100 text-muted" style="min-height: 200px;">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt fa-3x mb-2"></i>
                    <p>กรุณาใส่ embed code เพื่อดูตัวอย่างแผนที่</p>
                </div>
            </div>
        `;
            return;
        }

        // Validate if it's an iframe embed code
        const iframeRegex = /<iframe[^>]*src\s*=\s*["']([^"']+)["'][^>]*>/i;
        const match = mapEmbedCode.match(iframeRegex);

        if (match) {
            // Valid iframe embed code
            try {
                // Create a temporary div to parse the HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = mapEmbedCode;
                const iframe = tempDiv.querySelector('iframe');

                if (iframe) {
                    // Set standard preview size and style
                    iframe.style.width = '100%';
                    iframe.style.height = '300px';
                    iframe.style.border = '0';
                    iframe.style.borderRadius = '6px';

                    mapPreview.innerHTML = iframe.outerHTML;
                } else {
                    showMapError('รูปแบบ embed code ไม่ถูกต้อง');
                }
            } catch (error) {
                showMapError('เกิดข้อผิดพลาดในการแสดงแผนที่');
            }
        } else {
            // Invalid embed code
            showMapError('กรุณาใส่ embed code ของ Google Maps ที่ถูกต้อง (ต้องเป็น iframe)');
        }
    }

    function showMapError(message) {
        const mapPreview = document.getElementById('map-preview');
        mapPreview.innerHTML = `
        <div class="d-flex align-items-center justify-content-center h-100 text-danger" style="min-height: 200px;">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-2"></i>
                <p>${message}</p>
                <small class="text-muted">ตัวอย่าง: &lt;iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450"&gt;&lt;/iframe&gt;</small>
            </div>
        </div>
    `;
    }

    document.getElementById('add-map').addEventListener('click', function() {
        const container = document.getElementById('map-container');
        const newRow = document.createElement('div');
        newRow.className = 'map-row mb-3';
        newRow.innerHTML = `
        <div class="row">
            <div class="col-6">
                <input type="text" name="google_maps_embed_code[]" class="form-control" placeholder="Google Maps Embed Code width = 100% height = 250">
            </div>
            <div class="col-5">
                <input type="text" name="location_name[]" class="form-control" placeholder="ชื่อสถานที่">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger btn-md remove-map"><i class="fa fa-minus"></i></button>
            </div>
        </div>
    `;
        container.appendChild(newRow);
    });

    // Remove facility functionality with better event delegation
    document.addEventListener('click', function(e) {
        // Check if clicked element or its parent is a remove button
        const removeButton = e.target.closest('.remove-map');
        if (removeButton) {
            e.preventDefault();
            e.stopPropagation();

            const mapRows = document.querySelectorAll('.map-row');
            if (mapRows.length > 1) {
                removeButton.closest('.map-row').remove();
            }
        }
    });
</script>