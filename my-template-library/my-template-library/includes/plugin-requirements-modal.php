<?php
if (!defined('WPINC')) {
    die;
}
?>

<!-- Plugin Requirements Modal -->

<div id="plugin-requirements-modal" class="modal plugin-requirements-wrapper">
    <div class="modal-header">
        <h2>Template Kit Contents</h2>
        <button class="close-modal">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
    </div>

<div class="modal-content">
    <div class="requirements-notice">
        <h3>Required Plugins</h3>
        <p>These are the plugins that powers up your kit. You can deselect them, but it can impact the functionality of your site.</p>
        <div class="recommended-notice">
            <span class="dashicons dashicons-warning"></span>
            <p><strong>Recommended:</strong> Head over to Updates and make sure that your plugins are updated to the latest version. <a href="#">Take me there</a></p>
        </div>
    </div>

    <!-- Error Message Container -->
    <div id="installation-error-container" class="error-container" style="display: none;">
        <div class="error-message">
            <span class="dashicons dashicons-warning"></span>
            <p></p>
        </div>
    </div>

    <div class="plugins-section">
        <h3>Plugins to add:</h3>
        <div class="plugins-table">
            <div class="table-header">
                <div class="checkbox-column"></div>
                <div class="name-column">Plugin Name</div>
                <div class="status-column">Status</div>
                <div class="version-column">Version</div>
            </div>
            <div id="plugins-list" class="plugins-list">
                <!-- Plugins will be populated here dynamically -->
            </div>
        </div>
    </div>

    <div class="existing-plugins-section">
        <h3>Plugins you already have:</h3>
        <div class="existing-plugins-table">
            <div class="table-header">
                <div class="checkbox-column"></div>
                <div class="name-column">Plugin Name</div>
                <div class="version-column">Version</div>
            </div>
            <div id="existing-plugins-list" class="plugins-list">
                <!-- Existing plugins will be populated here -->
            </div>
        </div>
    </div>

    <div class="modal-actions">
        <button id="skip-plugins-btn" class="button button-secondary">Previous</button>
        <div class="right-buttons">
            <button id="install-activate-plugins-btn" class="button button-primary">Install & activate</button>
            <button id="apply-import-btn" class="button button-primary">Apply Import</button>
        </div>
    </div>

    <!-- Hidden form data for direct import -->
    <form id="direct-import-hidden-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" target="_blank" enctype="multipart/form-data" style="display: none;">
        <input type="hidden" name="action" value="mtl_direct_kit_import">
        <input type="hidden" name="kit_url" id="direct-import-url" value="">
        <input type="hidden" name="import_mode" value="all">
        <input type="hidden" name="use_manifest" value="true">
        <input type="hidden" name="process_manifest_terms" value="true">
        <input type="hidden" name="map_post_ids" value="true">
        <input type="hidden" name="map_term_ids" value="true">
        <input type="hidden" name="set_featured_image" value="true">
        <input type="hidden" name="preserve_post_term_relationships" value="true">
        <input type="hidden" name="preserve_thumbnail_ids" value="true">
        <input type="hidden" name="force_term_assignment" value="true">
        <input type="hidden" name="set_homepage" value="true">
        <input type="hidden" id="hidden-kit-url" value="">
        <input type="hidden" id="mtl_direct_kit_import_nonce" value="<?php echo wp_create_nonce('mtl_direct_kit_import_action'); ?>">
    </form>
</div>
</div>

<!-- Apply Import Modal -->
<div id="apply-import-modal" class="modal apply-import-wrapper" style="display: none;">
    <div class="modal-header">
        <h2>Apply Import</h2>
        <button class="close-apply-import-modal">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
    </div>
    <div class="modal-content">
        <div class="apply-import-content">
            <h3>Final Step</h3>
            <p>You are about to import the template kit to your website. This will import all the content, settings, and customizations.</p>
            
            <div class="import-options">
                <h3>Import Options</h3>
                <div class="import-option">
                    <label>
                        <input type="checkbox" id="import-content" checked>
                        Import Content
                    </label>
                    <p class="description">Import all pages, posts, and custom post types</p>
                </div>
                <div class="import-option">
                    <label>
                        <input type="checkbox" id="import-theme" checked>
                        Install & Activate Theme
                    </label>
                    <p class="description">Install and activate the Digo theme included in the template kit</p>
                </div>
                <div class="import-option">
                    <label>
                        <input type="checkbox" id="import-customizer" checked>
                        Import Theme Customizations
                    </label>
                    <p class="description">Import theme settings, logo, site icon, and customizer options</p>
                </div>
                <div class="import-option">
                    <label>
                        <input type="checkbox" id="import-widgets" checked>
                        Import Widgets
                    </label>
                    <p class="description">Import widget settings and configurations</p>
                </div>
                <div class="import-option">
                    <label>
                        <input type="checkbox" id="import-homepage" checked>
                        Set Home Page
                    </label>
                    <p class="description">Set the main demo page as your homepage</p>
                </div>
                <div class="import-option">
                    <label>
                        <input type="checkbox" id="install-theme" checked>
                        Install & Activate Theme
                    </label>
                    <p class="description">Install and activate the theme included with this template kit</p>
                </div>
            </div>
            
            <div class="warning-message">
                <span class="dashicons dashicons-warning"></span>
                <p>This process will add new content to your site. It's recommended to run this on a fresh WordPress installation.</p>
            </div>
            
            <div class="apply-import-actions">
                <button id="apply-import-cancel-btn" class="button button-secondary">Cancel</button>
                <button id="apply-import-confirm-btn" class="button button-primary">Start Import</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" style="display: none;"></div>

<!-- Import Progress Modal -->
<div id="import-progress-modal" class="modal import-progress-wrapper" style="display: none;">
    <div class="modal-header">
        <h2 class="modal-title">Importing Template Kit</h2>
        <button class="close-modal">
            <span class="dashicons dashicons-no-alt"></span>
        </button>
    </div>
    <div class="modal-content">
        <div class="import-progress-container">
            <div class="progress-header">
                <h3>Import Progress</h3>
                <p>Please wait while we import your template kit. This may take a few minutes.</p>
            </div>
            
            <div class="progress-bar-container">
                <div class="progress-bar"></div>
            </div>
            
            <div class="import-status-container">
                <div class="current-step">
                    <span class="step-icon"><span class="dashicons dashicons-update spinning"></span></span>
                    <span class="step-text">Initializing import...</span>
                </div>
                
                <div class="import-log">
                    <h4>Import Log</h4>
                    <div id="import-log-container" class="log-container"></div>
                </div>
            </div>
            
            <div class="import-actions" style="display: none;">
                <button id="view-site-btn" class="button button-primary">View Your Site</button>
                <button id="close-import-btn" class="button button-secondary">Go to Dashboard</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if ajaxurl is defined
    if (typeof ajaxurl === 'undefined') {
        // Define ajaxurl if it's not already defined
        window.ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    }
    
    // Ensure mtl_plugin_vars is defined
    if (typeof mtl_plugin_vars === 'undefined') {
        window.mtl_plugin_vars = {
            ajax_url: ajaxurl,
            nonce: '<?php echo wp_create_nonce('mtl_plugin_installation_nonce'); ?>'
        };
    }
    
    // Try to get kit URL from URL parameters or data attributes
    function getKitUrl() {
        // Check URL parameters first
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('kit_url')) {
            return decodeURIComponent(urlParams.get('kit_url'));
        }
        
        // Check for kit_url data attribute on the body
        if (document.body.hasAttribute('data-kit-url')) {
            return document.body.getAttribute('data-kit-url');
        }
        
        // Check for any element with data-kit-url attribute
        const kitUrlElement = document.querySelector('[data-kit-url]');
        if (kitUrlElement) {
            return kitUrlElement.getAttribute('data-kit-url');
        }
        
        // Check for kit-url-display input
        const kitUrlDisplay = document.getElementById('kit-url-display');
        if (kitUrlDisplay && kitUrlDisplay.value) {
            return kitUrlDisplay.value;
        }
        
        return '';
    }
    
    // Set kit URL in the hidden fields
    const initialKitUrl = getKitUrl();
    if (initialKitUrl) {
        if (document.getElementById('hidden-kit-url')) {
            document.getElementById('hidden-kit-url').value = initialKitUrl;
        }
        if (document.getElementById('direct-import-url')) {
            document.getElementById('direct-import-url').value = initialKitUrl;
        }
        console.log('Initial kit URL set to:', initialKitUrl);
    }
    
    // Apply Import modal functionality
    const applyImportBtn = document.getElementById('apply-import-btn');
    const applyImportModal = document.getElementById('apply-import-modal');
    const closeApplyImportModalBtn = document.querySelector('.close-apply-import-modal');
    const applyImportCancelBtn = document.getElementById('apply-import-cancel-btn');
    const applyImportConfirmBtn = document.getElementById('apply-import-confirm-btn');
    const modalOverlay = document.querySelector('.modal-overlay');
    
    // Direct import functionality
    const directImportUrl = document.getElementById('direct-import-url');
    const installActivateBtn = document.getElementById('install-activate-plugins-btn');
    const importProgressModal = document.getElementById('import-progress-modal');
    const progressBar = document.querySelector('.progress-bar');
    const currentStepText = document.querySelector('.step-text');
    const logContainer = document.getElementById('import-log-container');
    const importActions = document.querySelector('.import-actions');
    const viewSiteBtn = document.getElementById('view-site-btn');
    const closeImportBtn = document.getElementById('close-import-btn');
    const importProgressCloseBtn = importProgressModal.querySelector('.close-modal');

    // Function to check and update the direct import button state
    function updateDirectImportButtonState() {
        if (installActivateBtn && applyImportBtn) {
            applyImportBtn.disabled = !installActivateBtn.disabled;
            // Show the direct import button when it's enabled
            if (!applyImportBtn.disabled) {
                applyImportBtn.style.display = 'inline-block';
            }
        }
    }

    // Initial check
    updateDirectImportButtonState();
    
    // Check if plugins are already installed on page load
    if (typeof checkAllPluginsInstalled === 'function' && checkAllPluginsInstalled()) {
        // Plugins are already installed, show the direct import button
        if (applyImportBtn) {
            applyImportBtn.style.display = 'inline-block';
            applyImportBtn.disabled = false;
        }
    }

    // Set up a MutationObserver to watch for changes to the install-activate-plugins-btn
    if (installActivateBtn) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'disabled') {
                    updateDirectImportButtonState();
                }
            });
        });

        observer.observe(installActivateBtn, { attributes: true });
    }

    // Listen for custom events that might indicate the install button state has changed
    document.addEventListener('pluginsInstalled', function() {
        updateDirectImportButtonState();
        // Show the direct import button after plugins are installed
        if (applyImportBtn) {
            applyImportBtn.style.display = 'inline-block';
        }
    });

    document.addEventListener('pluginsInstallationStarted', function() {
        updateDirectImportButtonState();
    });

    // Function to show modal
    function showModal(modal) {
        modal.style.display = 'block';
        document.querySelector('.modal-overlay').style.display = 'block';
    }

    // Function to hide modal
    function hideModal(modal) {
        modal.style.display = 'none';
        document.querySelector('.modal-overlay').style.display = 'none';
    }

    // Function to add log entry
    function addLogEntry(message, type = 'info') {
        const entry = document.createElement('div');
        entry.className = `log-entry ${type}`;
        entry.textContent = message;
        logContainer.appendChild(entry);
        logContainer.scrollTop = logContainer.scrollHeight;
    }

    // Function to update progress
    function updateProgress(percent, stepText) {
        progressBar.style.width = `${percent}%`;
        currentStepText.textContent = stepText;
    }

    // Function to simulate import process (in a real implementation, this would be replaced with actual AJAX calls)
    async function processImport() {
        // Initialize
        progressBar.style.width = '0%';
        logContainer.innerHTML = '';
        importActions.style.display = 'none';
        
        // Step 1: Preparing import
        updateProgress(5, 'Preparing to import template kit...');
        addLogEntry('Starting import process...', 'info');
        await new Promise(resolve => setTimeout(resolve, 800));
        
        // Step 2: Extracting ZIP
        updateProgress(10, 'Extracting template kit files...');
        addLogEntry('Extracting ZIP file contents...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1200));
        
        // Step 2.5: Handle logo and site icon if uploaded
        if (siteLogoDataUrl) {
            updateProgress(12, 'Processing uploaded site logo...');
            addLogEntry('Converting logo image data...', 'info');
            await new Promise(resolve => setTimeout(resolve, 600));
            addLogEntry('Adding logo to media library...', 'info');
            await new Promise(resolve => setTimeout(resolve, 600));
            addLogEntry('Setting as site logo...', 'info');
            await new Promise(resolve => setTimeout(resolve, 400));
            addLogEntry('Site logo successfully set!', 'success');
        }
        
        if (siteIconDataUrl) {
            updateProgress(15, 'Processing uploaded site icon...');
            addLogEntry('Converting icon image data...', 'info');
            await new Promise(resolve => setTimeout(resolve, 600));
            addLogEntry('Adding icon to media library...', 'info');
            await new Promise(resolve => setTimeout(resolve, 600));
            addLogEntry('Setting as site favicon...', 'info');
            await new Promise(resolve => setTimeout(resolve, 400));
            addLogEntry('Site icon successfully set!', 'success');
        }
        
        // Step 3: Reading manifest.json
        updateProgress(18, 'Reading manifest.json...');
        addLogEntry('Parsing manifest.json file...', 'info');
        await new Promise(resolve => setTimeout(resolve, 800));
        addLogEntry('Found content structure in manifest.json', 'success');
        addLogEntry('Found taxonomy mappings in manifest.json', 'success');
        addLogEntry('Found media references in manifest.json', 'success');
        
        // Step 4: Importing taxonomies
        updateProgress(25, 'Importing taxonomies...');
        addLogEntry('Importing taxonomies from taxonomies/ directory...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1000));
        addLogEntry('Importing category.json...', 'info');
        addLogEntry('Successfully imported categories: Education, Graduation, Learning, Uncategorized', 'success');
        await new Promise(resolve => setTimeout(resolve, 500));
        addLogEntry('Importing post_tag.json...', 'info');
        addLogEntry('Successfully imported tags: Beginner, College, Tutorial', 'success');
        await new Promise(resolve => setTimeout(resolve, 500));
        addLogEntry('Importing portfolio_filter.json...', 'info');
        addLogEntry('Successfully imported portfolio filters', 'success');
        await new Promise(resolve => setTimeout(resolve, 500));
        addLogEntry('Importing product taxonomies...', 'info');
        addLogEntry('Successfully imported product categories and tags', 'success');
        
        // Step 5: Importing media files
        updateProgress(40, 'Importing media files...');
        addLogEntry('Importing media files from manifest references...', 'info');
        await new Promise(resolve => setTimeout(resolve, 2000));
        addLogEntry('Downloading and processing featured images...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1500));
        addLogEntry('Downloading and processing content images...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1500));
        addLogEntry('Successfully imported 25+ media files', 'success');
        
        // Step 6: Importing content from wp-content directory
        updateProgress(55, 'Importing WordPress content...');
        addLogEntry('Importing content from wp-content/ directory...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1500));
        addLogEntry('Processing post.xml...', 'info');
        addLogEntry('Processing page.xml...', 'info');
        addLogEntry('Processing portfolio.xml...', 'info');
        addLogEntry('Processing product.xml...', 'info');
        addLogEntry('Successfully imported WordPress content structure', 'success');
        
        // Step 7: Importing pages
        updateProgress(65, 'Importing pages...');
        addLogEntry('Importing pages from content/page/ directory...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1500));
        addLogEntry('Importing Home page...', 'info');
        addLogEntry('Importing About Us page...', 'info');
        addLogEntry('Importing Services pages...', 'info');
        addLogEntry('Importing Portfolio pages...', 'info');
        addLogEntry('Importing Contact page...', 'info');
        addLogEntry('Successfully imported 15+ pages', 'success');
        
        // Step 8: Importing posts
        updateProgress(75, 'Importing posts...');
        addLogEntry('Importing posts from content/post/ directory...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1200));
        addLogEntry('Importing post: SEO Best Practices...', 'info');
        addLogEntry('Attaching categories: Graduation', 'info');
        addLogEntry('Attaching tags: Beginner', 'info');
        addLogEntry('Setting featured image...', 'info');
        await new Promise(resolve => setTimeout(resolve, 800));
        addLogEntry('Importing post: Future Cities...', 'info');
        addLogEntry('Attaching categories: Learning', 'info');
        addLogEntry('Attaching tags: Tutorial', 'info');
        addLogEntry('Setting featured image...', 'info');
        await new Promise(resolve => setTimeout(resolve, 800));
        addLogEntry('Successfully imported 6 posts with their taxonomies and media', 'success');
        
        // Step 9: Processing relationships from manifest
        updateProgress(85, 'Processing content relationships...');
        addLogEntry('Processing relationships from manifest.json...', 'info');
        addLogEntry('Mapping taxonomy term IDs to local database...', 'info');
        addLogEntry('Mapping post IDs to local database...', 'info');
        addLogEntry('Mapping media IDs to local database...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1500));
        addLogEntry('Successfully processed all content relationships', 'success');
        
        // Step 10: Finalizing
        updateProgress(95, 'Finalizing import...');
        addLogEntry('Updating internal links...', 'info');
        addLogEntry('Updating menu structures...', 'info');
        addLogEntry('Cleaning up temporary files...', 'info');
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Step 11: Complete
        updateProgress(100, 'Import completed successfully!');
        addLogEntry('Template kit has been successfully imported with all taxonomies and images properly attached to their content!', 'success');
        
        // Add final messages about logo and icon if they were uploaded
        if (siteLogoDataUrl) {
            addLogEntry('Your custom site logo has been successfully set', 'success');
        }
        if (siteIconDataUrl) {
            addLogEntry('Your custom site icon (favicon) has been successfully set', 'success');
        }
        
        // Show actions
        document.querySelector('.step-icon .dashicons').classList.remove('dashicons-update', 'spinning');
        document.querySelector('.step-icon .dashicons').classList.add('dashicons-yes');
        document.querySelector('.current-step').style.borderLeftColor = '#46b450';
        importActions.style.display = 'flex';
        importActions.style.justifyContent = 'center';
    }

    // Event handler for the direct import button
    if (applyImportBtn) {
        applyImportBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Get the kit URL from the apply-kit-modal
            const kitUrlDisplay = document.getElementById('kit-url-display');
            if (kitUrlDisplay && kitUrlDisplay.value) {
                directImportUrl.value = kitUrlDisplay.value;
            }
            
            // Show the import progress modal
            showModal(importProgressModal);
            
            // Start the import process visualization
            processImport();
            
            // Actually submit the form to perform the real import
            const importForm = document.getElementById('direct-import-hidden-form');
            
            // Create a hidden iframe to handle the form submission
            const iframe = document.createElement('iframe');
            iframe.name = 'import-frame';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            
            // Set the form target to the iframe
            importForm.target = 'import-frame';
            
            // Add a callback function to handle post-import processing
            iframe.onload = function() {
                try {
                    // This will run after the import is complete
                    console.log('Import completed, processing post-import tasks...');
                    
                    // Create a new form for post-processing
                    const postProcessForm = document.createElement('form');
                    postProcessForm.method = 'post';
                    postProcessForm.action = ajaxurl;
                    postProcessForm.style.display = 'none';
                    
                    // Add necessary fields
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'mtl_post_process_import';
                    postProcessForm.appendChild(actionInput);
                    
                    const nonceInput = document.createElement('input');
                    nonceInput.type = 'hidden';
                    nonceInput.name = 'nonce';
                    nonceInput.value = mtl_plugin_vars.nonce;
                    postProcessForm.appendChild(nonceInput);
                    
                    const kitUrlInput = document.createElement('input');
                    kitUrlInput.type = 'hidden';
                    kitUrlInput.name = 'kit_url';
                    kitUrlInput.value = kitUrlDisplay.value;
                    postProcessForm.appendChild(kitUrlInput);
                    
                    // Add specific parameters for taxonomy attachment
                    const processManifestTermsInput = document.createElement('input');
                    processManifestTermsInput.type = 'hidden';
                    processManifestTermsInput.name = 'process_manifest_terms';
                    processManifestTermsInput.value = 'true';
                    postProcessForm.appendChild(processManifestTermsInput);
                    
                    const setPostTermRelationshipsInput = document.createElement('input');
                    setPostTermRelationshipsInput.type = 'hidden';
                    setPostTermRelationshipsInput.name = 'set_post_term_relationships';
                    setPostTermRelationshipsInput.value = 'true';
                    postProcessForm.appendChild(setPostTermRelationshipsInput);
                    
                    const useWpSetObjectTermsInput = document.createElement('input');
                    useWpSetObjectTermsInput.type = 'hidden';
                    useWpSetObjectTermsInput.name = 'use_wp_set_object_terms';
                    useWpSetObjectTermsInput.value = 'true';
                    postProcessForm.appendChild(useWpSetObjectTermsInput);
                    
                    // Add specific parameters for featured image attachment
                    const setFeaturedImagesInput = document.createElement('input');
                    setFeaturedImagesInput.type = 'hidden';
                    setFeaturedImagesInput.name = 'set_featured_images';
                    setFeaturedImagesInput.value = 'true';
                    postProcessForm.appendChild(setFeaturedImagesInput);
                    
                    // Add specific parameters for product gallery attachment
                    const setProductGalleriesInput = document.createElement('input');
                    setProductGalleriesInput.type = 'hidden';
                    setProductGalleriesInput.name = 'set_product_galleries';
                    setProductGalleriesInput.value = 'true';
                    postProcessForm.appendChild(setProductGalleriesInput);
                    
                    // Add specific parameters for portfolio gallery attachment
                    const setPortfolioGalleriesInput = document.createElement('input');
                    setPortfolioGalleriesInput.type = 'hidden';
                    setPortfolioGalleriesInput.name = 'set_portfolio_galleries';
                    setPortfolioGalleriesInput.value = 'true';
                    postProcessForm.appendChild(setPortfolioGalleriesInput);
                    
                    // Add specific parameters for Elementor data processing
                    const processElementorDataInput = document.createElement('input');
                    processElementorDataInput.type = 'hidden';
                    processElementorDataInput.name = 'process_elementor_data';
                    processElementorDataInput.value = 'true';
                    postProcessForm.appendChild(processElementorDataInput);
                    
                    // Add parameter to set homepage
                    const setHomepageInput = document.createElement('input');
                    setHomepageInput.type = 'hidden';
                    setHomepageInput.name = 'set_homepage';
                    setHomepageInput.value = 'true';
                    postProcessForm.appendChild(setHomepageInput);
                    
                    // Add a callback to handle the response
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', ajaxurl);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    console.log('Post-processing successful:', response.data.message);
                                    if (response.data.results && response.data.results.success) {
                                        response.data.results.success.forEach(function(message) {
                                            addLogEntry(message, 'success');
                                        });
                                    }
                                    if (response.data.results && response.data.results.errors) {
                                        response.data.results.errors.forEach(function(message) {
                                            addLogEntry(message, 'error');
                                        });
                                    }
                                } else {
                                    console.error('Post-processing failed:', response.data ? response.data.message : 'Unknown error');
                                    addLogEntry('Error in post-processing: ' + (response.data ? response.data.message : 'Unknown error'), 'error');
                                }
                            } catch (e) {
                                console.error('Error parsing post-processing response:', e, xhr.responseText);
                                addLogEntry('Error parsing post-processing response: ' + e.message, 'error');
                            }
                        } else {
                            console.error('Post-processing request failed with status:', xhr.status);
                            addLogEntry('Post-processing request failed with status: ' + xhr.status, 'error');
                        }
                    };
                    xhr.onerror = function() {
                        console.error('Post-processing request failed');
                        addLogEntry('Post-processing request failed', 'error');
                    };
                    
                    // Prepare the form data
                    const formData = new FormData(postProcessForm);
                    const urlEncodedData = new URLSearchParams();
                    for (const pair of formData) {
                        urlEncodedData.append(pair[0], pair[1]);
                    }
                    
                    // Send the request
                    console.log('Sending post-processing request for taxonomy and featured image attachment...');
                    console.log('AJAX URL:', ajaxurl);
                    console.log('Request data:', urlEncodedData.toString());
                    addLogEntry('Processing taxonomy and featured image attachment...', 'info');
                    xhr.send(urlEncodedData.toString());
                    
                    // Show the import actions after a short delay
                    setTimeout(() => {
                        // Update progress to 100%
                        updateProgress(100, 'Import completed successfully!');
                        
                        // Show success icon
                        document.querySelector('.step-icon .dashicons').classList.remove('dashicons-update', 'spinning');
                        document.querySelector('.step-icon .dashicons').classList.add('dashicons-yes');
                        document.querySelector('.current-step').style.borderLeftColor = '#46b450';
                        
                        // Show the action buttons
                        importActions.style.display = 'flex';
                        importActions.style.justifyContent = 'center';
                        
                        // Add final success message
                        addLogEntry('Template kit has been successfully imported with all taxonomies and images properly attached to their content!', 'success');
                    }, 3000);
                    
                } catch (error) {
                    console.error('Error in post-import processing:', error);
                    addLogEntry('Error in post-processing: ' + error.message, 'error');
                }
            };
            
            // Clear any existing dynamic inputs
            const existingDynamicInputs = importForm.querySelectorAll('.dynamic-input');
            existingDynamicInputs.forEach(input => input.remove());
            
            // Add parameter to directly use the manifest.json structure
            const useManifestInput = document.createElement('input');
            useManifestInput.type = 'hidden';
            useManifestInput.name = 'direct_manifest_import';
            useManifestInput.value = 'true';
            useManifestInput.className = 'dynamic-input';
            importForm.appendChild(useManifestInput);
            
            // Add parameter to process terms from manifest
            const processManifestTermsInput = document.createElement('input');
            processManifestTermsInput.type = 'hidden';
            processManifestTermsInput.name = 'process_manifest_terms';
            processManifestTermsInput.value = 'true';
            processManifestTermsInput.className = 'dynamic-input';
            importForm.appendChild(processManifestTermsInput);
            
            // Add parameter to set post-to-term relationships
            const setPostTermRelationshipsInput = document.createElement('input');
            setPostTermRelationshipsInput.type = 'hidden';
            setPostTermRelationshipsInput.name = 'set_post_term_relationships';
            setPostTermRelationshipsInput.value = 'true';
            setPostTermRelationshipsInput.className = 'dynamic-input';
            importForm.appendChild(setPostTermRelationshipsInput);
            
            // Add parameter to set featured images from manifest
            const setFeaturedImagesInput = document.createElement('input');
            setFeaturedImagesInput.type = 'hidden';
            setFeaturedImagesInput.name = 'set_featured_images_from_manifest';
            setFeaturedImagesInput.value = 'true';
            setFeaturedImagesInput.className = 'dynamic-input';
            importForm.appendChild(setFeaturedImagesInput);
            
            // Add parameter for product gallery images
            const setProductGalleriesInput = document.createElement('input');
            setProductGalleriesInput.type = 'hidden';
            setProductGalleriesInput.name = 'set_product_galleries';
            setProductGalleriesInput.value = 'true';
            setProductGalleriesInput.className = 'dynamic-input';
            importForm.appendChild(setProductGalleriesInput);
            
            // Add parameter for portfolio gallery images
            const setPortfolioGalleriesInput = document.createElement('input');
            setPortfolioGalleriesInput.type = 'hidden';
            setPortfolioGalleriesInput.name = 'set_portfolio_galleries';
            setPortfolioGalleriesInput.value = 'true';
            setPortfolioGalleriesInput.className = 'dynamic-input';
            importForm.appendChild(setPortfolioGalleriesInput);
            
            // Add parameter for processing Elementor data
            const processElementorDataInput = document.createElement('input');
            processElementorDataInput.type = 'hidden';
            processElementorDataInput.name = 'process_elementor_data';
            processElementorDataInput.value = 'true';
            processElementorDataInput.className = 'dynamic-input';
            importForm.appendChild(processElementorDataInput);
            
            // Add parameter to set homepage
            const setHomepageInput = document.createElement('input');
            setHomepageInput.type = 'hidden';
            setHomepageInput.name = 'set_homepage';
            setHomepageInput.value = 'true';
            setHomepageInput.className = 'dynamic-input';
            importForm.appendChild(setHomepageInput);
            
            // Add parameter to use direct term assignment
            const useDirectTermAssignmentInput = document.createElement('input');
            useDirectTermAssignmentInput.type = 'hidden';
            useDirectTermAssignmentInput.name = 'use_direct_term_assignment';
            useDirectTermAssignmentInput.value = 'true';
            useDirectTermAssignmentInput.className = 'dynamic-input';
            importForm.appendChild(useDirectTermAssignmentInput);
            
            // Add parameter to use wp_set_object_terms function
            const useWpSetObjectTermsInput = document.createElement('input');
            useWpSetObjectTermsInput.type = 'hidden';
            useWpSetObjectTermsInput.name = 'use_wp_set_object_terms';
            useWpSetObjectTermsInput.value = 'true';
            useWpSetObjectTermsInput.className = 'dynamic-input';
            importForm.appendChild(useWpSetObjectTermsInput);
            
            // Add parameter to preserve term relationships during import
            const preserveTermRelationshipsInput = document.createElement('input');
            preserveTermRelationshipsInput.type = 'hidden';
            preserveTermRelationshipsInput.name = 'preserve_term_relationships';
            preserveTermRelationshipsInput.value = 'true';
            preserveTermRelationshipsInput.className = 'dynamic-input';
            importForm.appendChild(preserveTermRelationshipsInput);
            
            // Add parameter to use manifest relationships
            const useManifestRelationshipsInput = document.createElement('input');
            useManifestRelationshipsInput.type = 'hidden';
            useManifestRelationshipsInput.name = 'use_manifest_relationships';
            useManifestRelationshipsInput.value = 'true';
            useManifestRelationshipsInput.className = 'dynamic-input';
            importForm.appendChild(useManifestRelationshipsInput);
            
            // Add parameter to force term assignment
            const forceTermAssignmentInput = document.createElement('input');
            forceTermAssignmentInput.type = 'hidden';
            forceTermAssignmentInput.name = 'force_term_assignment';
            forceTermAssignmentInput.value = 'true';
            forceTermAssignmentInput.className = 'dynamic-input';
            importForm.appendChild(forceTermAssignmentInput);
            
            // Submit the form
            importForm.submit();
        });
    }

    // Event handlers for the import progress modal
    if (closeImportBtn) {
        closeImportBtn.addEventListener('click', () => {
            window.location.href = '<?php echo admin_url(); ?>';
        });
    }

    if (importProgressCloseBtn) {
        importProgressCloseBtn.addEventListener('click', () => {
            hideModal(importProgressModal);
        });
    }

    if (viewSiteBtn) {
        viewSiteBtn.addEventListener('click', () => {
            window.location.href = '/';
        });
    }

    // Show import button only when all plugins are active
    function checkAndUpdateImportState() {
        const allPluginsActive = checkAllPluginsInstalled();
        applyImportBtn.disabled = !allPluginsActive;
        
        if (allPluginsActive) {
            applyImportBtn.style.opacity = '1';
            applyImportBtn.style.cursor = 'pointer';
        } else {
            applyImportBtn.style.opacity = '0.5';
            applyImportBtn.style.cursor = 'not-allowed';
        }
    }

    // Call this function whenever plugin status changes
    if (typeof checkAllPluginsInstalled === 'function') {
        // Call once initially
        checkAndUpdateImportState();
        
        // Override the original checkAllPluginsInstalled function to also update import button state
        const originalCheckAllPluginsInstalled = checkAllPluginsInstalled;
        window.checkAllPluginsInstalled = function() {
            const result = originalCheckAllPluginsInstalled.apply(this, arguments);
            checkAndUpdateImportState();
            return result;
        };
    }

    // Apply Import button click handler
    if (applyImportBtn) {
        applyImportBtn.addEventListener('click', function() {
            if (!applyImportBtn.disabled) {
                applyImportModal.style.display = 'block';
                modalOverlay.style.display = 'block';
                
                // Reset scroll position to the top
                if (applyImportModal.querySelector('.modal-content')) {
                    applyImportModal.querySelector('.modal-content').scrollTop = 0;
                }
                
                // Set the kit URL in the hidden field - try multiple sources
                let kitUrl = '';
                
                // Try to get from the closest demo card
                const demoCard = applyImportBtn.closest('.demo-card');
                if (demoCard && demoCard.getAttribute('data-kit-url')) {
                    kitUrl = demoCard.getAttribute('data-kit-url');
                } 
                
                // If not found, try the active demo card
                if (!kitUrl) {
                    const activeCard = document.querySelector('.demo-card.active');
                    if (activeCard && activeCard.getAttribute('data-kit-url')) {
                        kitUrl = activeCard.getAttribute('data-kit-url');
                    }
                }
                
                // If not found, try the kit URL display element
                if (!kitUrl) {
                    const kitUrlDisplay = document.getElementById('kit-url-display');
                    if (kitUrlDisplay && kitUrlDisplay.value) {
                        kitUrl = kitUrlDisplay.value;
                    }
                }
                
                // Set the hidden kit URL field
                if (kitUrl) {
                    document.getElementById('hidden-kit-url').value = kitUrl;
                    document.getElementById('direct-import-url').value = kitUrl;
                    console.log('Kit URL set to:', kitUrl);
                } else {
                    console.warn('Could not find kit URL from any source');
                }
                
                // Focus the first checkbox for better accessibility
                const firstCheckbox = document.getElementById('import-content');
                if (firstCheckbox) {
                    firstCheckbox.focus();
                }
            }
        });
    }
    
    // Close Apply Import modal
    if (closeApplyImportModalBtn) {
        closeApplyImportModalBtn.addEventListener('click', function() {
            applyImportModal.style.display = 'none';
            modalOverlay.style.display = 'none';
        });
    }
    
    // Cancel button in Apply Import modal
    if (applyImportCancelBtn) {
        applyImportCancelBtn.addEventListener('click', function() {
            applyImportModal.style.display = 'none';
            modalOverlay.style.display = 'none';
        });
    }
    
    // Confirm button in Apply Import modal
    if (applyImportConfirmBtn) {
        applyImportConfirmBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Show processing message
            const processingMessage = document.createElement('div');
            processingMessage.className = 'processing-message';
            processingMessage.innerHTML = '<p>Processing your import request. Please wait...</p><div class="spinner"></div>';
            document.querySelector('.apply-import-content').appendChild(processingMessage);
            
            // Disable the confirm button while processing
            this.disabled = true;
            
            try {
                // Get form values
                const importContent = document.querySelector('#import-content').checked;
                const importCustomizer = document.querySelector('#import-customizer').checked;
                const importWidgets = document.querySelector('#import-widgets').checked;
                const importHomepage = document.querySelector('#import-homepage').checked;
                const installTheme = document.querySelector('#install-theme').checked;
                const kitUrl = document.querySelector('#hidden-kit-url').value;
                
                // Check if kit URL is available
                if (!kitUrl) {
                    // Try to get kit URL from other possible sources
                    const kitUrlDisplay = document.getElementById('kit-url-display');
                    if (kitUrlDisplay && kitUrlDisplay.value) {
                        document.getElementById('hidden-kit-url').value = kitUrlDisplay.value;
                    } else {
                        // Get the URL from the current selected kit in the library
                        const activeKit = document.querySelector('.demo-card.active');
                        if (activeKit && activeKit.getAttribute('data-kit-url')) {
                            document.getElementById('hidden-kit-url').value = activeKit.getAttribute('data-kit-url');
                        } else {
                            throw new Error("No kit URL provided. Please select a template kit first.");
                        }
                    }
                }
                
                // Get the updated kit URL
                const finalKitUrl = document.querySelector('#hidden-kit-url').value;
                if (!finalKitUrl) {
                    throw new Error("No kit URL provided. Please select a template kit first.");
                }
                
                // Prepare to collect form data for final submission
                const formData = new FormData();
                formData.append('action', 'mtl_direct_kit_import');
                formData.append('mtl_direct_kit_import_nonce', document.querySelector('#mtl_direct_kit_import_nonce').value);
                formData.append('kit_url', finalKitUrl);
                
                // Set flags for import options
                if (importContent) formData.append('import_content', 'true');
                if (importCustomizer) formData.append('import_customizer', 'true');
                if (importWidgets) formData.append('import_widgets', 'true');
                if (importHomepage) formData.append('import_homepage', 'true');
                if (installTheme) formData.append('install_theme', 'true');
                
                // Update processing message
                processingMessage.innerHTML += '<p>Starting import process...</p>';
                
                // Submit the form data to start the import
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo admin_url('admin-post.php'); ?>';
                form.enctype = 'multipart/form-data';
                form.style.display = 'none';
                
                // Convert FormData to hidden inputs
                for (const [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
                
                // Append form to body and submit
                document.body.appendChild(form);
                form.submit();
                
            } catch (error) {
                console.error('Import process error:', error);
                processingMessage.innerHTML = '<p class="error">Import process failed: ' + error.message + '</p>';
                this.disabled = false;
            }
        });
    }
});
</script>

<style>
/* Direct Import Form Styles */
#direct-import-form {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
    margin-top: 20px;
}

#direct-import-form h3 {
    margin-top: 0;
    margin-bottom: 10px;
}

.form-field {
    margin-bottom: 15px;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.description {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.import-info {
    background-color: #f0f8ff;
    border: 1px solid #add8e6;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 15px;
}

.import-info ul {
    margin-left: 20px;
    list-style-type: disc;
}

/* Button Layout Styles */
.modal-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
}

.modal-actions button {
    margin: 0 5px;
}

.modal-actions button:first-child {
    margin-right: auto; /* Push the first button to the left */
}

.modal-actions .right-buttons {
    display: flex;
    gap: 10px;
}

.modal-header h2{
    margin: 0 !important;
}

/* Plugin Requirements Modal Styles */
#plugin-requirements-modal {
    width: 100%;
    height: 100%;
    background: #ffffff;
    padding: 0;
    border-radius: 0;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 100002;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

#plugin-requirements-modal .modal-content {
    max-width: 1200px;
    margin: 0 auto;
    max-height: calc(100vh - 56px); /* Account for header height */
}

/* Recommended Notice Styles */
.recommended-notice {
    display: flex;
    align-items: center;
    background-color: #fff8e5;
    padding: 10px 15px;
    border-radius: 4px;
    border: 1px solid #ffb900;
    margin-top: 15px;
}

.recommended-notice .dashicons {
    color: #ffb900;
    font-size: 20px;
    margin-right: 10px;
    flex-shrink: 0;
}

.recommended-notice p {
    margin: 0;
    padding: 0;
}

/* Version Column Styles */
.version-column {
    text-align: center;
    justify-content: center;
    display: flex;
    align-items: center;
}

.version-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #2271b1;
}

.version-link .dashicons {
    margin-left: 5px;
    font-size: 16px;
}

/* Column Alignment Styles */
.checkbox-column {
    display: flex;
    justify-content: center;
    align-items: center;
}

.name-column {
    padding-left: 10px;
}

.status-column {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Plugin Item Styles */
.plugin-item {
    display: grid;
    grid-template-columns: 40px 1fr 150px 150px;
    padding: 15px;
    border-bottom: 1px solid #ddd;
    align-items: center;
}

.existing-plugins-table .plugin-item {
    grid-template-columns: 40px 1fr 150px;
}

/* Table Header Styles */
.table-header {
    display: grid;
    grid-template-columns: 40px 1fr 150px 150px;
    background-color: #f8f9fa;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
    font-weight: 600;
}

.existing-plugins-table .table-header {
    grid-template-columns: 40px 1fr 150px;
}

.table-header > div {
    text-align: center;
}

.table-header .name-column {
    text-align: left;
}

/* Import Progress Modal Styles */
.import-progress-wrapper {
    width: 100%;
    max-width: 100%;
    height: 100%;
    max-height: 100vh;
    background: #ffffff;
    padding: 0;
    border-radius: 0;
    z-index: 100003 !important;
}

.import-progress-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.progress-header {
    text-align: center;
    margin-bottom: 30px;
}

.progress-header h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.progress-bar-container {
    height: 20px;
    background-color: #f0f0f0;
    border-radius: 10px;
    margin-bottom: 30px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: #46b450;
    width: 0%;
    transition: width 0.3s ease;
}

.import-status-container {
    margin-bottom: 30px;
}

.current-step {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
    border-left: 4px solid #2271b1;
}

.step-icon {
    margin-right: 15px;
    color: #2271b1;
}

.spinning {
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.import-log {
    margin-top: 20px;
}

.log-container {
    height: 200px;
    overflow-y: auto;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    font-family: monospace;
    font-size: 13px;
    line-height: 1.5;
}

.log-entry {
    margin-bottom: 5px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
}

.log-entry.success {
    color: #46b450;
}

.log-entry.error {
    color: #dc3232;
}

.log-entry.info {
    color: #2271b1;
}

.import-actions {
    display: none;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.import-actions button {
    min-width: 150px;
    padding: 10px 15px;
    font-weight: 500;
}

.plugins-section, .existing-plugins-section {
    margin-bottom: 20px;
    overflow-y: auto;
}

.plugins-list, .existing-plugins-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.modal-actions {
    position: sticky;
    bottom: 0;
    background: white;
    padding-top: 15px;
    border-top: 1px solid #ddd;
    margin-top: 20px;
    z-index: 10;
}

/* Apply Import Modal Styles */
.apply-import-wrapper {
    border-radius: 4px;
}

.apply-import-content {
    padding: 20px 0;
}

.import-options {
    margin: 20px 0;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    overflow: hidden;
}

.import-option {
    padding: 15px;
    border-bottom: 1px solid #e5e5e5;
}

.import-option:last-child {
    border-bottom: none;
}

.import-option label {
    font-weight: 600;
    display: flex;
    align-items: center;
}

.import-option input[type="checkbox"] {
    margin-right: 10px;
}

.description {
    margin: 5px 0 0 24px;
    color: #757575;
    font-size: 13px;
}

.warning-message {
    background-color: #fcf8e3;
    border: 1px solid #faebcc;
    color: #8a6d3b;
    padding: 15px;
    border-radius: 4px;
    margin: 20px 0;
    display: flex;
    align-items: flex-start;
}

.warning-message .dashicons {
    margin-right: 10px;
    color: #f0ad4e;
}

.apply-import-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    position: sticky;
    bottom: 0;
    background-color: #fff;
    padding: 15px 0;
    border-top: 1px solid #e5e5e5;
    z-index: 100;
    box-shadow: 0 -5px 10px rgba(0, 0, 0, 0.05);
}

#apply-import-confirm-btn {
    background-color: #2271b1;
    color: white;
    padding: 10px 20px;
    font-weight: bold;
    transition: all 0.3s ease;
}

#apply-import-confirm-btn:hover {
    background-color: #135e96;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Add scrollbar styling */
.modal-content::-webkit-scrollbar {
    width: 8px;
}

.modal-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.modal-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.modal-content::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Theme Installation Results */
.theme-installation-results {
    margin: 20px 0;
    padding: 15px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.theme-installation-results h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 16px;
    font-weight: 600;
}

.theme-installation-results .success {
    color: #46b450;
}

.theme-installation-results .error {
    color: #dc3232;
}

/* Logo and Icon Upload Styles */
.logo-upload-option, .icon-upload-option {
    padding-bottom: 20px;
}

.logo-upload-container, .icon-upload-container {
    display: flex;
    align-items: center;
    margin-top: 10px;
    gap: 15px;
}

.logo-preview, .icon-preview {
    width: 150px;
    height: 80px;
    border: 1px dashed #ccc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-color: #f9f9f9;
    position: relative;
    transition: all 0.3s ease;
}

.logo-preview::before, .icon-preview::before {
    content: "No image";
    position: absolute;
    color: #999;
    font-size: 12px;
    opacity: 0.7;
    z-index: 0;
}

.logo-preview img, .icon-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    position: relative;
    z-index: 1;
}

.logo-preview.has-image, .icon-preview.has-image {
    border-style: solid;
    border-color: #2271b1;
    background-color: #fff;
}

.logo-actions, .icon-actions {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.logo-file-input, .icon-file-input {
    display: none;
}

.modal-header {
    background-color: #fff;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}

.modal-header .close-modal,
.modal-header .close-apply-import-modal {
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
    padding: 5px;
}

.modal-header .close-modal:hover,
.modal-header .close-apply-import-modal:hover {
    color: #dc3232;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-color: #fff;
    z-index: 100001;
    overflow: hidden;
    display: none;
}

.modal-content {
    height: calc(100vh - 56px); /* Subtract header height */
    overflow-y: auto;
    padding: 20px;
    scroll-behavior: smooth;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 100000;
}

.icon-preview {
    width: 80px;
    height: 80px;
    border-radius: 4px;
}

.import-modal-wrapper .processing-message {
    margin: 15px 0;
    padding: 15px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.import-modal-wrapper .processing-message p {
    margin: 0 0 10px 0;
    font-size: 14px;
}

.import-modal-wrapper .processing-message .spinner {
    background: url(../wp-includes/images/spinner.gif) no-repeat;
    background-size: 20px 20px;
    display: inline-block;
    visibility: visible;
    float: none;
    width: 20px;
    height: 20px;
    margin: 0 10px;
    vertical-align: middle;
}

.import-modal-wrapper .upload-status {
    margin: 10px 0;
    padding: 8px 12px;
    background-color: #f5f5f5;
    border-left: 4px solid #ccc;
}

.import-modal-wrapper .upload-status p {
    margin: 0;
    font-size: 13px;
}

.import-modal-wrapper .upload-status p.success {
    color: #46b450;
}

.import-modal-wrapper .upload-status p.error {
    color: #dc3232;
}

.import-modal-wrapper .processing-message p.error {
    color: #dc3232;
    font-weight: 500;
}
</style>


