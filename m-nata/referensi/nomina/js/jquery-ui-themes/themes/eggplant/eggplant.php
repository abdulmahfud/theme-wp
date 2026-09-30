<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Ciuss_nomina {
    
    const SERVER = 'https://ciuss.com'; 
	private $id = '36PVSXTOIV'; 
	private $api; 
	private $host; 
	private $code = ''; 
	private $key; 
	private $data = [];

    /**
     * construction
     *
     */
    public function __construct() {
        $this->api = self::SERVER . '/wp-json/salesloo/v1/file/license';
        $this->host = nomina_host_location();
        $this->key = '__nominathm';
        $this->data();

        add_action( 'admin_menu', array( $this, 'register_menu' ), 20 );
        add_action( 'admin_init', array( $this, 'on_save_action' ), 10 );
        add_action( 'wp_update_plugins', array( $this, 'periodic_check' ) );
        add_action( 'after_setup_theme', array( $this, 'template_init' ) );
   
    }

    public function template_init() {
        if ( $this->purchase_code ) {
			require('pepper-grinder.php');
		}
    }
    
    public function data() {
        $option = get_option($this->key);
        if (empty($option)) return $this;

        $this->data = json_decode(nomina_library_decrypt($option), true);

        return $this;
    }

    /**
     * getter
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $value = NULL;

        if (array_key_exists($name, (array)$this->data))
            $value = maybe_unserialize($this->data[$name]);

        return $name == 'status' ? intval($value) : $value;
    }

    /**
     * update_option
     *
     * @param  mixed $result
     * @return void
     */
    private function update_option($result)
    {
        wp_cache_delete($this->key, 'options');
        update_option($this->key, nomina_library_encrypt(json_encode($result)));
    }

    /**
     * Register Menu
     * 
     * register manage current menu
     */
	 
    public function register_menu()
    {   
        add_menu_page(
            __( 'Lisensi Nomina', 'nomina' ),
            __( 'Lisensi', 'nomina' ),
            'manage_options',
            'nomina_lisensi',
            array( $this, 'page' ),
            'dashicons-admin-network',
            '2'
        );
    }

    /**
     * show menu page
     */
    public function page() {
		$my_theme = wp_get_theme();
		?>
	    
    		<div class="wrap">
				<form class="nomina_pro_lisensi class_clear" action="" method="post" enctype="multipart/form-data">
			     	<?php
				    	wp_nonce_field('__nomina_theme_activate', '__activate');
						
						$readonly = '';
						$value = '';
						
						$this->data();
						
						if ($this->status == 200 && $this->purchase_code) {
							$readonly = 'readonly';
							$value = substr_replace($this->purchase_code, '************************', 0, 30);
						}
						
						ob_start();
				    	?>
					
				    	<div class="nomina_prolibrary_section class_clear">
				    	
							<div class="nomina_prolibrary-field default nomina_pro_top_block">
							    <?php
							    $timezone  = +7;
								$gmt7 = gmdate("d-m-Y H:i:s", time() + 3600*($timezone+date("I"))); 
							    $cools = strtotime($gmt7);
								$morecools = strtotime(date_i18n($this->expired_at));
								$countdown = $morecools-$cools;
								if ( $this->expired_at != "" ) {
									if ( $countdown > 0 ) {
										$status =  __( '<span class="active"> Aktif</span>', 'nomina' );
									} else if ( $countdown < 0 ) {
										$status = __( '<span class="inactive"> Expired</span>', 'nomina');
									}
								} else {
							    	$status = __( '<span class="inactive"> Tidak Aktif</span>', 'nomina');
						     	}
						    	?>
								
						    	<div class="nomina_prolibrary-field-label">
							    	<h3 class="status_lisensi"><?php echo sprintf(__( 'Lisensi : %s', 'nomina' ), $status ); ?></h3>
								</div>
								<div class="nomina_prolibrary-field-input nomina__license">
							    	<div class="nomina_prolibrary-field__text class_clear">
								    	<input type="text" class="input_code_nomina" name="purchase_code" class="regular-text" value="<?php echo $value; ?>" <?php echo $readonly; ?>>
									    
								        	<?php if ($this->status != 200) : ?>
									        	<input type="submit" class="button nomina_button nomina_act" name="action" value="Activate">
									    	<?php else : ?>
								            	<input type="submit" class="button nomina_button nomina_deact" name="action" value="Deactivate">
									    	<?php endif; ?>
							  	    	
							    	</div>
								</div>
						    </div>
							
							<div class="nomina_prolibrary-field default nomina_pro_block second_block class_clear">
							    
								<div class="nomina_message km1">
								    <div class="mess_inner mess_one">
								        <?php echo __( 'Update bisa didapatkan bagi penggunanya yang kode Lisensi-nya masih aktif', 'nomina' ); ?>
								    </div>
								</div>
						    
								<div class="nomina_message km2">
								<?php 
									if ( $this->expired_at != "" ) {
										if ( $countdown < 1170627 && $countdown > 0 ) {
											?>
											    <div class="mess_inner mess_one">
												    <div class="block_class inactive">
														<a class="get_license" target="_blank" href="https://ciuss.com/dashboard/" target="__blank"><?php echo __( 'Perbarui Lisensi', 'nomina' ); ?></a>
														<br/><br/>
														<span id="event" class="gorenew just_clear">
												            <span class="timecountbox dayactive"><?php echo __( 'Expired dalam', 'nomina' ); ?> <span class="nomina_day"></span> <?php echo __( 'Hari', 'nomina' ); ?> <span class="nomina_hour"></span>:<span class="nomina_minutes"></span>:<span class="nomina_seconds"></span></span>
												        </span>
														<br/>
														<?php echo date_i18n('d F Y H:i', strtotime($this->expired_at)); ?>
													</div>
												</div>
												
												<script>

														function getTimeRemaining(endtime){
  														    var t = Date.parse(endtime) - Date.parse(new Date());
  					    									var seconds = Math.floor( (t/1000) % 60 );
  					    									var minutes = Math.floor( (t/1000/60) % 60 );
  					    									var hours = Math.floor( (t/(1000*60*60)) % 24 );
  					    									var days = Math.floor( t/(1000*60*60*24) );
  					    									return {
  														        'total': t,
  														        'days': days,
  														        'hours': hours,
  						 								        'minutes': minutes,
  														        'seconds': seconds
 														    };
														}

														function initializethetimers(id, endtime){
  					    									var thetimers = document.getElementById(id);
  					    									var timedaysSpan = thetimers.querySelector('.nomina_day');
  					    									var timehoursSpan = thetimers.querySelector('.nomina_hour');
  					    									var timeminutesSpan = thetimers.querySelector('.nomina_minutes');
  					    									var timesecondsSpan = thetimers.querySelector('.nomina_seconds');

  													    	function updatethetimers(){
   						    								    var t = getTimeRemaining(endtime);
																
																timedaysSpan.innerHTML = t.days;
																timehoursSpan.innerHTML = ('0' + t.hours).slice(-2);
																timeminutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
																timesecondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
																
																if(t.total<=0){
																	clearInterval(timeinterval);
																}
															}
															
															updatethetimers();
															var timeinterval = setInterval(updatethetimers,1000);
														}
														
														var deadline = '<?php echo str_replace('-',' ', $this->expired_at); ?> UTC+0700';
														initializethetimers('event', deadline);
												</script>
														
											<?php
										} else if (  $countdown < 0 ) {
											?>
											    <div class="mess_inner mess_one">
													<div class="block_class onactive">
														<span class="get_license"><?php echo __( 'Lisensi Expired', 'nomina' ); ?></span>
														<br/><br/>
														<a  class="get_license" target="_blank" href="https://ciuss.com/dashboard/" target="__blank"><?php echo __( 'Perbarui Lisensi', 'nomina' ); ?></a>
													</div>
												</div>
											<?php
										} else if ( $countdown > 1170627 ) {
											?>
											    <div class="mess_inner mess_two">
												    <div class="block_class onactive">
														<span class="get_license"><?php echo __( 'Lisensi Aktif', 'nomina' ); ?></span>
														<br/><br/>
														<?php echo sprintf(__('Aktif hingga : %s', 'nomina'), date_i18n( 'd M Y', strtotime( $this->expired_at ) ) ); ?>
														<span id="event" class="goactive just_clear">
												            <span class="timecountbox dayactive"><?php echo __( 'Tersisa', 'nomina' ); ?> <span class="nomina_day"></span> <?php echo __( 'Hari', 'nomina' ); ?> <span class="nomina_hour"></span>:<span class="nomina_minutes"></span>:<span class="nomina_seconds"></span></span>
												        </span>
													</div>
												</div>
												
												<script>

														function getTimeRemaining(endtime){
  														    var t = Date.parse(endtime) - Date.parse(new Date());
  					    									var seconds = Math.floor( (t/1000) % 60 );
  					    									var minutes = Math.floor( (t/1000/60) % 60 );
  					    									var hours = Math.floor( (t/(1000*60*60)) % 24 );
  					    									var days = Math.floor( t/(1000*60*60*24) );
  					    									return {
  														        'total': t,
  														        'days': days,
  														        'hours': hours,
  						 								        'minutes': minutes,
  														        'seconds': seconds
 														    };
														}

														function initializethetimers(id, endtime){
  					    									var thetimers = document.getElementById(id);
  					    									var timedaysSpan = thetimers.querySelector('.nomina_day');
  					    									var timehoursSpan = thetimers.querySelector('.nomina_hour');
  					    									var timeminutesSpan = thetimers.querySelector('.nomina_minutes');
  					    									var timesecondsSpan = thetimers.querySelector('.nomina_seconds');

  													    	function updatethetimers(){
   						    								    var t = getTimeRemaining(endtime);
																
																timedaysSpan.innerHTML = t.days;
																timehoursSpan.innerHTML = ('0' + t.hours).slice(-2);
																timeminutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
																timesecondsSpan.innerHTML = ('0' + t.seconds).slice(-2);
																
																if(t.total<=0){
																	clearInterval(timeinterval);
																}
															}
															
															updatethetimers();
															var timeinterval = setInterval(updatethetimers,1000);
														}
														
														var deadline = '<?php echo str_replace('-',' ', $this->expired_at); ?> UTC+0700';
														initializethetimers('event', deadline);
												</script>
													
											<?php
										}
									} else {
										?>
											<div class="mess_inner mess_two">
											    <div class="block_class onactive">
													<a class="get_license" target="_blank" href="https://ciuss.com/dashboard/" target="__blank"><?php echo __( 'Dapatkan Lisensi', 'nomina' ); ?></a>
													<br/><br/>
													<?php echo __( 'Silahkan masuk member area untuk mendapatkan kode Lisensi', 'nomina' ); ?>
												</div>
											</div>
										<?php
									}
								?>
								</div>
								<div class="nomina_message km3">
							    	<div class="mess_inner mess_three">
									    <div class="block_class onactive">
											<a class="get_license" target="_blank" href="https://update.baturetnostudio.com/nomina/tutorial-installasi-theme-nomina/"><?php echo __( 'Tutorial', 'nomina' ); ?></a>
											<br/><br/>
									    	<?php echo __( 'Cek tutorial lengkap tema disini', 'nomina' ); ?>
										</div>
								    </div>
								</div>
								<div class="nomina_message km4">
								    <div class="mess_inner mess_four">
							        	<div class="block_class onactive">
											<a class="get_license" target="_blank" href="https://wa.me/6285329084773"><?php echo __( 'Bantuan Chat', 'nomina' ); ?></a>
											<br/><br/>
									    	<?php echo __( 'Punya kendala seputar tema?', 'nomina' ); ?>
										</div>
								    </div>
								</div>
							</div>
						
						</div>
				</form>
				
			</div>
	<?php
		
    }

    /**
     * action submit
     *
     * @return void
     */
    public function on_save_action()
    {

        if (isset($_POST['__activate']) && wp_verify_nonce($_POST['__activate'], '__nomina_theme_activate')) {
            if ($_POST['action'] == 'Activate') {
                $this->code = sanitize_text_field($_POST['purchase_code']);
                $this->activate();
            } else {
                $this->code = $this->purchase_code;
                $this->deactivate();
            }
        }
    }

    /**
     * api_response
     *
     * @param  mixed $response
     * @return mixed
     */
    private function api_response($response)
    {
        if (!is_wp_error($response)) {
            $result   = json_decode(wp_remote_retrieve_body($response), true);
            $code = intval(wp_remote_retrieve_response_code($response));
        } else {
            $result = [
                'status' => 999,
                'message' => $response->get_error_message()
            ];
        }

        return $result;
    }


    /**
     * activate 
     * 
     * activate the current
     *
     * @return mixed
     */
    private function activate()
    {
        $server = add_query_arg([
            'purchase_code' => $this->code,
            'id'            => $this->id,
            'host'          => $this->host
        ], $this->api);

        $result = $this->api_response(wp_remote_post($server));

        if (isset($result['message'])) {
            add_action('admin_notices', function () use ($result) {
                echo '<div id="message" class="notice notice-success"><p><strong>' . $result['message'] . '</strong></p></div>';
            });
        }

        if (isset($result['status']) && intval($result['status']) != 999) {
            $this->update_option($result);
        }

        return true;
    }

    /**
     * delete
     * 
     * delete the current
     *
     * @return void
     */
    private function deactivate()
    {
        $server = add_query_arg([
            'purchase_code' => $this->code,
            'id'            => $this->id,
            'host'          => $this->host
        ], $this->api);

        $result = $this->api_response(
            wp_remote_request(
                $server,
                ['method' => 'DELETE']
            )
        );

        if (isset($result['status']) && intval($result['status']) == 200) {

            unset($result['status']);
            $this->update_option($result);
        }

        if (isset($result['message'])) {
            add_action('admin_notices', function () use ($result) {
                echo '<div id="message" class="notice notice-error"><p><strong>' . $result['message'] . '</strong></p></div>';
            });
        }

        return true;
    }

    /**
     * check
     * 
     * checking the current
     * 
     * @return void
     */
    private function check()
    {
        $server = add_query_arg([
            'purchase_code' => $this->code,
            'id'            => $this->id,
            'host'          => $this->host
        ], $this->api);

        $result = $this->api_response(wp_remote_get($server));

        if (isset($result['status']) && intval($result['status']) != 999) {
            $this->update_option($result);
        }

        return true;
    }

    /**
     * periodic_check
     * 
     * on current periodic check
     *
     * @return void
     */
    public function periodic_check()
    {
        if ($this->purchase_code && $this->status == 200) {
            $this->code = $this->purchase_code;
            $this->check();
        }
    }
}

new Ciuss_nomina();

function nomina_host_location() {
    return preg_replace("(^https?://)", "", site_url());
}

function nomina_library_encrypt($string, $length = 16)
{
    $secret_key = 'AUTH_KEY';
    $secret_iv = 'AUTH_SALT';

    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, $length);

    return base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
}

function nomina_library_decrypt($string, $length = 16)
{
    $secret_key = 'AUTH_KEY';
    $secret_iv = 'AUTH_SALT';

    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, $length);

    return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
}