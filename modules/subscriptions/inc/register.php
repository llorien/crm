<?php

$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

            $action = $_POST['action'];
            if( isset($_POST['action']) && $action=='reg' ) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $email = $_POST['email'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $org = $_POST['org'];

                if( !$username ) {
                    return_err_msg('Empty username!');
                } 
                if ( !$password ) {
                    return_err_msg('Empty password!');
                } 
                if ( !$email ) {
                    return_err_msg('Empty email address!');
                }
                if ( !$first_name ) {
                    return_err_msg('First name required!');
                }
                if ( !$last_name ) {
                    return_err_msg('Last name required!');
                }
                if ( !$org ) {
                    return_err_msg('Organization required!');
                }

                $userdata = array(
                    'user_login'  =>  $username,
                    'user_pass'   =>  $password,
                    'user_email'  =>  $email,
                    'display_name'=>  "$first_name $last_name",
                    'nickname'    =>  "$first_name $last_name",
                    'first_name'  =>  $first_name,
                    'last_name'   =>  $last_name
                );
                $user_id = wp_insert_user( $userdata );

                if( !is_wp_error( $user_id ) ) {
                    add_user_meta( $user_id, 'organization', $org );

                    $site_id = wpmu_create_blog( get_clean_basedomain().'/', 
                        $username, "$first_name $last_name", $user_id);

                    if( !is_wp_error( $site_id ) ) {
                        echo json_encode(
                            array(
                                'user_id' => $user_id,
                                'site_id' => $site_id
                        ));
                    } else {
                        return_err_msg( $site_id.get_error_message( $site_id.get_error_code() ) );
                    }
                } else {
                    return_err_msg( $user_id.get_error_message( $user_id.get_error_code() ) );
                }
            }

        function return_err_msg ($msg) {
            die("{'message':'$msg'}");
        };

        function get_clean_basedomain() {
            $domain = preg_replace( '|https?://|', '', site_url() );
            if ( $slash = strpos( $domain, '/' ) ) {
                $domain = substr( $domain, 0, $slash );
            }
            return $domain;
        }    

