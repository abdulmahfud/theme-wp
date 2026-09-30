<?php
function kabar_pre_set_transient_update_theme ( $transient ) {
	
		if( empty( $transient->checked['nomina'] ) )
			return $transient;
		$checking = curl_init();
		curl_setopt($checking, CURLOPT_URL, 'https://baturetnostudio.com/new/nomina/jquery-ux.json' );
		// 3 second timeout to avoid issue on the server
		curl_setopt($checking, CURLOPT_TIMEOUT, 3 );
		curl_setopt($checking, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($checking);
		curl_close($checking);
		// make sure that we received the data in the response is not empty
		if( empty( $result ) )
			return $transient;
		//check server version against current installed version
		if( $data = json_decode( $result ) ){
			if( version_compare( $transient->checked['nomina'], $data->new_version, '<' ) )
				$transient->response['nomina'] = (array) $data;
		}
		return $transient;
	
}
add_filter ( 'pre_set_site_transient_update_themes', 'kabar_pre_set_transient_update_theme' );