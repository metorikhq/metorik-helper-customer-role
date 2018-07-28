<?php
/**
 * Plugin Name: Metorik Helper - Customer Role
 * Plugin URI: https://metorik.com
 * Description: Adds customer role to WooCommerce 2.6 Customers API.
 * Version: 1.0.0
 * Author: Metorik
 * Author URI: https://metorik.com
 * WC requires at least: 2.6.0
 * WC tested up to: 3.4.0.
 */
class Metorik_Helper_Customer_Role
{
    /**
     * Current version of Metorik.
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Main Metorik Helper Instance.
     *
     * Ensures only one instance of the Metorik Helper is loaded or can be loaded.
     *
     * @return Metorik Helper - Main instance.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Start plugin.
     */
    public function init()
    {
        if (class_exists('WooCommerce')) {
            // Filter customer API response
            add_filter('woocommerce_rest_prepare_customer', array($this, 'filter_customer_object'), 10, 2);
        }
    }

    public function filter_customer_object($response, $customer)
    {
        // if customer
        if ($customer) {
            // response data
            $data = $response->get_data();

            // roles meta
            $user_meta = get_userdata($customer->ID);
            $roles = $user_meta->roles;

            // if role set and no role in data already
            if (! isset($data['role']) && isset($roles[0])) {
                $data['role'] = $roles[0];
                $response->set_data($data);
            }
        }

        return $response;
    }
}

/**
 * For plugin-wide access to initial instance.
 */
function Metorik_Helper_Customer_Role()
{
    return Metorik_Helper_Customer_Role::instance();
}

Metorik_Helper_Customer_Role();
