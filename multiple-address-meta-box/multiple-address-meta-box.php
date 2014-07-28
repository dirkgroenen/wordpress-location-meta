<?php
    /*
        Plugin Name: Multiple address meta box
        Description: Add multiple addresses (decoded to latlon) to a post
        Author: Dirk Groenen - TakeTwo Merkidentiteit
        Version: 1.0
    */

    class MultipleAddressMetaBox {

        public function __construct(){
            
            include(plugins_url( '/options/options.php' , __FILE__ ));
            
            add_action( 'add_meta_boxes', array($this, 'add_address_meta_box') );
            add_action( 'save_post', array($this, 'prfx_meta_save') );
            add_action( 'admin_enqueue_scripts', array($this, 'add_headfiles') );
            
        }
        
        /**
         * Add the JS to the head
         */
        public function add_headfiles() {
            wp_enqueue_script( 'multipleaddressmetabox-functions',  plugins_url( '/js/custom.js' , __FILE__ ), array( 'jquery' ));
            wp_enqueue_script('google-maps', '//maps.googleapis.com/maps/api/js?&sensor=false', array(), '3', true);
            wp_enqueue_style( 'multipleaddressmetabox-styles',  plugins_url( '/css/styles.css' , __FILE__ ));
        }

        /**
        * Adds a meta box to the post editing screen
        * Here you have to set the post type where you want it to display
        */
        function add_address_meta_box() {
            add_meta_box( 'prfx_meta_address', "Locaties", array($this, 'meta_callback'), 'post' );
        }
        
        /**
         * Outputs the content of the meta box
         */
        function meta_callback( $post ) {
            wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
            $prfx_stored_meta = get_post_meta( $post->ID );
            
            ?>
            <div class="fields">
                <label for="meta-text" class="prfx-row-title">Vul de adressen van alle locaties in:</label>
                <?php
                if(isset($prfx_stored_meta['latlon']) && isset($prfx_stored_meta['addresses'][0])){
                    $latlons = unserialize($prfx_stored_meta['latlon'][0]);
                    $addresses = unserialize($prfx_stored_meta['addresses'][0]);
                    
                    for($x = 0; $x < count($latlons); $x++){
                    ?>
                        <div class="address_meta_box_row">
                            <div class="humaninput">
                                <input type="text" name="addresses[]" class="address" autocomplete="off" style="width: 100%" value="<?php echo $addresses[$x]; ?>" />
                                <div class="suggestions">
                                    <ul></ul>
                                </div>
                            </div>
                            <div class="close"></div>
                            <input type="hidden" name="latlon[]" class="latlon" value="<?php echo $latlons[$x]; ?>" />
                            <div style="clear:both"></div>
                        </div>
                    <?php
                    }                    
                }
                else{
                ?>
                    <div class="address_meta_box_row">
                        <div class="humaninput">
                            <input type="text" name="addresses[]" class="address" autocomplete="off" style="width: 100%" value="" />
                            <div class="suggestions">
                                <ul></ul>
                            </div>
                        </div>
                        <div class="close"></div>
                        <input type="hidden" name="latlon[]" class="latlon" value="" />
                        <div style="clear:both"></div>
                    </div>
                <?php
                }
                ?>
                
            </div>
            <div class="addone">
                <a href="#" class="add">Extra locatie toevoegen</a>
            </div>
            <?php
        }
        
        /**
        * Saves the custom meta input
        */
        function prfx_meta_save( $post_id ) {

            // Checks save status
            $is_autosave = wp_is_post_autosave( $post_id );
            $is_revision = wp_is_post_revision( $post_id );
            $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

            // Exits script depending on save status
            if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
                return;
            }

            // Checks for input and sanitizes/saves if needed
            if(isset($_POST['addresses'])){
                update_post_meta( $post_id, 'addresses', $_POST['addresses']);
            }
            
            // Checks for input and sanitizes/saves if needed
            if(isset($_POST['latlon'])){
                update_post_meta( $post_id, 'latlon', $_POST['latlon']);
            }

        }
        
    }

    $multipleaddressmetabox = new MultipleAddressMetaBox();

?>