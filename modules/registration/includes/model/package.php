<?php

birch_ns( 'brithoncrm.registration', function( $ns ) {

        global $brithoncrm;

        $ns->register_account = function() use ( $ns ) {

            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $org = $_POST['org'];

            if ( ! $username ) {
                $ns->return_err_msg( 'Empty username!' );
            }
            if ( ! $password ) {
                $ns->return_err_msg( 'Empty password!' );
            }
            if ( ! $email ) {
                $ns->return_err_msg( 'Empty email address!' );
            }
            if ( ! $first_name ) {
                $ns->return_err_msg( 'First name required!' );
            }
            if ( ! $last_name ) {
                $ns->return_err_msg( 'Last name required!' );
            }
            if ( ! $org ) {
                $ns->return_err_msg( 'Organization required!' );
            }

            $userdata = array(
                'user_login' => $username,
                'user_pass' => $password,
                'user_email' => $email,
                'display_name' => "$first_name $last_name",
                'nickname' => "$first_name $last_name",
                'first_name' => $first_name,
                'last_name' => $last_name,
            );

            $user_id = wp_insert_user( $userdata );
            $subdir = $ns->generate_blog_dir( $first_name, $last_name );

            if ( ! is_wp_error( $user_id ) ) {
                add_user_meta( $user_id, 'organization', $org );
                $site_id = wpmu_create_blog( $ns->get_clean_basedomain(),
                    $subdir, "$first_name $last_name", $user_id );

                if ( ! is_wp_error( $site_id ) ) {
                    $creds = array();
                    $creds['user_login'] = $username;
                    $creds['user_password'] = $password;
                    $creds['remember'] = true;
                    $usr = wp_signon( $creds, false );

                    die( json_encode(
                            array(
                                'user_id' => $user_id,
                                'site_id' => $site_id,
                                'site_dir' => $subdir,
                            ) ) );
                } else {
                    $ns->return_err_msg( $site_id->get_error_message( $site_id->get_error_code() ) );
                }
            } else {
                $ns->return_err_msg( $user_id->get_error_message( $user_id->get_error_code() ) );
            }
        };

        $ns->return_err_msg = function( $msg, $error = 'Error' ) use ( $ns ) {
            die( json_encode( array(
                        'error' => $error,
                        'message' => $msg,
                    ) ) );
        };

        $ns->return_result = function( $succeed, $data ) use ( $ns ) {
            return array(
                'succeed' => $succeed,
                'data' => $data,
            );
        };

        $ns->get_clean_basedomain = function() use ( $ns ) {
            $domain = preg_replace( '|https?://|', '', site_url() );
            if ( $slash = strpos( $domain, '/' ) ) {
                $domain = substr( $domain, 0, $slash );
            }
            return $domain;
        };

        $ns->generate_blog_dir = function( $first_name, $last_name ) use ( $ns ) {
            return '/'.$first_name.'_'.$last_name.rand();
        };

    } );
