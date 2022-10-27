/* This File is part of Camila PHP Framework
   Copyright (C) 2006-2022 Umberto Bresciani

   Camila PHP Framework is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Camila PHP Framework is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Camila PHP Framework; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


global $HTTP_SERVER_VARS ;
$ip = $HTTP_SERVER_VARS['REMOTE_ADDR'] ;
$accessfile = CAMILA_TEMP_RWDIR . "/".$_CAMILA["user_id"]."-".$_CAMILA["page_url"].".txt";
$accesses = array() ;
$now = $expire = time() ;

// read from accessfile
$fp = @fopen( $accessfile , "r" ) ;
if( $fp !== false ) {
	$expire = intval( fgets( $fp , 65536 ) ) ;
	if( $expire >= $now ) {
		$accesses = unserialize( fgets( $fp , 65536 ) ) ;
		if( ! is_array( $accesses ) ) $accesses = array() ;
	} else {
		$expire = $now ;
		$stage = 0 ;
	}
	//echo ":".$expire."-".$accesses[ $ip ];
	fclose( $fp ) ;
}

if( empty( $accesses[ $ip ] ) )
    $accesses[ $ip ] = 1;
else $accesses[ $ip ] ++ ;

if( $expire <= $now ) $expire = $now + CAMILA_BAN_IP_SECS;

if( $accesses[ $ip ] >= $_CAMILA["page_dos_threshold"] ) {
	$die_flag = true;
}

$fp = @fopen( $accessfile , "w" );
@flock( $fp , LOCK_EX );
@fwrite( $fp , $expire."\n" ) ;
@fwrite( $fp , serialize( $accesses ) . "\n" );
@flock( $fp , LOCK_UN );
@fclose( $fp );

if( $die_flag ) print_error_page("Per motivi di sicurezza è necessario ritentare l'accesso a questa pagina fra ".CAMILA_BAN_IP_SECS." secondi. Grazie.");
