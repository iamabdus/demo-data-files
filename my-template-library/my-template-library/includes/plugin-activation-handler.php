<?php
if (!defined('WPINC')) {
    die;
}

// Define plugin directory if not already defined
if (!defined('MTL_PLUGIN_DIR')) {
    define('MTL_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Required WordPress files
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/file.php';

class Template_Kit_Plugin_Manager {
    private $upgrader;
    private $skin;
    private $errors = [];
    private $log = [];

    public function __construct() {
        // Make sure we have access to WordPress plugin functions
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $this->skin = new WP_Ajax_Upgrader_Skin();
        $this->upgrader = new Plugin_Upgrader($this->skin);
    }

    /**
     * Install and activate multiple plugins in batch
     */
    public function batch_install_plugins($plugins, $batch_size = 5) {
        $results = [];
        $batches = array_chunk($plugins, $batch_size);

        foreach ($batches as $batch) {
            foreach ($batch as $plugin) {
                $results[] = $this->install_and_activate_plugin($plugin);
            }
        }

        return $results;
    }

    /**
     * Main installation method
     */
    public function install_and_activate_plugin($plugin_data) {
        $this->log_message("Starting installation process for {$plugin_data['name']}");

        try {
            // Validate plugin data
            $this->validate_plugin_data($plugin_data);

            // Check if plugin is already installed but inactive
            if (file_exists(WP_PLUGIN_DIR . '/' . dirname($plugin_data['path']))) {
                return $this->activate_plugin($plugin_data);
            }

            // Check if plugin is available on WordPress.org
            $wp_org_download = $this->get_wordpress_plugin_download_url($plugin_data['slug']);
            
            if ($wp_org_download) {
                // If available on WordPress.org, treat as free plugin
                $plugin_data['source'] = $wp_org_download;
                $plugin_data['type'] = 'free';
                $this->log_message("Plugin found on WordPress.org, using official source");
                return $this->handle_free_plugin($plugin_data);
            } else {
                // If not available on WordPress.org, use provided source
                $plugin_data['type'] = 'premium';
                return $this->handle_premium_plugin($plugin_data);
            }

        } catch (Exception $e) {
            $this->log_message("Error: " . $e->getMessage());
            return $this->error_response($e->getMessage());
        }
    }

    /**
     * Handle free plugin installation
     */
    private function handle_free_plugin($plugin_data) {
        if (!isset($plugin_data['source']) || empty($plugin_data['source'])) {
            throw new Exception('No valid download URL found for the plugin');
        }

        // Install the plugin
        $this->log_message("Installing free plugin from: " . $plugin_data['source']);
        $installed = $this->upgrader->install($plugin_data['source']);
        
        if (is_wp_error($installed)) {
            throw new Exception($installed->get_error_message());
        }

        // Get the destination folder from the upgrader
        $plugin_folder = $this->upgrader->plugin_info();
        if ($plugin_folder) {
            $plugin_data['path'] = $plugin_folder;
        }

        return $this->activate_plugin($plugin_data);
    }

    /**
     * Handle premium plugin installation
     */
    private function handle_premium_plugin($plugin_data) {
        if (!isset($plugin_data['source']) || empty($plugin_data['source'])) {
            throw new Exception('Premium plugin source URL is required');
        }

        $this->log_message("Installing premium plugin from: " . $plugin_data['source']);
        
        $installed = $this->upgrader->install($plugin_data['source']);
        
        if (is_wp_error($installed)) {
            throw new Exception('Installation failed: ' . $installed->get_error_message());
        }

        if (!$installed) {
            throw new Exception('Installation failed: Unknown error occurred');
        }

        // Get the destination folder from the upgrader
        $plugin_folder = $this->upgrader->plugin_info();
        if (!$plugin_folder) {
            throw new Exception('Could not determine plugin folder after installation');
        }

        $plugin_data['path'] = $plugin_folder;

        return $this->activate_plugin($plugin_data);
    }

    /**
     * Activate plugin
     */
    private function activate_plugin($plugin_data) {
        try {
            // Get the correct plugin path
            $plugin_path = $plugin_data['path'];
            
            $this->log_message("Attempting to activate plugin: " . $plugin_data['name'] . " with path: " . $plugin_path);
            
            // First, try the direct path
            if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_path)) {
                $this->log_message("Direct path exists: " . WP_PLUGIN_DIR . '/' . $plugin_path);
                
                if (is_plugin_active($plugin_path)) {
                    $this->log_message("Plugin is already active using direct path.");
                    return $this->success_response('Plugin is already active', 'active');
                } else {
                    $this->log_message("Plugin exists but is not active. Trying to activate with direct path.");
                    $direct_activation = activate_plugin($plugin_path);
                    
                    if (!is_wp_error($direct_activation)) {
                        $this->log_message("Plugin activated successfully with direct path.");
                        return $this->success_response('Plugin activated successfully', 'active');
                    } else {
                        $this->log_message("Failed to activate with direct path. Error: " . $direct_activation->get_error_message());
                        // We'll continue to try finding the main plugin file below
                    }
                }
            } else {
                $this->log_message("Direct path does not exist: " . WP_PLUGIN_DIR . '/' . $plugin_path);
            }
            
            // If direct path doesn't exist or plugin is not active, we need to find the main plugin file
            $plugin_dir = dirname($plugin_path);
            $plugin_dir_path = WP_PLUGIN_DIR . '/' . $plugin_dir;
            
            $this->log_message("Looking for plugin files in directory: " . $plugin_dir_path);
            
            if (!file_exists($plugin_dir_path)) {
                throw new Exception('Plugin directory not found: ' . $plugin_dir);
            }
            
            // Get all PHP files in the plugin directory
            $plugin_files = glob($plugin_dir_path . '/*.php');
            $this->log_message("Found " . count($plugin_files) . " PHP files in plugin directory");
            
            if (empty($plugin_files)) {
                throw new Exception('No PHP files found in plugin directory: ' . $plugin_dir);
            }
            
            // Check each PHP file for a valid plugin header
            $main_plugin_file = null;
            foreach ($plugin_files as $file) {
                $plugin_data_from_file = get_plugin_data($file, false, false);
                
                $this->log_message("Checking file: " . basename($file) . " - " . 
                    (empty($plugin_data_from_file['Name']) ? "No plugin name found" : "Plugin name: " . $plugin_data_from_file['Name']));
                
                // If this file has a Name in the header, it's likely the main plugin file
                if (!empty($plugin_data_from_file['Name'])) {
                    $main_plugin_file = $file;
                    break;
                }
            }
            
            // If we couldn't find a file with a valid header, fall back to the first PHP file
            if ($main_plugin_file === null && !empty($plugin_files)) {
                $main_plugin_file = $plugin_files[0];
                $this->log_message("Warning: No file with valid plugin header found. Using first PHP file: " . basename($main_plugin_file));
            } elseif ($main_plugin_file === null) {
                throw new Exception('No valid plugin file found in directory: ' . $plugin_dir);
            } else {
                $this->log_message("Found main plugin file: " . basename($main_plugin_file));
            }
            
            // Get the relative path for WordPress
            $relative_path = str_replace(WP_PLUGIN_DIR . '/', '', $main_plugin_file);
            $this->log_message("Using relative path for activation: " . $relative_path);
            
            // Check if already active
            if (is_plugin_active($relative_path)) {
                $this->log_message("Plugin is already active with path: " . $relative_path);
                return $this->success_response('Plugin is already active', 'active');
            }
            
            // Try to activate the plugin
            $this->log_message("Attempting to activate plugin with path: " . $relative_path);
            $result = activate_plugin($relative_path);
            
            if (is_wp_error($result)) {
                $error_message = $result->get_error_message();
                $this->log_message("Activation failed: " . $error_message);
                throw new Exception('Activation failed: ' . $error_message);
            }
            
            $this->log_message("Successfully activated plugin: " . $plugin_data['name'] . " (" . $relative_path . ")");
            return $this->success_response('Plugin activated successfully', 'active');
        } catch (Exception $e) {
            $this->log_message("Error in activate_plugin: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get WordPress.org plugin download URL
     */
    private function get_wordpress_plugin_download_url($slug) {
        $api = plugins_api('plugin_information', [
            'slug' => $slug,
            'fields' => [
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ],
        ]);

        if (is_wp_error($api)) {
            return false;
        }

        return $api->download_link;
    }

    /**
     * Validate plugin data
     */
    private function validate_plugin_data($plugin_data) {
        $required_fields = ['name', 'slug', 'path'];
        foreach ($required_fields as $field) {
            if (!isset($plugin_data[$field]) || empty($plugin_data[$field])) {
                throw new Exception("Missing required plugin field: {$field}");
            }
        }

        // Source is required if not a WordPress.org plugin
        if (!isset($plugin_data['source']) || empty($plugin_data['source'])) {
            $wp_org_download = $this->get_wordpress_plugin_download_url($plugin_data['slug']);
            if (!$wp_org_download) {
                throw new Exception('Plugin source URL is required for non-WordPress.org plugins');
            }
        }
    }

    /**
     * Success response
     */
    private function success_response($message, $status = 'success') {
        return [
            'success' => true,
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Error response
     */
    private function error_response($message) {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message
        ];
    }

    /**
     * Log message
     */
    private function log_message($message) {
        $this->log[] = date('Y-m-d H:i:s') . ' - ' . $message;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Template Kit Plugin Manager: ' . $message);
        }
    }

    /**
     * Get installation logs
     */
    public function get_logs() {
        return $this->log;
    }
}

// Ajax handlers
add_action('wp_ajax_mtl_install_and_activate_plugin', 'mtl_install_and_activate_plugin');
function mtl_install_and_activate_plugin() {
    global $wp_version;
    
    // Verify nonce
    if (!check_ajax_referer('mtl_plugin_installation_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    // Check user capabilities
    if (!current_user_can('install_plugins')) {
        wp_send_json_error(['message' => 'You do not have permission to install plugins']);
    }

    // Get plugin data
    $plugin_data = isset($_POST['plugin']) ? $_POST['plugin'] : null;
    if (!$plugin_data) {
        wp_send_json_error(['message' => 'No plugin data provided']);
    }

    // Debug log
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Attempting to install/activate plugin:');
        error_log(print_r($plugin_data, true));
        error_log('WordPress version: ' . $wp_version);
        error_log('PHP version: ' . PHP_VERSION);
    }

    // Sanitize plugin data
    $plugin_data = array_map('sanitize_text_field', $plugin_data);

    // Initialize plugin manager
    $plugin_manager = new Template_Kit_Plugin_Manager();

    // Install and activate plugin
    try {
        $result = $plugin_manager->install_and_activate_plugin($plugin_data);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            $error_data = array_merge($result, ['plugin_info' => $plugin_data]);
            wp_send_json_error($error_data);
        }
    } catch (Exception $e) {
        $error_data = [
            'success' => false,
            'status' => 'error',
            'message' => $e->getMessage(),
            'plugin_info' => $plugin_data
        ];
        wp_send_json_error($error_data);
    }
}

// Add status check handler
add_action('wp_ajax_mtl_check_plugin_status', 'mtl_check_plugin_status');
function mtl_check_plugin_status() {
    global $wp_version;
    
    // Verify nonce
    if (!check_ajax_referer('mtl_plugin_installation_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    // Check user capabilities
    if (!current_user_can('install_plugins')) {
        wp_send_json_error(['message' => 'You do not have permission to check plugin status']);
    }
    
    // Make sure the plugin.php file is loaded
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    if (!function_exists('is_plugin_active')) {
        error_log('Critical Error: is_plugin_active function not available');
        wp_send_json_error(['message' => 'WordPress plugin functions not available']);
        return;
    }

    // Log system information for debugging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('WP Version: ' . $wp_version);
        error_log('PHP Version: ' . PHP_VERSION);
        error_log('ABSPATH: ' . ABSPATH);
        error_log('WP_PLUGIN_DIR: ' . WP_PLUGIN_DIR);
        
        $all_active_plugins = get_option('active_plugins', []);
        error_log('All Active Plugins: ' . print_r($all_active_plugins, true));
    }

    // Get plugins data from request
    $plugins = isset($_POST['plugins']) ? $_POST['plugins'] : [];
    
    if (empty($plugins)) {
        wp_send_json_error(['message' => 'No plugins provided']);
    }

    $statuses = [];
    
    foreach ($plugins as $plugin) {
        $slug = sanitize_text_field($plugin['slug']);
        $path = sanitize_text_field($plugin['path']);
        
        // Check if plugin is installed
        $installed = file_exists(WP_PLUGIN_DIR . '/' . dirname($path));
        
        // Debug info
        $debug_info = array(
            'slug' => $slug,
            'path' => $path,
            'full_path' => WP_PLUGIN_DIR . '/' . $path,
            'dirname' => dirname($path),
            'dir_exists' => $installed
        );
        
        // Check if plugin is active - make sure we have a valid path
        $active = false;
        
        if ($installed) {
            // First try the direct path
            if (file_exists(WP_PLUGIN_DIR . '/' . $path)) {
                $active = is_plugin_active($path);
                
                // Also check for network activation if this is a multisite
                if (!$active && function_exists('is_plugin_active_for_network') && is_multisite()) {
                    $active = is_plugin_active_for_network($path);
                    $debug_info['network_active'] = $active;
                }
                
                $debug_info['direct_path_exists'] = true;
                $debug_info['direct_path_active'] = $active;
            } else {
                $debug_info['direct_path_exists'] = false;
                
                // If direct path doesn't exist, try to find the main plugin file
                $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($path);
                $plugin_files = glob($plugin_dir . '/*.php');
                
                if (!empty($plugin_files)) {
                    // Try each PHP file in the directory
                    foreach ($plugin_files as $plugin_file) {
                        $relative_path = str_replace(WP_PLUGIN_DIR . '/', '', $plugin_file);
                        $test_active = is_plugin_active($relative_path);
                        
                        // Also check for network activation
                        if (!$test_active && function_exists('is_plugin_active_for_network') && is_multisite()) {
                            $test_active = is_plugin_active_for_network($relative_path);
                            $debug_info['network_active_' . basename($plugin_file)] = $test_active;
                        }
                        
                        $debug_info['tested_path_' . basename($plugin_file)] = $test_active;
                        
                        if ($test_active) {
                            $active = true;
                            $path = $relative_path; // Update path to the correct one
                            break;
                        }
                    }
                }
            }
        }
        
        // Log the debug info
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Plugin Status Check - ' . $slug . ': ' . print_r($debug_info, true));
        }
        
        $statuses[$slug] = [
            'is_installed' => $installed,
            'is_active' => $active,
            'debug' => $debug_info
        ];
    }

    wp_send_json_success([
        'statuses' => $statuses
    ]);
}

// Enqueue necessary scripts
add_action('admin_enqueue_scripts', 'mtl_enqueue_plugin_scripts');
function mtl_enqueue_plugin_scripts() {
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'mtl_plugin_vars', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('mtl_plugin_installation_nonce'),
        'is_admin' => current_user_can('manage_options'),
    ]);
}